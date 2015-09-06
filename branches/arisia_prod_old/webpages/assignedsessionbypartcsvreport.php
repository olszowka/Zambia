<?php
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $query="SET group_concat_max_len=25000";
    if (!$result=mysql_query($query,$link)) {
    	require_once('StaffHeader.php');
    	require_once('StaffFooter.php');
    	$title="Assigned Session by Participant -- Get CSV";
    	staff_header($title);
    	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        echo "<P class\"errmsg\">".$message."\n";
        staff_footer();
        exit();
        }
    $query=<<<EOD
SELECT
           P.badgeid, 
           P.pubsname, 
           IF ((moderator=1), 'Yes', ' ') AS 'Moderator',
           S.sessionid,
           S.title
    FROM
            Sessions S
       JOIN ParticipantOnSession POS USING (sessionid) 
       JOIN Participants P USING (badgeid)
    ORDER BY CAST(P.badgeid AS unsigned);
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
    header('Content-disposition: attachment; filename=assignsessionbypart.csv');
    header('Content-type: text/csv');
    echo "badgeid,pubs name,moderator,sessionid,title\n";
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
