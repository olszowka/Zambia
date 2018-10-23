<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
global $title;
$title="Send CSV file of Description Report for Publications";
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$query = "SET group_concat_max_len=25000";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
mysqli_free_result($result);
$query = <<<EOD
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
	          S.progguiddesc AS 'Description'
	FROM
	          Schedule SCH
	     JOIN Sessions S USING(sessionid)
	     JOIN Tracks T USING(trackid)
	     JOIN Types TY USING(typeid)
	     JOIN Divisions DV USING(divisionid)
	     JOIN PubStatuses PS USING(pubstatusid)
	     JOIN KidsCategories K USING(kidscatid)
	LEFT JOIN SessionHasPubChar SHPC USING(sessionid)
	LEFT JOIN PubCharacteristics PC USING(pubcharid)
	    WHERE PS.pubstatusname = 'Public'
	 GROUP BY scheduleid
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=longdesc.csv');
header('Content-type: text/csv');
echo "sessionid,track,type,division,\"publication status\",pubsno,\"publication characteristics\",\"kids category\",title,description\n";
render_query_result_as_csv($result);
exit();
?>
