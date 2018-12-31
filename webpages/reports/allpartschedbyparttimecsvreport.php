<?php
// Copyright (c) 2015-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
// Report Name: Participant Schedule
// Report Description: Export CSV file of full participant schedule by participant, time
// Report Categories: Reports downloadable as CSVs: 20
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
global $title;
$title = "Send CSV file of Full Participant Schedule";
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$query = <<<EOD
SELECT 
		IF ((P.pubsname IS NULL), ' ', CONCAT(' ',P.pubsname,' (',P.badgeid,')')) AS 'Participant', 
		IF ((moderator=1),'moderator', ' ') AS Moderator,
		DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') AS 'Start Time', 
		CONCAT(IF(LEFT(duration,2)=00, '', IF(LEFT(duration,1)=0, 
			CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))), 
			IF(DATE_FORMAT(duration,'%i')=00, '', IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, 
			CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'), 
			CONCAT(DATE_FORMAT(DURATION,'%i'),'min')))) AS Duration,
		R.roomname,
		function, 
		trackname,
		S.sessionid,
		S.title
	FROM
				Sessions S
   		   JOIN Schedule SCH USING (sessionid)
		   JOIN Rooms R USING (roomid)
	  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
	  LEFT JOIN Participants P ON POS.badgeid=P.badgeid 
	  LEFT JOIN Tracks T ON T.trackid=S.trackid 
	ORDER BY
		CAST(P.badgeid AS unsigned),
		SCH.starttime;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=allpartsched.csv');
header('Content-type: text/csv');
echo "Participant, Moderator, Start Time, Duration, Room, Function, Track, Session ID, Title\n";
render_query_result_as_csv($result);
exit();
?>
