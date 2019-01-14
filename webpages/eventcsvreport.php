<?php
// Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query="SET group_concat_max_len=25000";
if (!$result=mysql_query($query,$link)) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title="Participant Schedule Export for Program Packets";
    staff_header($title);
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
}
$query=<<<EOD
SELECT
        S.sessionid,
        S.title,
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS when2,
        R.roomname,
        GROUP_CONCAT(
            P.pubsname
            SEPARATOR "\n"
            ) AS participants
    FROM
                  Schedule SCH
        LEFT JOIN ParticipantOnSession POS USING (sessionid)
             JOIN Sessions S USING (sessionid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN Participants P USING (badgeid)
    WHERE 
        S.divisionid = 3 /* Events */
    GROUP BY
        S.sessionid
    ORDER BY
        SCH.starttime
EOD;
if (!$result=mysql_query($query,$link)) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title="Send CSV file of Program Packet Export";
    staff_header($title);
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
}
if (mysql_num_rows($result)==0) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title="Send CSV file of Program Packet Export";
    staff_header($title);
    $message="Report returned no records.";
    echo "<P>".$message."\n";
    staff_footer();
    exit();
}
header('Content-disposition: attachment; filename=event_csv_report.csv');
header('Content-type: text/csv');
echo "Session Id,Title,When,Room,Participants\n";
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
