<?php
require_once('StaffCommonCode.php');
require_once('SubmitCommentOn.php');
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$title="Comment On Session";

staff_header($title);

$topsectiononly=true; // no room selected -- flag indicates to display only the top section of the page
if (isset($_POST["comment"])) {
    SubmitCommentOnSessions();
    }

if (isset($_POST["selsess"])) {
        $selsessionid=$_POST["selsess"];
        $topsectiononly=false;
        }
    elseif (isset($_GET["selsess"])) { // room was select by external page such as a report
        $selsessionid=$_GET["selsess"];
        $topsectiononly=false;
        }
    else {
        $selsessionid=0; // room was not yet selected.
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }

$query="SELECT T.trackname, S.sessionid, S.title FROM Sessions AS S ";
$query.="JOIN $ReportDB.Tracks AS T USING (trackid) ";
$query.="JOIN $ReportDB.SessionStatuses AS SS USING (statusid) ";
$query.="WHERE SS.may_be_scheduled=1 ";
$query.="ORDER BY T.trackname, S.sessionid, S.title";
if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<FORM name=\"selsesform\" method=POST action=\"CommentOnSessions.php\">\n";
echo "<DIV><LABEL for=\"selsess\">Select Session</LABEL>\n";
echo "<SELECT name=\"selsess\">\n";
echo "     <OPTION value=0 ".(($selsessionid==0)?"selected":"").">Select Session</OPTION>\n";
while (list($trackname,$sessionid,$title)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$sessionid."\" ".(($selsessionid==$sessionid)?"selected":"");
    echo ">".htmlspecialchars($trackname)." - ".htmlspecialchars($sessionid);
    echo " - ".htmlspecialchars($title)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\">";
if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
    }
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
echo "</FORM>\n";
if ($topsectiononly) {
    staff_footer();
    exit();
    }
?>
<?php echo "<HR>&nbsp;<BR>\n"; ?>
<FORM name="sesscommentform" method=POST action="CommentOnSessions.php">
  <P>Comment on/for <?php echo htmlspecialchars($title)?> for 
<?php echo CON_NAME; ?>:
<INPUT type="hidden" name="sessionid" value="<?php echo $selsessionid; ?>">
<INPUT type="hidden" name="title" value="<?php echo $title; ?>">
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
