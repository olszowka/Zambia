<?php
require_once('StaffCommonCode.php');
global $link;
$TimecardDB=TIMECARDDB;  // make it a variable so it can be substituted

// Tests for the substituted variables
if ($TimecardDB=="TIMECARDDB") {unset($TimecardDB);}

$title="Volunteer Check In";
$description="<P align=\"center\">Check in the Volunteer in question.</P>";
$additionalinfo = "<P align=\"center\"><A HREF=\"genreport.php?reportname=progvolexpected\">Programming</A> or <A HREF=\"genreport.php?reportname=genvolexpected\">General</A> Volunteer expected to be on.</A><br>";
$additionalinfo.= "<A HREF=\"VolunteerCheckOut.php\">Check Out</A> instead.</P>";

// Should be generated from PermissionAtoms or PermissionRoles, somehow
$permission_array=array('SuperProgramming', 'Programming', 'SuperGeneral', 'General', 'SuperLiaison', 'Liaison', 'SuperWatch', 'Watch', 'SuperRegistration', 'Registration', 'SuperVendor', 'Vendor', 'SuperEvents', 'Events', 'SuperLogistics', 'Logistics', 'SuperSales', 'Sales', 'SuperFasttrack', 'Fasttrack');

foreach ($permission_array as $perm) {
  if (may_I($perm)) {$inrole_array[]="'$perm'";}
}

if (isset($inrole_array)) {
  $inrole_string=implode(",",$inrole_array);
 } else {
  $inrole_string="'P-Volunteer','G-Volunteer'";
 }

$query=<<<EOF
SELECT
    DISTINCT badgeid,
    pubsname 
  FROM
      Participants
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
  WHERE
    permrolename in ($inrole_string)
  ORDER BY
    pubsname
EOF;

if (isset($_POST['badgeid'])) {
  $badgeid=$_POST['badgeid'];
 } elseif (isset($_GET['badgeid'])) {
  $badgeid=$_GET['badgeid'];
 } else {
  topofpagereport($title,$description,$additionalinfo);
  echo "<FORM name=\"whichvol\" method=POST action=\"VolunteerCheckIn.php\">";
  echo "  <DIV style=\"text-align:center\">\n    <LABEL for=\"badgeid\">Volunteer: </LABEL>\n";
  echo "    <SELECT name=\"badgeid\">\n";
  populate_select_from_query($query,$_SESSION['badgeid'],"",false);
  echo "    </SELECT>\n  </DIV>\n";
  echo "  <DIV style=\"text-align:center\">\n";
  echo "    <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButtion\">Choose</BUTTON>\n";
  echo "  </DIV>\n</FORM>\n";
  correct_footer();
  exit();
 }

if (isset($_POST['voltimein'])) {
  $element_array = array('badgeid', 'voltimein', 'volinbadgeid');
  $value_array=array($badgeid,$_POST['voltimein'],$_SESSION['badgeid']);
  $message.=submit_table_element($link, $title, "$TimecardDB.TimeCard", $element_array, $value_array);
  /* if (isset($_SESSION['return_to_page'])) {
    header("Location: ".$_SESSION['return_to_page']); // Redirect back to what send you here
    } else { */
    header("Location: VolunteerCheckIn.php"); // Redirect back to here, with a blank slate
  /* } */
  /* topofpagereport($title,$description,$additionalinfo);
  echo "<P>Database: $TimecardDB.Timecard</P>\n";
  correct_footer(); */
  exit();
 }

$query=<<<EOF
SELECT
    pubsname 
  FROM
      Participants
  WHERE
    badgeid='$badgeid'
EOF;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

$pubsname=$element_array[1]['pubsname'];

topofpagereport($title,$description,$additionalinfo);
?>
<FORM name="volcheckin" method=POST action="VolunteerCheckIn.php">
  <INPUT type="hidden" name="badgeid" value="<?php echo $badgeid; ?>">
  <INPUT type="hidden" name="voltimein" value="<?php echo date('Y-m-d H:i:s'); ?>">
  <DIV style="text-align:center">
    <BUTTON class="SubmitButtion" type="submit" name="submit">Check in <?php echo "$pubsname"; ?> now.</BUTTON>
  </DIV>
</FORM>
<hr>
<FORM name="volfixcheckin" method=POST action="VolunteerCheckIn.php">
  <INPUT type="hidden" name="badgeid" value="<?php echo $badgeid; ?>">
  <DIV style="text-align:center">
    <LABEL for="voltimein">Actual start time for <?php echo "$pubsname: " ?></LABEL>
    <INPUT type="text" size="20" name="voltimein" value="<?php echo date('Y-m-d H:i:s'); ?>"
  </DIV>
  <DIV style="text-align:center">
    <BUTTON class="SubmitButton" type="submit" name="submit">Check in <?php echo "$pubsname"; ?> as of the above date.</BUTTON>
  </DIV>
</FORM>
<?php
correct_footer();
?>