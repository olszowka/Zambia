<?php
$title="Comment On Participant";
require_once('StaffCommonCode.php');
require_once('SubmitCommentOn.php');

staff_header($title);

if (isset($_POST["comment"])) {
    SubmitCommentOnParticipants();
    }

if (isset($_POST["partid"])) {
        $selpartid=$_POST["partid"];
        }
    else {
        $selpartid=0;
        }

select_participant($selpartid, "CommentOnParticipants.php");

if ((!isset($_POST["partid"])) or ($_POST["partid"]==0)) {
    staff_footer();
    exit();
    }
?>
<?php echo "<HR>&nbsp;<BR>\n"; ?>
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
