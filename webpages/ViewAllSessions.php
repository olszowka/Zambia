<?php
// Copyright (c) 2005-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Query Session Results";
require_once('StaffCommonCode.php');
require_once('RenderViewSessions.php');
$_SESSION['return_to_page'] = 'ViewAllSessions.php';
$query = <<<EOD
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
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}
RenderViewSessions($result);
exit();
?> 
