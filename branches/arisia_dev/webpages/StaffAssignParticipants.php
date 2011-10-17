<?php
$title="Staff - Assign Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('StaffAssignParticipants_FNC.php');

staff_header($title);

$topsectiononly=true; // no room selected -- flag indicates to display only the top section of the page
if (isset($_POST["numrows"])) {
    SubmitAssignParticipants();
    }

if (isset($_POST["selsess"])) { // room was selected by this form
        $selsessionid=$_POST["selsess"];
        $topsectiononly=false;
        //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
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
$query.="JOIN Tracks AS T USING (trackid) ";
$query.="JOIN SessionStatuses AS SS USING (statusid) ";
$query.="WHERE SS.may_be_scheduled=1 ";
$query.="ORDER BY T.trackname, S.sessionid, S.title";
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
echo "<DIV class=\"SubmitDiv\">";
if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
    }
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
echo "</FORM>\n";
echo "<HR>&nbsp;<BR>\n";
if ($topsectiononly) {
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
SELECT
            POS.badgeid AS posbadgeid,
            POS.moderator,
            P.badgeid,
            P.pubsname,
			P.staff_notes,
            PSI.rank,
            PSI.willmoderate,
            PSI.comments
    FROM
            Participants AS P
       JOIN
(select distinct badgeid, sessionid from
(select badgeid, sessionid from ParticipantOnSession where sessionid=$selsessionid
    union
select badgeid, sessionid from ParticipantSessionInterest where sessionid=$selsessionid) as R2) as R
        using (badgeid)
  LEFT JOIN ParticipantSessionInterest AS PSI
        on R.badgeid = PSI.badgeid and R.sessionid = PSI.sessionid
  LEFT JOIN ParticipantOnSession AS POS
        on R.badgeid = POS.badgeid and R.sessionid = POS.sessionid
    where
        POS.sessionid=$selsessionid or POS.sessionid is null;
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$query = <<<EOD
SELECT
            P.pubsname,
            P.badgeid,
            CD.lastname
    FROM
            Participants P
       JOIN CongoDump CD USING(badgeid)
    WHERE
            P.interested=1
        AND P.badgeid not in
                   (Select badgeid
                        from ParticipantSessionInterest
                       where sessionid=$selsessionid)
    ORDER BY
            IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
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
    echo "      <TD class=\"vatop\">".$bigarray[$i]["pubsname"]."</TD>\n";
    echo "      <TD class=\"vatop\">Rank: ".$bigarray[$i]["rank"]."</TD>\n";
    echo "      <TD class=\"vatop\">".(($bigarray[$i]["willmoderate"]==1)?"Volunteered to moderate.":"")."</TD>\n";
    echo "      </TR>\n";
    echo "   <TR>\n";
    echo "      <TD class=\"vatop\"><INPUT type=\"radio\" name=\"moderator\" id=\"moderator\"value=\"".$bigarray[$i]["badgeid"]."\" ";
    echo (($bigarray[$i]["moderator"])?"checked":"")."></TD>";
    echo "      <TD class=\"vatop lrpad\">Moderator</TD>";
    echo "      <TD colspan=4 class=\"border1111 lrpad\">".htmlspecialchars($bigarray[$i]["comments"]);
    echo "</TD>\n";
    echo "      </TR>\n";
	if ($bigarray[$i]["staff_notes"]) {
		echo "      <tr><td>&nbsp;</td><td>&nbsp;</td><td colspan=\"4\">".htmlspecialchars($bigarray[$i]["staff_notes"]);
		echo "</td></tr>\n";
		}
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
while (list($pubsname,$badgeid)= mysql_fetch_array($Presult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$badgeid."\">";
    echo htmlspecialchars($pubsname)." - ";
    echo htmlspecialchars($badgeid)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Add</BUTTON></DIV>\n";

echo "</FORM>\n";
staff_footer();
?>
