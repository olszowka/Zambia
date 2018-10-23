<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
global $title;
$title = "Send CSV file of Panel Merge Report for Publications";
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$query = "SET group_concat_max_len=25000";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
mysqli_free_result($result);
$query = <<<EOD
SELECT
            S.sessionid, 
            R.roomname, 
            DATE_FORMAT(ADDTIME('2010-01-15 00:00:00',SCH.starttime),'%a %l:%i %p') starttime, 
            CASE
                WHEN HOUR(S.duration) < 1 THEN concat(date_format(S.duration,'%i'),'min')
                WHEN MINUTE(S.duration)=0 THEN concat(date_format(S.duration,'%k'),'hr')
                ELSE concat(date_format(S.duration,'%k'),'hr ',date_format(S.duration,'%i'),'min')
                END
                AS duration,
            T.trackname, 
            S.title, 
            group_concat(P.pubsname, if(POS.moderator=1,'(m)','') ORDER BY POS.moderator DESC SEPARATOR ', ') panelinfo,
            PUB.pubstatusname
    FROM
            Sessions S
       JOIN Schedule SCH USING(sessionid)
       JOIN Rooms R USING(roomid)
       JOIN Tracks T USING(trackid)
       JOIN PubStatuses PUB USING(pubstatusid)
  LEFT JOIN ParticipantOnSession POS USING(sessionid)
  LEFT JOIN Participants P USING(badgeid)
   GROUP BY
            S.sessionid
   ORDER BY
            SCH.starttime
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=panelmerge.csv');
header('Content-type: text/csv');
echo "sessionid,room,\"start time\",duration,track,title,participants\n";
render_query_result_as_csv($result);
exit();
?>
