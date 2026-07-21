<?php
// Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "View All Sessions";
require_once('StaffCommonCode.php');
staff_header($title, 'bs5', true);
$_SESSION['return_to_page'] = 'ViewAllSessions.php';
$queryArray = array();
$queryArray["sessions"] = <<<EOD
SELECT
        S.sessionid, TR.trackname, S.title,
        CONCAT( IF(LEFT(S.duration,2)=00, '', IF(LEFT(S.duration,1)=0, CONCAT(RIGHT(LEFT(S.duration,2),1),'hr '), CONCAT(LEFT(S.duration,2),'hr '))),
        IF(DATE_FORMAT(S.duration,'%i')=00, '', IF(LEFT(DATE_FORMAT(S.duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(S.duration,'%i'),1),'min'),
        CONCAT(DATE_FORMAT(S.duration,'%i'),'min')))) AS duration, S.estatten, SS.statusname,
        GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
    FROM
                  Sessions S
             JOIN Tracks TR USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
        LEFT JOIN Tags TA USING (tagid)
    GROUP BY
        S.sessionid
    ORDER BY
        trackname, statusname;
EOD;
if (($resultXML = mysql_query_XML($queryArray)) === false) {
    echo "<p class=\"alert alert-danger\">Error querying database. Unable to continue.<br></p>\n";
    staff_footer();
    exit();
}
RenderXSLT('ViewAllSessions.xsl', array(), $resultXML);
staff_footer();
exit();
?>
