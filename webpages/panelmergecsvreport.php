<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query="SET group_concat_max_len=25000";
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Panel Merge Report for Publications";
	staff_header($title);
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$query=<<<EOD
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
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Panel Merge Report for Publications";
	staff_header($title);
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
if (mysql_num_rows($result)==0) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Panel Merge Report for Publications";
	staff_header($title);
	$message="Report returned no records.";
    echo "<P>".$message."\n";
    staff_footer();
    exit(); 
	}
header('Content-disposition: attachment; filename=panelmerge.csv');
header('Content-type: text/csv');
echo "sessionid,room,\"start time\",duration,track,title,participants\n";
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
