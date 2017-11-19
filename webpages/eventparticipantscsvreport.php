<?php
//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query="SET group_concat_max_len=25000";
if (!$result = mysql_query($query, $link)) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title = "Send CSV file of Event Schedule and Participants";
    staff_header($title);
    $message = $query . "<br>Error querying database. Unable to continue.<br>";
    echo "<p class\"errmsg\">" . $message . "\n";
    staff_footer();
    exit();
}
$query=<<<EOD
SELECT
        S.title, S.sessionid, R.roomname,
        DATE_FORMAT(ADDTIME('$ConStartDatim', SCH.starttime),'%a') as day, 
        DATE_FORMAT(ADDTIME('$ConStartDatim', SCH.starttime),'%l:%i %p') as time, 
        concat(if(left(duration,2)=00, '', 
            if(left(duration,1)=0, 
                concat(right(left(duration,2),1), 'hr '),
                concat(left(duration,2),'hr '))), 
                if(date_format(duration,'%i')=00, '', 
                if(left(date_format(duration,'%i'),1)=0, 
                concat(right(date_format(duration,'%i'),1),'min'), 
                concat(date_format(duration,'%i'),'min')))) as duration,
        T.trackname, P.pubsname, CD.firstname, CD.lastname

    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN Tracks T USING (trackid)
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
    WHERE
        S.divisionid = 3; /* Events */
EOD;
if (!$result = mysql_query($query, $link)) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title = "Pocket Program CSV Report";
    staff_header($title);
    $message = $query . "<br>Error querying database. Unable to continue.<br>";
    echo "<p class\"errmsg\">" . $message . "\n";
    staff_footer();
    exit();
}
if (mysql_num_rows($result) == 0) {
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $title = "Pocket Program CSV Report";
    staff_header($title);
    $message = "Report returned no records.";
    echo "<p>" . $message . "\n";
    staff_footer();
    exit();
}
header('Content-disposition: attachment; filename=event_schedule_participants.csv');
header('Content-type: text/csv');
echo "title,sessionid,room,day,time,duration,track,\"pubs name\", \"last name\", \"first name\"\n";
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $betweenValues = false;
    foreach ($row as $value) {
        if ($betweenValues) {
            echo ",";
        }
        if (strpos($value, "\"") !== false) {
            $value = str_replace("\"", "\"\"", $value);
            echo "\"$value\"";
        } elseif (strpos($value, ",") !== false or strpos($value, "\n") !== false) {
            echo "\"$value\"";
        } else {
            echo $value;
        }
        $betweenValues = true;
    }
    echo "\n";
}
exit();
?>