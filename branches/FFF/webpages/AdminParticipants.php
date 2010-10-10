<?php
$title="Administer Participants";
require_once('StaffCommonCode.php');
require_once('SubmitAdminParticipants.php');

staff_header($title);

// Submit the note, if there was one, when this was called
if (isset($_POST["note"])) {
  SubmitNoteOnParticipant($_POST["note"], $_POST["partid"]);
 }

// Submit the updates to this page if there was any
if (isset($_POST["interested"])) {
  SubmitAdminParticipants();
 }

// Carry over the person being noted on, from the form before, if they exist
if (isset($_POST["partid"])) {
  $selpartid=$_POST["partid"];
 }
 else { $selpartid=0; }

//Choose the individual from the database
select_participant($selpartid, "AdminParticipants.php");

//Stop page here if and individual has not yet been selected
if ((!isset($_POST["partid"])) or ($_POST["partid"]==0)) {
    staff_footer();
    exit();
    }

$query ="SELECT interested, pubsname FROM Participants WHERE badgeid=$selpartid";
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
list($interested, $pubsname)= mysql_fetch_array($result, MYSQL_NUM);
?>

<?php echo "<HR>&nbsp;<BR>\n"; ?>
<FORM name="partadminform" method=POST action="AdminParticipants.php">
<P>Participant is interested and available to participate in 
<?php echo CON_NAME; ?> programming:
<INPUT type="hidden" name="wasinterested" value="<?php echo $interested; ?>">
<INPUT type="hidden" name="partid" value="<?php echo $selpartid; ?>">
<SELECT name="interested" class="yesno">
            <OPTION value=0 <?php if ($interested!=1 and $interested!=2 and $interested!=3 and $interested!=4) {echo "selected";} ?> >&nbsp</OPTION>
            <OPTION value=1 <?php if ($interested==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($interested==2) {echo "selected";} ?> >No</OPTION>
            <OPTION value=3 <?php if ($interested==3) {echo "selected";} ?> >Invited</OPTION>
            <OPTION value=4 <?php if ($interested==4) {echo "selected";} ?> >Suggested</OPTION>
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
<DIV class="sectionheader"><A HREF=StaffEditCreateParticipant.php?action=edit&badgeid=<?php echo $selpartid;?>>Edit <?php echo htmlspecialchars($pubsname); ?> Further</A></DIV>
<?php
// Show previous notes added, for references, and end page
ShowNotesOnParticipant($selpartid);
?>
