<?php
// Copyright (c) 2005-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
require_once('db_functions.php');
require_once('BrainstormCommonCode.php');
$title = "Scheduled Suggestions";
$showlinks =  getInt("showlinks");
$_SESSION['return_to_page'] = "ViewPrecis.php?showlinks=$showlinks";
if ($showlinks == 1) {
    $showlinks = true;
} elseif ($showlinks == 0) {
    $showlinks = false;
}
// inclusion of configuration file db_name.php occurs here
if (prepare_db_and_more() === false) {
    $message = "Error connecting to database.";
    RenderError($message);
    exit ();
}
$ConStartDatim = CON_START_DATIM;
$query = <<<EOD
SELECT
        sessionid, trackname, null typename, title, 
        CONCAT( IF(LEFT(duration,2)=00, '', 
                IF(LEFT(duration,1)=0, CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))),
                IF(DATE_FORMAT(duration,'%i')=00, '', 
                IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'), 
            CONCAT(DATE_FORMAT(duration,'%i'),'min')))) Duration,
        estatten, progguiddesc, persppartinfo, roomname,
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
    	          Sessions S
    	     JOIN Tracks TR USING (trackid)
    	     JOIN SessionStatuses SS USING (statusid)
		LEFT JOIN Schedule SCH USING (sessionid)
		LEFT JOIN Rooms R USING (roomid)
    WHERE
            SS.statusname IN ('Assigned','Scheduled')
        AND S.invitedguest=0;
EOD;
brainstorm_header($title);
$result = mysqli_query_exit_on_error($query);
echo "<p> These ideas are highly likely to make it into the final schedule. Things are looking good for them.  Please remember events out of our control and last minute emergencies cause this to change!  No promises, but we are doing our best to have this happen. ";
echo "<p> If you want to help, email us at ";
echo "<a href=\"mailto:" . PROGRAM_EMAIL . "\">" . PROGRAM_EMAIL . "</a> </p>\n";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, $showlinks);
brainstorm_footer();
exit();
?> 

