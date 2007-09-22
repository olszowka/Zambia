<?php
$title="Assign Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('StaffAssignParticipants_FNC.php');

staff_header($title);

if (isset($_POST["numrows"])) {
    SubmitAssignParticipants();
    }

if (isset($_POST["selsess"])) {
        $selsessionid=$_POST["selsess"];
        }
    else {
        $selsessionid=0;
        }
$query="SELECT T.trackname, S.sessionid, S.title FROM Sessions AS S, Tracks AS T WHERE ";
$query.="S.trackid = T.trackid AND (S.statusid in (2,3,7)) ORDER BY T.trackname, S.sessionid";
if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<FORM name=\"selsesform\" method=POST action=\"StaffAssignParticipants.php\">\n";
echo "<DIV><LABEL for=\"selsess\">Select Session</LABEL>\n";
echo "<SELECT name=\"selsess\">\n";
echo "     <OPTION value=0 ".(($selsessionid==0)?"selected":"").">Select Session</OPTION>\n";
while (list($trackname,$sessionid,$title)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$sessionid."\" ".(($selsessionid==$sessionid)?"selected":"");
    echo ")>".htmlspecialchars($trackname)." - ";
    echo htmlspecialchars($sessionid)." - ".htmlspecialchars($title)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
echo "</FORM>\n";
echo "<HR>&nbsp;<BR>\n";
if ((!isset($_POST["selsess"])) or ($_POST["selsess"]==0)) {
    staff_footer();
    exit();
    }
$query = <<<EOD
SELECT title, progguiddesc, persppartinfo, notesforpart, notesforprog FROM Sessions
WHERE sessionid=$selsessionid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<H2>$selsessionid - ".htmlspecialchars(mysql_result($result,0,"title"))."</H2>";    
echo "<P>Program Guide Text\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"progguiddesc"));
echo "\n";
echo "<P>Prospective Participant Info\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"persppartinfo"));
echo "\n";
echo "<P>Notes for Participant\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"notesforpart"));
echo "\n";
echo "<P>Notes for Program Staff\n";
echo "<P class=\"border1111 lrmargin lrpad\">";
echo htmlspecialchars(mysql_result($result,0,"notesforprog"));
echo "\n";
echo "<HR>\n";
$query = <<<EOD
SELECT POS.badgeid AS posbadgeid, POS.moderator, CD.badgeid, CD.badgename, PSI.rank, 
PSI.willmoderate, PSI.comments FROM CongoDump AS CD 
JOIN ParticipantSessionInterest AS PSI ON CD.badgeid=PSI.badgeid 
LEFT JOIN ParticipantOnSession AS POS ON (CD.badgeid=POS.badgeid
and PSI.sessionid=POS.sessionid) where PSI.sessionid=$selsessionid
ORDER BY POS.moderator DESC, POS.badgeid DESC, CD.badgename
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$query="SELECT C.lastname, C.firstname, C.badgename, C.badgeid FROM CongoDump AS C,";
$query.=" Participants AS P WHERE C.badgeid=P.badgeid AND P.interested=1 AND";
$query.=" C.badgeid not in (Select badgeid from ParticipantSessionInterest where";
$query.=" sessionid=$selsessionid) ORDER BY C.lastname";
if (!$Presult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    RenderError($title,$message);
    exit();
    }
$i=0;
$modid=0;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    if ($bigarray[$i]["moderator"]==1) {
        $modid=$bigarray[$i]["badgeid"];
        }
    $i++;
    }
$numrows=$i; 
echo "<FORM name=\"selsesform\" method=POST action=\"StaffAssignParticipants.php\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "<INPUT type=\"radio\" name=\"moderator\" id=\"moderator\" value=\"0\"".(($modid==0)?"checked":"").">";
echo "<LABEL for=\"moderator\">No Moderator Selected</LABEL>";
echo "<TABLE>\n";
for ($i=0;$i<$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"vatop\"><INPUT type=\"checkbox\" name=\"asgn".$bigarray[$i]["badgeid"]."\" ";
    echo (($bigarray[$i]["posbadgeid"])?"checked":"")." value=\"1\"></TD>";
    echo "      <TD class=\"vatop lrpad\">Assigned</TD>";
    echo "<INPUT type=\"hidden\" name=\"row$i\" value=\"".$bigarray[$i]["badgeid"]."\">";
    echo "<INPUT type=\"hidden\" name=\"wasasgn".$bigarray[$i]["badgeid"]."\" value=\"";
    echo ((isset($bigarray[$i]["posbadgeid"]))?1:0)."\">";
    echo "         </TD>\n";
    echo "      <TD class=\"vatop\">".$bigarray[$i]["badgeid"]."</TD>\n";
    echo "      <TD class=\"vatop\">".$bigarray[$i]["badgename"]."</TD>\n";
    echo "      <TD class=\"vatop\">Rank: ".$bigarray[$i]["rank"]."</TD>\n";
    echo "      <TD class=\"vatop\">".(($bigarray[$i]["willmoderate"]==1)?"Will moderate.":"Will not moderate.")."</TD>\n";
    echo "      </TR>\n";
    echo "   <TR>\n";
    echo "      <TD class=\"vatop\"><INPUT type=\"radio\" name=\"moderator\" id=\"moderator\"value=\"".$bigarray[$i]["badgeid"]."\" ";
    echo (($bigarray[$i]["moderator"])?"checked":"")."></TD>";
    echo "      <TD class=\"vatop lrpad\">Moderator</TD>";
    echo "      <TD colspan=4 class=\"border1111 lrpad\">".htmlspecialchars($bigarray[$i]["comments"]);
    echo "</TD>\n";
    echo "      </TR>\n";
    echo "   <TR><TD colspan=6>&nbsp;</TD></TR>\n";
    }
echo "</TABLE>";
echo "<INPUT type=\"hidden\" name=\"selsess\" value=\"$selsessionid\">\n";
echo "<INPUT type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<INPUT type=\"hidden\" name=\"wasmodid\" value=\"$modid\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "<HR>\n";
echo "<DIV><LABEL for=\"asgnpart\">Assign participant not indicated as interested or invited.</LABEL><BR>\n";
echo "<SELECT name=\"asgnpart\">\n";
echo "     <OPTION value=0 selected>Assign Participant</OPTION>\n";
while (list($lastname,$firstname,$badgename,$badgeid)= mysql_fetch_array($Presult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$badgeid."\">".htmlspecialchars($lastname).", ";
    echo htmlspecialchars($firstname)." (".htmlspecialchars($badgename).") - ";
    echo htmlspecialchars($badgeid)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BU
TTON></DIV>\n";

echo "</FORM>\n";
staff_footer();
?>
