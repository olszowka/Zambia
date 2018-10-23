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
        P.pubsname;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
echo_if_zero_rows_and_exit($result);
header('Content-disposition: attachment; filename=progpacketmerge.csv');
header('Content-type: text/csv');
echo "badgeid,pubs name,panel info\n";
render_query_result_as_csv($result);
exit();
?>
