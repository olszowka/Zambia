<?php
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $query=<<<EOD
SELECT
        POS.badgeid, P.pubsname, DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime,
        CASE
			WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
			WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
			ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
			END
			AS duration,
        R.roomname, S.title, IF(POS.moderator=1,'(M)','') AS moderator, CD.firstname, CD.lastname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    WHERE EXISTS (
        SELECT * FROM UserHasPermissionRole UHPR
            WHERE 
                    UHPR.badgeid = P.badgeid
                AND UHPR.permroleid = 6 /* Event Organizer */
        )
    ORDER BY
        P.pubsname;
EOD;
    if (!$result=mysql_query($query,$link)) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Send CSV file of Event Organizer Packet Export 2";
    	staff_header($title);
    	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        echo "<P class\"errmsg\">".$message."\n";
        staff_footer();
        exit();
        }
    if (mysql_num_rows($result)==0) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Send CSV file of Event Organizer Packet Export 2";
    	staff_header($title);
    	$message="Report returned no records.";
        echo "<P>".$message."\n";
        staff_footer();
        exit(); 
    	}
    header('Content-disposition: attachment; filename=event_organizer_packet_merge2.csv');
    header('Content-type: text/csv');
    echo "badgeid, pubs name, start time, duration, room name, title, moderator, first name, last name\n";
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
