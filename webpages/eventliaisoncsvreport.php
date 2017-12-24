<?php
//	Copyright (c) 2011-2017 The Zambia Group. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$query=<<<EOD
SELECT
        S.sessionid, DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a') AS 'day',
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%l:%i %p') AS 'starttime',
        left(S.duration, 5) AS duration, R.roomname, S.title, P.pubsname,
        IF(ISNULL(EO.badgeid), "", "Event Organizer") AS eventorganizer,
        IF(ISNULL(EO.badgeid), "", CD.phone) AS phone,
        IF(ISNULL(EO.badgeid), "", CD.email) AS email
    FROM
            Schedule SCH
       JOIN Rooms R USING (roomid)
       JOIN Sessions S USING (sessionid)
  LEFT JOIN ParticipantOnSession POS USING (sessionid)
  LEFT JOIN Participants P USING (badgeid)
  LEFT JOIN CongoDump CD USING (badgeid)
  LEFT JOIN (SELECT
              badgeid
          FROM
              UserHasPermissionRole
          WHERE
              permroleid = 6 /* Event Organizer */
      ) AS EO USING (badgeid)
    WHERE
            S.divisionid = 2 /* Events */
        AND EXISTS (SELECT *
            FROM
                UserHasPermissionRole UHPR
            WHERE
                    UHPR.badgeid = P.badgeid
                AND (   UHPR.permroleid = 5 /* Event Participant */
                     OR UHPR.permroleid = 6 /* Event Organizer */
                    )
            )
    ORDER BY
            SCH.starttime, R.roomname
EOD;
if (!$result=mysql_query($query,$link)) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Event Liaison Report";
	staff_header($title);
	$message=$query."<br>Error querying database. Unable to continue.<br>";
    echo "<p class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
}
if (mysql_num_rows($result)==0) {
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	$title="Send CSV file of Schedule for PDA Upload";
	staff_header($title);
	$message="Report returned no records.";
    echo "<p>".$message."\n";
    staff_footer();
    exit(); 
}
header('Content-disposition: attachment; filename=EventLiaison.csv');
header('Content-type: text/csv');
echo "sessionid,day,start time,duration,room name,title,participant name,event organizer,phone,email\n";
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $betweenValues = false;
    foreach ($row as $value) {
        if ($betweenValues) echo ",";
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
