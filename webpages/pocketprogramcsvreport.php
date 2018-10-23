<?php
// Copyright (c) 2015-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
global $title;
$title = "Pocket Program CSV Report";
$query = "SET group_concat_max_len=25000";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
$query = <<<EOD
SELECT
            S.sessionid, 
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') as Day, 
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Time', 
            concat(if(left(duration,2)=00, '', 
                      if(left(duration,1)=0, 
                         concat(right(left(duration,2),1), 'hr '),
                       concat(left(duration,2),'hr '))), 
                   if(date_format(duration,'%i')=00, '', 
                      if(left(date_format(duration,'%i'),1)=0, 
                         concat(right(date_format(duration,'%i'),1),'min'), 
                         concat(date_format(duration,'%i'),'min')))) Duration, 
            roomname, 
            trackname as TRACK, 
	        typename as TYPE,
            K.kidscatname,
            title, 
            progguiddesc as 'Long Text', 
            group_concat(' ',pubsname, if (moderator=1,' (m)','')) as 'PARTIC' 
    FROM
            Sessions S
       JOIN Schedule SCH USING (sessionid)
       JOIN Rooms R USING (roomid)
       JOIN Tracks T USING (trackid)
       JOIN Types Ty USING (typeid)
	   JOIN KidsCategories K USING (kidscatid)
  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
  LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE 
            S.pubstatusid = 2
    GROUP BY
            SCH.sessionid
    ORDER BY 
            SCH.starttime, 
            R.roomname
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=pocketprogram.csv');
header('Content-type: text/csv');
echo "sessionid,day,time,duration,room,track,type,\"kids category\",title,description,participants\n";
render_query_result_as_csv($result);
exit();
?>