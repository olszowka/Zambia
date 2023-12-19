<?php
// Copyright (c) 2011-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
// This function will output the page with the form to add or create a session
// Variables
//     action: "create" or "edit"
//     session: array with all data of record to edit or defaults for create
//     messageWarning: a string to display before the form in warning format
//     messageSuccess: a string to display before the form in success format
//     messageFatal: a string to display instead of the form in warning format
function RenderEditCreateSession ($action, $session, $messageWarning, $messageFatal, $messageSuccess = '') {
    global $name, $email, $message_error, $title;
    require_once('StaffCommonCode.php');
    if ($action === "create") {
        $title = "Create New Session";
    } elseif ($action === "edit") {
        $title = "Edit Session";
    } else {
        exit();
    }
    staff_header($title, true);
    if ($messageFatal == '') {
        $queryArr = array();
        $queryArr['divisions'] =<<<EOD
SELECT
        divisionid, divisionname
    FROM
        Divisions
    ORDER BY
        display_order;
EOD;
        $queryArr['tracks'] =<<<EOD
SELECT
        trackid, trackname
    FROM
        Tracks
    ORDER BY
        display_order;
EOD;
        $queryArr['types'] =<<<EOD
SELECT
        typeid, typename
    FROM
        Types
    ORDER BY
        display_order;
EOD;
        $queryArr['pubstatuses'] =<<<EOD
SELECT
        pubstatusid, pubstatusname
    FROM
        PubStatuses
    ORDER BY
        display_order;
EOD;
        $queryArr['kidscategories'] =<<<EOD
SELECT
        kidscatid, kidscatname
    FROM
        KidsCategories
    ORDER BY
        display_order;
EOD;
        $queryArr['roomsets'] =<<<EOD
SELECT
        roomsetid, roomsetname
    FROM
        RoomSets
    ORDER BY
        display_order;
EOD;
        $queryArr['sessionstatuses'] =<<<EOD
SELECT
        statusid, statusname
    FROM
        SessionStatuses
    ORDER BY
        display_order;
EOD;
        if (is_array($session['featdest']) && count($session['featdest']) > 0) {
            $selected = 'if(featureid IN (' . implode(',', $session['featdest']) . '), 1, 0)';
        } else {
            $selected = '0';
        }
        $queryArr['features'] =<<<EOD
SELECT
        featureid, featurename, $selected as "selected"
    FROM
        Features
    ORDER BY
        selected DESC, display_order;
EOD;
        if (is_array($session['servdest']) && count($session['servdest']) > 0) {
            $selected = 'if(serviceid IN (' . implode(',', $session['servdest']) . '), 1, 0)';
        } else {
            $selected = '0';
        }
        $queryArr['services'] =<<<EOD
SELECT
        serviceid, servicename, $selected as "selected"
    FROM
        Services
    ORDER BY
        selected DESC, display_order;
EOD;
        if (is_array($session['tagdest']) && count($session['tagdest']) > 0) {
            $selected = 'if(tagid IN (' . implode(',', $session['tagdest']) . '), 1, 0)';
        } else {
            $selected = '0';
        }
        $queryArr['tags'] =<<<EOD
SELECT
        tagid, tagname, $selected as "selected"
    FROM
        Tags
    ORDER BY
        selected DESC, display_order;
EOD;
        $resultXML = mysql_query_XML($queryArr);
        if ($resultXML === false) {
            $resultXML = new DomDocument("1.0", "UTF-8");
            $messageFatal = $message_error;
        }
        $doc = $resultXML -> getElementsByTagName('doc') -> item(0);
        $sessionNode = $resultXML -> createElement('session');
        $doc -> appendChild($sessionNode);
        $sessionNode -> setAttribute('secondtitle', $session['secondtitle']);
        $sessionNode -> setAttribute('title', $session['title']);
        $sessionNode -> setAttribute('progguidhtml', $session['progguidhtml']);
        $sessionNode -> setAttribute('progguiddesc', $session['progguiddesc']);
        $sessionNode -> setAttribute('persppartinfo', $session['persppartinfo']);
        $sessionNode -> setAttribute('notesforpart', $session['notesforpart']);
        $sessionNode -> setAttribute('servnotes', $session['servnotes']);
        $sessionNode -> setAttribute('notesforprog', $session['notesforprog']);
        $sessionNode -> setAttribute('mlink', isset($session['mlink']) ? $session['mlink'] : '');
        $sessionNode -> setAttribute('plink', isset($session['plink']) ? $session['plink'] : '');
        $sessionNode -> setAttribute('rlink', isset($session['rlink']) ? $session['rlink'] : '');
        $sessionNode -> setAttribute('clink', isset($session['clink']) ? $session['clink'] : '');
    } else { // $messageFatal has content
        $resultXML = new DomDocument("1.0", "UTF-8");
    }
    $paramArray = array();
    $paramArray['languagestatusid'] = $session['languagestatusid'];
    $paramArray['sessionid'] = $session['sessionid'];
    $paramArray['track'] = $session['track'];
    $paramArray['divisionid'] = $session['divisionid'];
    $paramArray['type'] = $session['type'];
    $paramArray['pubstatusid'] = $session['pubstatusid'];
    $paramArray['kids'] = $session['kids'];
    $paramArray['invguest'] = $session['invguest'];
    $paramArray['atten'] = $session['atten'];
    $paramArray['duration'] = $session['duration'];
    $paramArray['roomset'] = $session['roomset'];
    $paramArray['status'] = $session['status'];
    $paramArray['signup'] = $session['signup'];

    $paramArray['action'] = $action;
    $paramArray['messageSuccess'] = $messageSuccess;
    $paramArray['messageWarning'] = $messageWarning;
    $paramArray['messageFatal'] = $messageFatal;
    $paramArray['name'] = $name;
    $paramArray['email'] = $email;
    $paramArray['track_tag_usage'] = TRACK_TAG_USAGE;
    if (TRACK_TAG_USAGE == 'TAG_ONLY') {
        $paramArray['trackid'] = DEFAULT_TAG_ONLY_TRACK;
    }
    $paramArray['showmeetinglink'] = defined('MEETING_LINK') ? MEETING_LINK : false;
    $paramArray['showparticipantlink'] = defined('PARTICIPANT_LINK') ? PARTICIPANT_LINK : false;
    $paramArray['showrecordinglink'] = defined('RECORDING_LINK') ? RECORDING_LINK : false;
    $paramArray['showcaptionlink'] = defined('CAPTION_LINK') ? CAPTION_LINK : false;
    RenderXSLT('EditCreateSession.xsl', $paramArray, $resultXML);
    staff_footer();
}
?>
