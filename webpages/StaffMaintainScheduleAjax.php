<?php
// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
require_once('StaffCommonCode.php');
$requestInputRaw = file_get_contents('php://input');
$requestInput = json_decode($requestInputRaw, true);
if (!$ajax_request_action = $requestInput['ajax_request_action']) {
    http_response_code(400); // bad request
    exit();
}
switch ($ajax_request_action) {
    case 'retrieveSessionInfo':
        retrieveSessionInfo($requestInput);
        break;
    case 'retrieveSessions':
        retrieveSessions($requestInput);
        break;
    default:
        http_response_code(400); // bad request
        exit();
}

function retrieveSessionInfo($requestInput): void {
    $ConStartDatim = CON_START_DATIM;
    $sessionid = $requestInput['sessionid'];
    $query = <<<EOD
SELECT
        S.title, S.progguiddesc AS description, S.sessionid, TR.trackname AS trackName, TY.typename AS typeName,
        DI.divisionname AS divisionName, TIME_TO_SEC(S.duration) / 60 AS duration, S.notesforprog AS notesForProgramming,
        R.roomname AS scheduledRoom,
        ADDTIME('$ConStartDatim',SCH.starttime) AS scheduledStart
    FROM
                  Sessions S
             JOIN Tracks TR USING (trackid)
             JOIN Types TY USING (typeid)
             JOIN Divisions DI USING (divisionid)
        LEFT JOIN Schedule SCH USING (sessionid)   
        LEFT JOIN Rooms R USING (roomid)
    WHERE
        S.sessionid = ?;
EOD;
    $result = mysqli_query_with_prepare_and_error_handling($query, 'i', array($sessionid), true, true);
    $returnValue = mysqli_fetch_assoc($result);
    if ($returnValue['scheduledStart']) {
        $date = new DateTimeImmutable($returnValue['scheduledStart']);
        $returnValue['scheduledStart'] = $date->format('c');
    }
    mysqli_free_result($result);
    $query = <<<EOD
SELECT
        TA.tagname
    FROM
             SessionHasTag SHT
        JOIN Tags TA USING (tagid)
    WHERE
        SHT.sessionid = ?
    ORDER BY
        TA.tagname;
EOD;
    $result = mysqli_query_with_prepare_and_error_handling($query, 'i', array($sessionid), true, true);
    $tagNameList = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $tagNameList[] = $row['tagname'];
    }
    mysqli_free_result($result);
    $returnValue['tagNameList'] = $tagNameList;
    $query = <<<EOD
SELECT
        P.badgeid, P.pubsname AS pubsName, CONCAT(CD.firstname, ' ', CD.lastname) AS name, POS.moderator
    FROM
             ParticipantOnSession POS
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
    WHERE
        POS.sessionid = ?;
EOD;
    $result = mysqli_query_with_prepare_and_error_handling($query, 'i', array($sessionid), true, true);
    $participants = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $moderator = $row['moderator'];
        unset($row['moderator']);
        if ($moderator) {
            $returnValue['moderator'] = $row;
        }
        $participants[] = $row;
    }
    $returnValue['participants'] = $participants;
    header("Content-Type: application/json");
    echo json_encode($returnValue);
}

function retrieveSessions($requestInput): void {
    if (isset($requestInput['currSessionIds'])) {
        $currSessionIds = sanitizeArrayOfInts($requestInput['currSessionIds']);
    } else {
        $currSessionIds = array();
    }
    $trackId = isset($requestInput['track']) ? $requestInput['track'] : null;
    if (isset($requestInput['tags'])) {
        $tagIds = sanitizeArrayOfInts($requestInput['tags']);
    } else {
        $tagIds = array();
    }
    $tagMatchAny = isset($requestInput['matchAny']);
    $tagMatchAll = isset($requestInput['matchAll']);
    $typeId = isset($requestInput['type']) ? $requestInput['type'] : null;
    $divisionId = isset($requestInput['division']) ? $requestInput['division'] : null;
    $sessionId = isset($requestInput['sessionId']) ? $requestInput['sessionId'] : null;
    $title = isset($requestInput['title']) ? mb_strtolower($requestInput['title']) : null;
    $personsAssigned = isset($requestInput['personAssigned']);
    $query = <<<EOD
SELECT
        S.sessionid, S.title, TR.trackname AS trackName, TY.typename AS typeName, D.divisionname AS divisionName,
        S.duration
    FROM
             Sessions S
        JOIN Tracks TR USING (trackid)
        JOIN Types TY USING (typeid)
        JOIN Divisions D USING (divisionid)
    WHERE
            S.statusid IN (2,3,7)
        AND NOT EXISTS (
            SELECT * FROM Schedule SCH WHERE S.sessionid = SCH.sessionid
        )

EOD;
    $paramArray = array();
    $typeString = '';
    if ($trackId) {
        $query .= "        AND trackid = ? \n";
        $paramArray[] = $trackId;
        $typeString .= 'i';
    }
    if ($typeId) {
        $query .= "        AND typeid = ? \n";
        $paramArray[] = $typeId;
        $typeString .= 'i';
    }
    if ($divisionId) {
        $query .= "        AND divisionid = ? \n";
        $paramArray[] = $divisionId;
        $typeString .= 'i';
    }
    if ($sessionId) {
        $query .= "        AND sessionid = ? \n";
        $paramArray[] = $sessionId;
        $typeString .= 'i';
    }
    if ($title) {
        $query .= "        AND LOWER(title) LIKE ? \n";
        $paramArray[] = '%' . mb_strtolower($title) . '%';
        $typeString .= 's';
    }
    if (count($currSessionIds) > 0) {
        $currSessionIdList = implode(",", $currSessionIds);
        $query .= "        AND sessionid NOT IN ($currSessionIdList)\n";
    }
    if (count($tagIds) > 0) {
        if ($tagMatchAll) {
            foreach ($tagIds as $tag) {
                $query .= "        AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S.sessionid AND tagid = $tag)\n";
            }
        } else {
            $tagidList = implode(',', $tagIds);
            $query .= "        AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S.sessionid AND tagid IN ($tagidList))\n";
        }
    }
    if ($personsAssigned) {
        $query .= "        AND EXISTS (SELECT * FROM ParticipantOnSession WHERE sessionid = S.sessionid)\n";
    }
    $result = mysqli_query_with_prepare_and_error_handling($query, $typeString, $paramArray, true, true);
    $returnValue = array();
    $sessionids = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $returnValue[] = $row;
        $sessionids[] = $row['sessionid'];
    }
    mysqli_free_result($result);
    if (count($sessionids) > 0) {
        $sessionIdList = implode(',', $sessionids);
        $query = <<<EOD
SELECT
        SHT.sessionid, TA.tagname
    FROM
             SessionHasTag SHT
        JOIN Tags TA USING (tagid)
    WHERE
        SHT.sessionid IN ($sessionIdList)
EOD;
        $result = mysqli_query_with_error_handling($query, true, true);
        $sessionTagsArr = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $sessionId = $row['sessionid'];
            if (!isset($sessionTagsArr[$sessionId])) {
                $sessionTagsArr[$sessionId] = array();
            }
            $sessionTagsArr[$sessionId][] = $row['tagname'];
        }
        foreach ($returnValue as &$session) {
            $sessionId = $session['sessionid'];
            if (isset($sessionTagsArr[$sessionId])) {
                $session['tagNameArray'] = $sessionTagsArr[$sessionId];
            }
        }
    }
    header("Content-Type: application/json");
    echo json_encode($returnValue);
}
