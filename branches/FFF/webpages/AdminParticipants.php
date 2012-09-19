<?php
$title="Administer Participants";
require_once('StaffCommonCode.php');
require_once('SubmitAdminParticipants.php');

$conid=$_SESSION['conid'];
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// Collaps the three choices into one
if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

// Submit the note, if there was one, when this was called
if (isset($_POST["note"])) {
  submit_participant_note ($_POST["note"], $_POST["partid"]);
 }

// Submit the updates to this page if there was any
if (isset($_POST["update"]) AND ($_POST["update"]=="please")) {
  SubmitAdminParticipants();
 }

// Carry over the person being noted on, from the form before, if they exist
if (isset($_GET["partid"])) {
  $_POST["partid"]=$_GET["partid"];
}
if (isset($_POST["partid"])) {
  $selpartid=$_POST["partid"];
 }
 else { $selpartid=0; }

// Begin page
topofpagereport($title,$description,$additionalinfo);

// Choose the individual from the database
select_participant($selpartid, '', "AdminParticipants.php");

// Stop page here if and individual has not yet been selected
if ((!isset($_POST["partid"])) or ($_POST["partid"]==0)) {
    correct_footer();
    exit();
    }

$query = <<<EOD
SELECT
    pubsname
  FROM
      $ReportDB.Participants
  WHERE
    badgeid=$selpartid
EOD;

if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    correct_footer();
    exit();
    }

list($pubsname)= mysql_fetch_array($result, MYSQL_NUM);

$query = <<<EOD
SELECT
    interestedtypeid
  FROM
      $ReportDB.Participants
    JOIN $ReportDB.Interested I USING (badgeid)
  WHERE
    badgeid=$selpartid AND
    I.conid=$conid
EOD;

if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    correct_footer();
    exit();
    }

list($interested)= mysql_fetch_array($result, MYSQL_NUM);

?>

<?php echo "<HR>&nbsp;<BR>\n"; ?>
<FORM name="partadminform" method=POST action="AdminParticipants.php">
  <INPUT type="hidden" name="wasinterested" value="<?php echo $interested; ?>">
  <INPUT type="hidden" name="partid" value="<?php echo $selpartid; ?>">
  <INPUT type="hidden" name="update" value="please">
  <LABEL for="interested">Participant is interested and available to participate in 
  <?php echo CON_NAME; ?>:</LABEL>
  <SELECT name="interested">
  <?php populate_select_from_table("$ReportDB.InterestedTypes", $interested, " ", FALSE); ?>
  </SELECT>
  <B>*** Changing this to no will remove the particpant from all sessions. ***</B><BR>
  <div class="password">
      <span class="password2">Change their Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="password"></span>
  </div>
  <div class="password">
      <span class="password2">Confirm New Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="cpassword"></span>
  </div>
  <div class="password">
      <span class="password2">Name for Publications&nbsp;</span>
      <span class="value"><INPUT type="text" size="30" name="pubsname" value="<?php echo htmlspecialchars($pubsname); ?>">
      </span>
  </div>
  <DIV class="titledtextarea">
      <LABEL for="note">Do not forget to annotate the update:</LABEL>
      <TEXTAREA name="note" rows=6 cols=72>Reset password on request. -AND/OR- Updated attendance state to:</TEXTAREA>
  </DIV>
  <BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>
<BR>
<HR>
<DIV class="sectionheader">
<?php
  $printname=htmlspecialchars($pubsname);
echo "<A HREF=StaffEditCreateParticipant.php?action=edit&partid=$selpartid>Edit $printname Further</A> ::\n";
if (may_I(SuperLiaison)) {
  echo "<A HREF=StaffEditCompensation.php?partid=$selpartid>Set Compensation for $printname</A> ::\n";
}
if (may_I(Participant)) {
  echo "<A HREF=ClassIntroPrint.php?individual=$selpartid>Print Intros for $printname</A> ::\n";
  echo "<A HREF=WelcomeLettersPrint.php?individual=$selpartid>Print Welcome Letter for $printname</A> ::\n";
}
echo "<A HREF=SchedulePrint.php?individual=$selpartid>Print Schedule for $printname</A>\n";
echo "</DIV>\n";

// Show previous notes added, for references, and end page
show_participant_notes ($selpartid);
?>
