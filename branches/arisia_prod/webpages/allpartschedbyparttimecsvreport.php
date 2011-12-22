<?php
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $query=<<<EOD
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
    if (!$result=mysql_query($query,$link)) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Send CSV file of Full Participant Schedule";
    	staff_header($title);
    	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        echo "<P class\"errmsg\">".$message."\n";
        staff_footer();
        exit();
        }
    if (mysql_num_rows($result)==0) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Send CSV file of Full Participant Schedule";
    	staff_header($title);
    	$message="Report returned no records.";
        echo "<P>".$message."\n";
        staff_footer();
        exit(); 
    	}
    header('Content-disposition: attachment; filename=allroomsched.csv');
    header('Content-type: text/csv');
    echo "Participant, Moderator, Start Time, Duration, Room, Function, Track, Session ID, Title\n";
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
