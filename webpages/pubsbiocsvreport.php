<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$query=<<<EOD
SELECT
        P.badgeid, CD.lastname, CD.firstname,
	    CD.badgename, P.pubsname, P.bio 
	FROM
	    Participants P JOIN
	    CongoDump CD USING (badgeid) JOIN
	    (SELECT DISTINCT badgeid 
	       FROM ParticipantOnSession POS JOIN 
	            Schedule SCH USING (sessionid)
	     ) as X
	   USING (badgeid) 
	ORDER BY
	    IF(locate(" ",pubsname)!=0,substring(P.pubsname,char_length(pubsname)-locate(" ",reverse(pubsname))+2),pubsname)
EOD;
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Biography Report for Publications";
	staff_header($title);
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
if (mysql_num_rows($result)==0) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Biography Report for Publications";
	staff_header($title);
	$message="Report returned no records.";
    echo "<P>".$message."\n";
    staff_footer();
    exit(); 
	}
header('Content-disposition: attachment; filename=PubBio.csv');
header('Content-type: text/csv');
echo "badgeid,lastname,firstname,badgename,pubsname,bio\n";
while ($row= mysql_fetch_array($result, MYSQL_NUM)) {
	$betweenValues=false;
	foreach ($row as $value) {
		if ($betweenValues) echo ",";
		if (strpos($value,"\"")) {
				$value=str_replace("\"","\"\"",$value);
				echo "\"$value\""; 
				}
			elseif (strpos($value,",") or strpos($value,"\n")) {
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