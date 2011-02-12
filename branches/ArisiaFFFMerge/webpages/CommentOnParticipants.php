<?php
$title="Comment On Participant";
require_once('StaffCommonCode.php');
require_once('SubmitCommentOn.php');

staff_header($title);

// Collaps the three choices into one
if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

// Submit the comment, if there was one, when this is called
if (isset($_POST["comment"])) {
    SubmitCommentOnParticipants();
    }

// Carry ofer the person being commented on, from the from before if they exist
if (isset($_POST["partid"])) {
        $selpartid=$_POST["partid"];
        }
    else {
        $selpartid=0;
        }

// Choose the individual from the database
select_participant($selpartid, "CommentOnParticipants.php");

// Stop page here if individual has not been selected
if ((!isset($_POST["partid"])) or ($_POST["partid"]==0)) {
    staff_footer();
    exit();
    }
?>
<HR>
<BR>
<FORM name="partcommentform" method=POST action="CommentOnParticipants.php">
  <P>Comment on/for <?php echo htmlspecialchars($pubsname)?> for 
<?php echo CON_NAME; ?>:
<INPUT type="hidden" name="partid" value="<?php echo $selpartid; ?>">
<INPUT type="hidden" name="pubsname" value="<?php echo $pubsname; ?>">
<DIV class="titledtextarea">
  <LABEL for="comment">Comment:</LABEL>
  <TEXTAREA name="comment" rows=6 cols=72></TEXTAREA>
</DIV>
<DIV class="password">
  <span class="password2">Identifying tag of individual offering comment:</span>
  <span class="value"><INPUT type="text" size="30" name="commenter">
        </span>
      </div>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>
<?php
staff_footer();
?>
