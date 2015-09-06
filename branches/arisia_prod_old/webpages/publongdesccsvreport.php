<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query=<<<EOD
SELECT
	          S.sessionid,
	          T.trackname,
	          TY.typename,
	          DV.divisionname,
	          PS.pubstatusname,
	          S.pubsno,
	          group_concat(PC.pubcharname SEPARATOR ' ') pubcharacteristics,
	          K.kidscatname,
	          S.title,
	          S.progguiddesc as 'Description'
	FROM
	          Schedule SCH
	     JOIN Sessions S USING(sessionid)
	     JOIN Tracks T USING(trackid)
	     JOIN Types TY USING(typeid)
	     JOIN Divisions DV USING(divisionid)
	     JOIN PubStatuses PS USING(pubstatusid)
	     JOIN KidsCategories K USING(kidscatid)
	left join SessionHasPubChar SHPC USING(sessionid)
	left join PubCharacteristics PC USING(pubcharid)
	    where PS.pubstatusname = 'Public'
	 GROUP BY scheduleid
EOD;
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Description Report for Publications";
	staff_header($title);
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
if (mysql_num_rows($result)==0) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Description Report for Publications";
	staff_header($title);
	$message="Report returned no records.";
    echo "<P>".$message."\n";
    staff_footer();
    exit(); 
	}
header('Content-disposition: attachment; filename=longdesc.csv');
header('Content-type: text/csv');
echo "sessionid,track,type,division,\"publication status\",pubsno,\"publication characteristics\",\"kids category\",title,description\n";
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
