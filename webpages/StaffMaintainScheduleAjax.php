<?php
// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Peter Olszowka on 2024-Mar-08
require_once('StaffCommonCode.php');
$requestInputRaw = file_get_contents('php://input');
$requestInput = json_decode($requestInputRaw, true);
if (!$ajax_request_action = $requestInput['ajax_request_action']) {
    exit();
}
switch ($ajax_request_action) {
    case 'retrieveSessions':
        retrieveSessions($requestInput);
        break;
    default:
        exit();
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
WITH TL AS (
    SELECT
            SHT.sessionid, GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
        FROM
                 SessionHasTag SHT
            JOIN Tags TA USING (tagid)
        GROUP BY
            SHT.sessionid
    )
SELECT
        S.sessionid, S.title, TR.trackname, TY.typename, D.divisionname, S.duration, TL.taglist
    FROM
             Sessions S
        JOIN Tracks TR USING (trackid)
        JOIN Types TY USING (typeid)
        JOIN Divisions D USING (divisionid)
        JOIN TL USING (sessionid)
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
    while ($row = mysqli_fetch_assoc($result)) {
        $returnValue[] = $row;
    }
    header("Content-Type: application/json");
    echo json_encode($returnValue);
    }
