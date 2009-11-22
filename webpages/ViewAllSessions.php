<?php
    require_once ('StaffCommonCode.php');
    require_once ('RenderViewSessions.php');
    require_once ('render_functions.php');
    $_SESSION['return_to_page']='ViewAllSessions.php';
    $title="Query Session Results";
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        RenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT
        sessionid, trackname, title,
	CONCAT( IF(LEFT(duration,2)=00, '', IF(LEFT(duration,1)=0, CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))),
	IF(DATE_FORMAT(duration,'%i')=00, '', IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'), CONCAT(DATE_FORMAT(duration,'%i'),'min')))) duration,
	estatten, statusname
    FROM
        Sessions JOIN
        Tracks USING (trackid) JOIN
        SessionStatuses USING (statusid)
    ORDER BY
        trackname, statusname
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        RenderError($title,$message);
        exit ();
        }
    RenderViewSessions();
    exit();
?> 
