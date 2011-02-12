<?php
$title="Comment On Programming";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('SubmitCommentOn.php');

staff_header($title);

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
staff_footer();
?>
