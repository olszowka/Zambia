<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query=<<<EOD
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
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Pocket Program CSV Report";
	staff_header($title);
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
if (mysql_num_rows($result)==0) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Pocket Program CSV Report";
	staff_header($title);
	$message="Report returned no records.";
    echo "<P>".$message."\n";
    staff_footer();
    exit(); 
	}
header('Content-disposition: attachment; filename=pocketprogram.csv');
header('Content-type: text/csv');
echo "sessionid,day,time,duration,room,track,type,\"kids category\",title,description,participants\n";
while ($row= mysql_fetch_array($result, MYSQL_NUM)) {
	$betweenValues=false;
	foreach ($row as $value) {
		if ($betweenValues) echo ",";
		if (strpos($value,"\"")!==false) {
				$value=str_replace("\"","\"\"",$value);
				echo "\"$value\""; 
				}
			elseif (strpos($value,",")!==false or strpos($value,"\n")!==false) {
				echo "\"$value\"";
				}
			else {
				echo $value;
				}
		$betweenValues=true;
		}
	echo "\n";
	}
exit();
?>