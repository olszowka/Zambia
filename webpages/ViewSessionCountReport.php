<?php
// Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "View Session Counts";
require_once('StaffCommonCode.php');
staff_header($title, 'bs5');
$query = array();
$query['sessions'] = <<<EOD
SELECT
        TR.trackname, SS.statusname, count(*) AS `count` 
    FROM
            Sessions
       JOIN Tracks TR USING (trackid)
       JOIN SessionStatuses SS USING (statusid)
    GROUP BY
        SS.statusname, TR.trackname;
EOD;
if (!$resultXML = mysql_query_XML($query)) {
    RenderError('Internal Server Error');
    exit(); // Should have exited already
}
$paramArray = array();
RenderXSLT('ViewSessionCountReport.xsl', $paramArray, $resultXML);
staff_footer();
?>
