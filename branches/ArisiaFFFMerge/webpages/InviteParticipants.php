<?php
$title="Invite Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
staff_header($title);

if (isset($_POST["selpart"])) {
    $partbadgeid=$_POST["selpart"];
    $sessionid=$_POST["selsess"];
    if (($partbadgeid==0) || ($sessionid==0)) {
            echo "<P class=\"errmsg\">Database not updated.  Select a participant and a session.</P>";
            }
        else {    
            $query="INSERT INTO ParticipantSessionInterest SET badgeid=\"".$partbadgeid."\", ";
            $query.="sessionid=".$sessionid;
            $result=mysql_query($query,$link);
            if ($result) {
                    echo "<P class=\"regmsg\">Database successfully updated.</P>\n";
                    }
                elseif (mysql_errno($link)==1062) {
                    echo "<P class=\"errmsg\">Database not updated.  That participant was already invited to that session.</P>";
                    }
                else {
                    echo $query."<P class=\"errmsg\">Database not updated.</P>";
                    }
                
            }        
    }
$query = <<<EOD
SELECT
            CD.lastname,
            CD.firstname,
            CD.badgename,
            P.badgeid,
            P.pubsname
    FROM
            Participants P
       JOIN CongoDump CD USING(badgeid)
    WHERE
            P.interested=1
    ORDER BY
            IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
if (!$Presult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    RenderError($title,$message);
    exit();
    }
$query="SELECT T.trackname, S.sessionid, S.title FROM Sessions AS S ";
$query.="JOIN Tracks AS T USING (trackid) ";
$query.="JOIN SessionStatuses AS SS USING (statusid) ";
$query.="WHERE SS.may_be_scheduled=1 ";
$query.="ORDER BY T.trackname, S.sessionid, S.title";
if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    RenderError($title,$message);
    exit();
    }
echo "<p>Use this tool to put sessions marked \"invited guests only\" on a participant's interest list.\n";
echo "<FORM name=\"invform\" method=POST action=\"InviteParticipants.php\">";
echo "<DIV><LABEL for=\"selpart\">Select Participant</LABEL>\n";
echo "<SELECT name=\"selpart\">\n";
echo "     <OPTION value=0 selected>Select Participant</OPTION>\n";
while (list($lastname,$firstname,$badgename,$badgeid,$pubsname)= mysql_fetch_array($Presult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$badgeid."\">";
    if ($pubsname!="") {
	        echo htmlspecialchars($pubsname);
            }
	    else {
		    echo htmlspecialchars($lastname).", ";
            echo htmlspecialchars($firstname);
            }
    echo " (".htmlspecialchars($badgename).") - ";
    echo htmlspecialchars($badgeid)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;";
echo "<DIV><LABEL for=\"selsess\">Select Session</LABEL>\n";
echo "<SELECT name=\"selsess\">\n";
echo "     <OPTION value=0 selected>Select Session</OPTION>\n";
while (list($trackname,$sessionid,$title)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$sessionid."\">".htmlspecialchars($trackname)." - ";
    echo htmlspecialchars($sessionid)." - ".htmlspecialchars($title)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;";
echo "<DIV class=\"SubmitButton\"><BUTTON type=\"submit\" name=\"Invite\" >Invite</BUTTON></DIV>";
echo "</FORM>";
staff_footer(); ?>
