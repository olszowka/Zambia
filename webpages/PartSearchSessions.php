<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo;
global $partAvail, $availability, $title;
$title="Search Sessions";
require ('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
participant_header($title, false, 'Normal', true);
if (!may_I('search_panels')) {
    
    $message_error="You do not currently have permission to view this page.<br>\n";
    RenderError($message_error);
    exit();
    }

$queryArray = array();
$queryArray['interested'] = <<<EOD
SELECT
        P.interested
    FROM
        Participants P
    WHERE
        P.badgeid = '$badgeid';
EOD;
$queryArray['tracks'] = <<<EOD
SELECT
        trackid, trackname
    FROM
        Tracks
    WHERE
        selfselect=1
    ORDER BY
        display_order;
EOD;
$queryArray['tags'] = <<<EOD
SELECT
        tagid, tagname
    FROM
        Tags
    ORDER BY
        display_order;
EOD;
if (($resultXML = mysql_query_XML($queryArray)) === false) {
    $message="Error querying database. Unable to continue.<br>";
    echo "<p class\"alert alert-error\">$message</p>\n";
    participant_footer();
    exit();
}
$paramArray = array();
$paramArray["conName"] = CON_NAME;
$paramArray["showTags"] = TRACK_TAG_USAGE !== "TRACK_ONLY";
$paramArray["showTrack"] = TRACK_TAG_USAGE !== "TAG_ONLY";
// echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
RenderXSLT('PartSearchSessions.xsl', $paramArray, $resultXML);

participant_footer();
?>
