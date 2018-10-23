<?php
// Copyright (c) 2015-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('csv_report_functions.php');
global $title;
$title = "Send CSV file of Program Packet Merge";
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$query = "SET group_concat_max_len=25000";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
mysqli_free_result($result);
$query = <<<EOD
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
    ORDER BY CAST(P.badgeid AS UNSIGNED);
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=assignsessionbypart.csv');
header('Content-type: text/csv');
echo "badgeid,pubs name,moderator,sessionid,title\n";
render_query_result_as_csv($result);
exit();
?>
