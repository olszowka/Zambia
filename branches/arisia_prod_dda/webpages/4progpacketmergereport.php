<?php
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $query="SET group_concat_max_len=25000";
    if (!$result=mysql_query($query,$link)) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Full Participant Schedule for the Program Packet Merge";
    	staff_header($title);
    	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        echo "<P class\"errmsg\">".$message."\n";
        staff_footer();
        exit();
        }
    $query=<<<EOD
SELECT
        POS.badgeid,
        P.pubsname,
        GROUP_CONCAT(
            DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p')," ",
            CASE
                WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
                WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
                ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
                END," ",
            R.roomname, "-",
            S.title,
            IF(moderator=1,'(M)','')
            ORDER BY SCH.starttime
            SEPARATOR "\n") panelinfo
    FROM
            Participants P
       JOIN ParticipantOnSession POS USING (badgeid)
       JOIN Sessions S USING (sessionid)
       JOIN Schedule SCH USING (sessionid)
       JOIN Rooms R USING (roomid)
    GROUP BY
        P.badgeid
    ORDER BY
        P.pubsname
EOD;
    if (!$result=mysql_query($query,$link)) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Send CSV file of Program Packet Merge";
    	staff_header($title);
    	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        echo "<P class\"errmsg\">".$message."\n";
        staff_footer();
        exit();
        }
    if (mysql_num_rows($result)==0) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Send CSV file of Program Packet Merge";
    	staff_header($title);
    	$message="Report returned no records.";
        echo "<P>".$message."\n";
        staff_footer();
        exit(); 
    	}
    header('Content-disposition: attachment; filename=progpacketmerge.csv');
    header('Content-type: text/csv');
    echo "badgeid,pubs name,panel info\n";
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
