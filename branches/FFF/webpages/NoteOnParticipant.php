<?php
$title="Notes On Participant";
require_once('StaffCommonCode.php');

staff_header($title);

// Collaps the three choices into one
if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

// Submit the note, if there was one, when this was called
if (isset($_POST["note"])) {
  submit_participant_note($_POST["note"], $_POST["partid"]);
 }

// Carry over the person being noted on, from the form before, if they exist
if (isset($_POST["partid"])) {
  $selpartid=$_POST["partid"];
 }
 elseif (isset($_GET["partid"])) {
  $selpartid=$_GET["partid"];
 }
 else {
   $selpartid=0;
 }

// Choose the individual from the database
select_participant($selpartid, '', "NoteOnParticipant.php");

// Stop page here if and individual has not yet been selected
if ($selpartid==0) {
  staff_footer();
  exit();
 }

// Add note through form below
?>

<HR>
<BR>
<FORM name="partnoteform" method=POST action="NoteOnParticipant.php">
<INPUT type="hidden" name="partid" value="<?php echo $selpartid; ?>">
<DIV class="titledtextarea">
  <LABEL for="note">Note:</LABEL>
  <TEXTAREA name="note" rows=6 cols=72></TEXTAREA>
</DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
// Show previous notes added, for references, and end page
show_participant_notes ($selpartid);
?>
