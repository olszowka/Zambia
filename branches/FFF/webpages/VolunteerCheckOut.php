<?php
require_once('StaffCommonCode.php');
global $link;
$TimecardDB=TIMECARDDB; // make it a variable so it can be substituted
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($TimecardDB=="TIMECARDDB") {unset($TimecardDB);}

$title="Volunteer Check Out";
$description="<P align=\"center\">Check out the Volunteer's <A HREF=\"VolunteerCheckIn.php\">Check In</A> instance.</P>";

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
    voltimeid,
    concat(pubsname, " in at: ",DATE_FORMAT(voltimein,'%a %l:%i %p (%k:%i)')) as 'Who'
  FROM
      Participants
    JOIN UserHasPermissionRole USING (badgeid)
    JOIN PermissionRoles USING (permroleid)
    JOIN $TimecardDB.TimeCard USING (badgeid)
  WHERE
    permrolename in ($inrole_string) AND
    voltimeout IS NULL
  ORDER BY
    pubsname
EOF;

if (isset($_POST['voltimeid'])) {
  $voltimeid=$_POST['voltimeid'];
 } elseif (isset($_GET['voltimeid'])) {
  $voltimeid=$_GET['voltimeid'];
 } else {
  topofpagereport($title,$description,$additionalinfo);
  echo "<FORM name=\"whichvol\" method=POST action=\"VolunteerCheckOut.php\">";
  echo "  <DIV style=\"text-align:center\">\n    <LABEL for=\"voltimeid\">Volunteer: </LABEL>\n";
  echo "    <SELECT name=\"voltimeid\">\n";
  populate_select_from_query($query,$_SESSION['badgeid'],"",false);
  echo "    </SELECT>\n  </DIV>\n";
  echo "  <DIV style=\"text-align:center\">\n";
  echo "    <BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButtion\">Choose</BUTTON>\n";
  echo "  </DIV>\n</FORM>\n";
  correct_footer();
  exit();
 }

if (isset($_POST['voltimeout'])) {
  $pairedvalue_array=array("voltimeout='".$_POST['voltimeout']."'","voloutbadgeid='".$_SESSION['badgeid']."'");
  $message.=update_table_element($link, $title, "$TimecardDB.TimeCard", $pairedvalue_array, "voltimeid", $_POST['voltimeid']);
  if (isset($_SESSION['return_to_page'])) {
    header("Location: ".$_SESSION['return_to_page']); // Redirect back to what send you here
  } else {
    header("Location: VolunteerCheckOut.php"); // Redirect back to here, with a blank slate
  }
  exit();
 }

$query=<<<EOF
SELECT
    pubsname,
    if (((voltimein > '$ConStartDatim') AND
	 (voltimein < ADDTIME('$ConStartDatim',SEC_TO_TIME('$ConNumDays'*86400)))),
        DATE_FORMAT(voltimein,'%a %l:%i %p (%k:%i)'),
        DATE_FORMAT(voltimein,'%c/%e %l:%i %p (%k:%i)')) AS "inat"
  FROM
      $TimecardDB.TimeCard
    JOIN Participants USING (badgeid)
  WHERE
    voltimeid='$voltimeid'
EOF;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

$pubsname=$element_array[1]['pubsname'];
$inat="<B>".$element_array[1]['inat']."</B> for $pubsname";

topofpagereport($title,$description,$additionalinfo);
?>
<FORM name="volcheckout" method=POST action="VolunteerCheckOut.php">
  <INPUT type="hidden" name="voltimeid" value="<?php echo $voltimeid; ?>">
  <INPUT type="hidden" name="voltimeout" value="<?php echo date('Y-m-d H:i:s'); ?>">
  <DIV style="text-align:center">
    <BUTTON class="SubmitButtion" type="submit" name="submit">Check out <?php echo "$pubsname"; ?> now.</BUTTON>
  </DIV>
</FORM>
<hr>
<FORM name="volfixcheckout" method=POST action="VolunteerCheckOut.php">
  <INPUT type="hidden" name="voltimeid" value="<?php echo $voltimeid; ?>">
  <DIV style="text-align:center">
    <LABEL for="voltimeout">Actual end time for shift starting at <?php echo "$inat: " ?></LABEL>
    <INPUT type="text" size="20" name="voltimeout" value="<?php echo date('Y-m-d H:i:s'); ?>"
  </DIV>
  <DIV style="text-align:center">
    <BUTTON class="SubmitButton" type="submit" name="submit">Check out <?php echo "$pubsname"; ?> as of the above date.</BUTTON>
  </DIV>
</FORM>
<?php
correct_footer();
?>