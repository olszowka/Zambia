<?php
require_once('StaffCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$title="Invite Participants";
$description="<P>Use this tool to put sessions marked \"invited guests only\" on a participant's interest list.</P>\n";


// Collaps the three choices into one
if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

if (isset($_POST["partid"])) {
  $selpartid=$_POST["partid"];
} elseif (isset($_GET["partid"])) {
  $selpartid=$_GET["partid"];
} else {
  $selpartid=0;
}

if (isset($_POST["selsess"])) { // room was selected by a form
  $selsessionid=$_POST["selsess"];
} elseif (isset($_GET["selsess"])) { // room was select by external page such as a report
  $selsessionid=$_GET["selsess"];
} else {
  $selsessionid=0; // room was not yet selected.
}

topofpagereport($title,$description,$additionalinfo);

if (($selpartid!=0) && ($selsessionid!=0)) {
  $query="INSERT INTO ParticipantSessionInterest SET badgeid=\"".$selpartid."\", ";
  $query.="sessionid=".$selsessionid;
  $result=mysql_query($query,$link);
  if ($result) {
    echo "<P class=\"regmsg\">Database successfully updated.</P>\n";
  } elseif (mysql_errno($link)==1062) {
    echo "<P class=\"errmsg\">Database not updated.  That participant was already invited to that session.</P>";
  } else {
    echo $query."<P class=\"errmsg\">Database not updated.</P>";
  }
}

if ($selsessionid==0) {
  select_participant($selpartid, "'Yes'", "InviteParticipants.php");
} else {
  select_participant($selpartid, "'Yes'", "InviteParticipants.php?selsess=$selsessionid");
}

echo "<hr>\n";

// Limit it to just the appropriate set of schedule elements presented
if (may_I("Programming")) {$pubstatus_array[]="'Prog Staff'"; $pubstatus_array[]="'Public'";}
if (may_I("Liaison")) {$pubstatus_array[]="'Public'";}
if (may_I("General")) {$pubstatus_array[]="'Volunteer'";}
if (may_I("Event")) {$pubstatus_array[]="'Event Staff'";}
if (may_I("Registration")) {$pubstatus_array[]="'Reg Staff'";}
if (may_I("Watch")) {$pubstatus_array[]="'Watch Staff'";}
if (may_I("Vendor")) {$pubstatus_array[]="'Vendor Staff'";}
if (may_I("Sales")) {$pubstatus_array[]="'Sales Staff'";}
if (may_I("Fasttrack")) {$pubstatus_array[]="'Fast Track'";}
if (may_I("Logistics")) {$pubstatus_array[]="'Logistics'"; $pubstatus_array[]="'Public'";}

if (isset($pubstatus_array)) {
  $pubstatus_string=implode(",",$pubstatus_array);
 } else {
  $pubstatus_string="'Public'";
 }

$query=<<<EOD
SELECT
    sessionid,
    concat(trackname,' - ',sessionid,' - ',title) as sname
  FROM
      Sessions
    JOIN $ReportDB.Tracks USING (trackid)
    JOIN $ReportDB.SessionStatuses USING (statusid)
    JOIN $ReportDB.PubStatuses USING (pubstatusid)
  WHERE
    may_be_scheduled=1 AND
    pubstatusname in ($pubstatus_string)
  ORDER BY
    trackname,
    sessionid,
    title
EOD;

echo "<FORM name=\"selsesform\" method=POST action=\"InviteParticipants.php\">\n";
echo "<INPUT type=\"hidden\" name=\"partid\" value=\"$selpartid\">\n";
echo "<DIV><LABEL for=\"selsess\">Select Session</LABEL>\n";
echo "<SELECT name=\"selsess\">\n";
populate_select_from_query($query,$selsessionid, "Select Session", false);
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\">";
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
echo "</FORM>\n";

correct_footer();
exit();
?>
