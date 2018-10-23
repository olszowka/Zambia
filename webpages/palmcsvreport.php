<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
global $title;
$title = "Send CSV file of Schedule for PDA Upload";
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$query = "SET group_concat_max_len=25000";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
mysqli_free_result($result);
$query = <<<EOD
SELECT
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') as 'Day',
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Start Time',
            left(duration,5) Length,
	        Roomname,
            trackname as Track,
            Title,
            if(group_concat(pubsname) is NULL,'',group_concat(pubsname SEPARATOR ', ')) as 'Participants'
    FROM
            Rooms R
       JOIN Schedule SCH USING (roomid)
       JOIN Sessions S USING (sessionid)
  LEFT JOIN Tracks T USING (trackid)
  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
  LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
            S.pubstatusid = 2
    GROUP BY
            SCH.sessionid
    ORDER BY
            SCH.starttime, R.roomname
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=PDASchedule.csv');
header('Content-type: text/csv');
echo "day,start time,duration,room name,track,title,participants\n";
render_query_result_as_csv($result);
exit();
?>
