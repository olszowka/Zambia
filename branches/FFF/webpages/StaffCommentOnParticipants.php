<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
  } else {
  require_once('PartCommonCode.php');
  }
require_once('SubmitCommentOn.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$title="Comment On Participant";
$description="<P>Please add a comment about a presenter, below.</P>";

// Start the page properly
topofpagereport($title,$description,$additionalinfo);

// Collaps the three choices into one
if ($_POST["partidl"]!=0) {$partid=$_POST["partidl"];}
if ($_POST["partidf"]!=0) {$partid=$_POST["partidf"];}
if ($_POST["partidp"]!=0) {$partid=$_POST["partidp"];}
if ($_POST["partid"]!=0) {$partid=$_POST["partid"];}
if ($_GET["partid"]!=0) {$partid=$_GET["partid"];}

// Submit the comment, if there was one, when this is called
if (isset($_POST["comment"])) {
    SubmitCommentOnParticipants();
    }

// Choose the individual from the database, if not previously selected
if (!($_GET["partid"]!=0)) {
  select_participant($partid, "'Yes'", "StaffCommentOnParticipants.php");
  echo "<HR>";
 }

// Stop page here if individual has not been selected
if ((!isset($partid)) or ($partid==0)) {
    correct_footer();
    exit();
    }

// Query to get the pubsname of the individuals in question.
$query="SELECT pubsname FROM $ReportDB.Participants WHERE badgeid='$partid'";
list($participant,$header_array,$participant_array)=queryreport($query,$link,$title,$description,0);
$pubsname=$participant_array[1]['pubsname'];

?>
<BR>
<FORM name="partcommentform" method=POST action="StaffCommentOnParticipants.php">
  <P>Comment on/for <?php echo htmlspecialchars($pubsname)?> for 
<?php echo CON_NAME; ?>:
<INPUT type="hidden" name="partid" value="<?php echo $partid; ?>">
<INPUT type="hidden" name="pubsname" value="<?php echo $pubsname; ?>">
<DIV class="titledtextarea">
  <LABEL for="comment">Comment:</LABEL>
  <TEXTAREA name="comment" rows=6 cols=72></TEXTAREA>
</DIV>
<DIV class="password">
  <span class="password2">Identifying tag of individual offering comment:</span>
  <span class="value"><INPUT type="text" size="30" name="commenter" value="<?php echo $_SESSION['badgename']; ?>">
        </span>
      </div>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>
<?php
correct_footer();
?>
