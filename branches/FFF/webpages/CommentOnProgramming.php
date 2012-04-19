<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
require_once('SubmitCommentOn.php');
global $link;
$title="Comment On Programming";
$description="<P>Please add your comments about programming in the box, below</P>";

// Start page properly
topofpagereport($title,$description,$additionalinfo);

if (isset($_POST["comment"])) {
    SubmitCommentOnProgramming();
    }

?>
<FORM name="programcommentform" method=POST action="CommentOnProgramming.php">
  <P>Comment on <?php echo CON_NAME; ?>:
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
correct_footer();
?>
