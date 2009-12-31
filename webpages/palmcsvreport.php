<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query=<<<EOD
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
//SELECT
//        P.badgeid, CD.lastname, CD.firstname,
//	    CD.badgename, P.pubsname, P.bio 
//	FROM
//	    Participants P JOIN
//	    CongoDump CD USING (badgeid) JOIN
//	    (SELECT DISTINCT badgeid 
//	       FROM ParticipantOnSession POS JOIN 
//	            Schedule SCH USING (sessionid)
//	     ) as X
//	   USING (badgeid) 
//	ORDER BY
//	    IF(locate(" ",pubsname)!=0,substring(P.pubsname,char_length(pubsname)-locate(" ",reverse(pubsname))+2),pubsname)
//EOD;
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Schedule for PDA Upload";
	staff_header($title);
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
if (mysql_num_rows($result)==0) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Schedule for PDA Upload";
	staff_header($title);
	$message="Report returned no records.";
    echo "<P>".$message."\n";
    staff_footer();
    exit(); 
	}
header('Content-disposition: attachment; filename=PDASchedule.csv');
header('Content-type: text/csv');
echo "day,start time,duration,room name,track,title,participants\n";
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
