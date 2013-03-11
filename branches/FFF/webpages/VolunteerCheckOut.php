<?php
require_once('StaffCommonCode.php');
global $link;
$conid=$_SESSION['conid'];
$TimecardDB=TIMECARDDB; // make it a variable so it can be substituted
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}
if ($TimecardDB=="TIMECARDDB") {unset($TimecardDB);}

$title="Volunteer Check Out";
$description="<P align=\"center\">Check out the Volunteer's <A HREF=\"VolunteerCheckIn.php\">Check In</A> instance.</P>";

/* Get all the Permission Roles */
$query = <<<EOD
SELECT
    permrolename,
    notes
  FROM
      $ReportDB.PermissionRoles
  WHERE
    permroleid > 1
EOD;

list($permrole_rows,$permrole_header_array,$permrole_array)=queryreport($query,$link,"Broken Query",$query,0);

// Empty Title Switch to begin with.
$TitleSwitch="";

/* Attempt to establish default graph based on permissions */
for ($i=1; $i<=$permrole_rows; $i++) {
  if (may_I($permrole_array[$i]['permrolename'])) {
    $permrolecheck_array[]="'".$permrole_array[$i]['permrolename']."'";
   }
 }
$permrolecheck_string=implode(",",$permrolecheck_array);

$query=<<<EOD
SELECT
    voltimeid,
    concat(pubsname, " in at: ",DATE_FORMAT(voltimein,'%a %l:%i %p (%k:%i)')) as 'Who'
  FROM
      $ReportDB.Participants
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
    JOIN $TimecardDB.TimeCard USING (badgeid)
  WHERE
    UHPR.conid=$conid AND
    permrolename in ($permrolecheck_string) AND
    voltimeout IS NULL
  ORDER BY
    pubsname
EOD;

if (isset($_POST['voltimeid'])) {
  $voltimeid=$_POST['voltimeid'];
 } elseif (isset($_GET['voltimeid'])) {
  $voltimeid=$_GET['voltimeid'];
 } else {
  $_SESSION['return_to_page']="VolunteerCheckIn.php";
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
  if (isset($_POST['onepageprog'])) {
    header("Location: genreport.php?reportname=progvolexpected"); // Redirect back to the progvolexpected report
  } elseif (isset($_POST['onepagegen'])) {
    header("Location: genreport.php?reportname=genvolexpected"); // Redirect back to the progvolexpected report
  } elseif (isset($_SESSION['return_to_page'])) {
    header("Location: ".$_SESSION['return_to_page']); // Redirect back to what send you here
  } else {
    header("Location: VolunteerCheckOut.php"); // Redirect back to here, with a blank slate
  }
  exit();
 }

$query=<<<EOD
SELECT
    pubsname,
    if (((voltimein > '$ConStartDatim') AND
	 (voltimein < ADDTIME('$ConStartDatim',SEC_TO_TIME('$ConNumDays'*86400)))),
        DATE_FORMAT(voltimein,'%a %l:%i %p (%k:%i)'),
        DATE_FORMAT(voltimein,'%c/%e %l:%i %p (%k:%i)')) AS "inat"
  FROM
      $TimecardDB.TimeCard
    JOIN $ReportDB.Participants USING (badgeid)
  WHERE
    voltimeid='$voltimeid'
EOD;

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