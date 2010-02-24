<?php
$title="Comment On Participant";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
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
$query="SELECT P.badgeid, CD.lastname, CD.firstname, CD.badgename FROM Participants P, CongoDump CD ";
$query.="where P.badgeid = CD.badgeid ORDER BY CD.lastname";
if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<FORM name=\"selpartform\" method=POST action=\"CommentOnParticipants.php\">\n";
echo "<DIV><LABEL for=\"partid\">Select Participant</LABEL>\n";
echo "<SELECT name=\"partid\">\n";
echo "     <OPTION value=0 ".(($selpartid==0)?"selected":"").">Select Participant</OPTION>\n";
while (list($partid,$lastname,$firstname,$badgename)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$partid."\" ".(($selpartid==$partid)?"selected":"");
    echo ">".htmlspecialchars($lastname).", ".htmlspecialchars($firstname);
    echo " (".htmlspecialchars($badgename).") - ".$partid."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
echo "</FORM>\n";
if ((!isset($_POST["partid"])) or ($_POST["partid"]==0)) {
    staff_footer();
    exit();
    }
?>
<?php echo "<HR>&nbsp;<BR>\n"; ?>
<FORM name="partadminform" method=POST action="CommentOnParticipants.php">
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
