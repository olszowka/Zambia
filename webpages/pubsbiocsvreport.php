<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
global $title;
$title = "Send CSV file of Biography Report for Publications";
$query = <<<EOD
SELECT
        P.badgeid, CD.lastname, CD.firstname,
	    CD.badgename, P.pubsname, P.bio 
	FROM
	    Participants P JOIN
	    CongoDump CD USING (badgeid) JOIN
	    (SELECT DISTINCT badgeid 
	       FROM ParticipantOnSession POS JOIN 
	            Schedule SCH USING (sessionid)
	     ) AS X
	   USING (badgeid) 
	ORDER BY
	    IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
// order by: if lastname is part of pubsname, order by it, otherwise, order by last word/token in pubsname
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=PubBio.csv');
header('Content-type: text/csv');
echo "badgeid,lastname,firstname,badgename,pubsname,bio\n";
render_query_result_as_csv($result);
exit();
?>
