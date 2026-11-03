<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Search Sessions";
require_once('StaffCommonCode.php');
staff_header($title, 'bs5');
if (may_I('Staff')) {
    $queryArr = array();
    $queryArr['tracks'] = <<<EOD
SELECT
        trackid, trackname, display_order
    FROM
        Tracks
    ORDER BY
        display_order;
EOD;
    $queryArr['types'] = <<<EOD
SELECT
        typeid, typename, display_order
    FROM
        Types
    ORDER BY
        display_order;
EOD;
    $queryArr['session_statuses'] = <<<EOD
SELECT
        statusid, statusname, display_order
    FROM
        SessionStatuses
    ORDER BY
        display_order;
EOD;
    $queryArr['divisions'] = <<<EOD
SELECT
        divisionid, divisionname, display_order
    FROM
        Divisions
    ORDER BY
        display_order;
EOD;
    $queryArr['tags'] = <<<EOD
SELECT
        tagid, tagname, display_order
    FROM
        Tags
    ORDER BY
        display_order;
EOD;
    $resultXML = mysql_query_XML($queryArr);
    if ($resultXML === false) {
        RenderError('Internal Error.'); // exits
    }
    $track_tag_usage = TRACK_TAG_USAGE;
    $paramArray = array();
    $paramArray['track_tag_usage'] = TRACK_TAG_USAGE;
    RenderXSLT('SearchSessions.xsl', $paramArray, $resultXML);
    staff_footer();
}
