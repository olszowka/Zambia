<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Query Session Results";
require_once('StaffCommonCode.php');
require_once('RenderViewSessions.php');
$_SESSION['return_to_page'] = 'ViewAllSessions.php';
$query = <<<EOD
SELECT
        sessionid, trackname, title,
        CONCAT( IF(LEFT(duration,2)=00, '', IF(LEFT(duration,1)=0, CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))),
        IF(DATE_FORMAT(duration,'%i')=00, '', IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'),
        CONCAT(DATE_FORMAT(duration,'%i'),'min')))) duration, estatten, statusname
    FROM
        Sessions JOIN
        Tracks USING (trackid) JOIN
        SessionStatuses USING (statusid)
    ORDER BY
        trackname, statusname;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}
RenderViewSessions($result);
exit();
?> 
