<?php
$title="Administer Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('SubmitAdminParticipants.php');

staff_header($title);

if (isset($_POST["interested"])) {
    SubmitAdminParticipants();
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
echo "<FORM name=\"selpartform\" method=POST action=\"AdminParticipants.php\">\n";
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
echo "<HR>\n";
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
<FORM name="partadminform" method=POST action="AdminParticipants.php">
<P>Participant is interested and available to participate in 
<?php echo CON_NAME; ?> programming:
<INPUT type="hidden" name="wasinterested" value="<?php echo $interested; ?>">
<INPUT type="hidden" name="partid" value="<?php echo $selpartid; ?>">
<SELECT name="interested" class="yesno">
            <OPTION value=0 <?php if ($interested!=1 and $interested!=2) {echo "selected";} ?> >&nbsp</OPTION>
            <OPTION value=1 <?php if ($interested==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($interested==2) {echo "selected";} ?> >No</OPTION></SELECT>
<B>*** Changing this to no will remove the particpant from all sessions. ***</B><BR>
    <div class="password">
      <span class="password2">Change Participant's Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="password"></span>
      </div>
    <div class="password">
      <span class="password2">Confirm New Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="cpassword"></span>
      </div>
    <div class="password">
      <span class="password2">Name for Publications&nbsp;</span>
      <span class="value"><INPUT type="text" size="30" name="pubsname" value="<?php
    echo htmlspecialchars($pubsname)."\">\n";?>
        </span>
      </div>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>
<?php
staff_footer();
?>
