<?php 
function SubmitAssignParticipants() {
    global $link;
//    print_r($_POST);
    $asgnpart=$_POST["asgnpart"];
    $numrows=$_POST["numrows"];
    $moderator=$_POST["moderator"];
    $volunteer=$_POST["volunteer"];
    $introducer=$_POST["introducer"];
    $aidedecamp=$_POST["aidedecamp"];
    $wasmodid=$_POST["wasmodid"];
    $wasvolid=$_POST["wasvolid"];
    $wasintid=$_POST["wasintid"];
    $wasaidid=$_POST["wasaidid"];
    $selsessionid=$_POST["selsess"];
    for ($i=0; $i<$numrows; $i++) {
        $badgeid=$_POST["row$i"];
        $ismod=($moderator==$badgeid);
	$isvol=($volunteer==$badgeid);
	$isint=($introducer==$badgeid);
	$isaid=($aidedecamp==$badgeid);
        $isasgn=(isset($_POST["asgn$badgeid"]) or $ismod or $isvol or $isint or $isaid);
        $wasasgn=($_POST["wasasgn$badgeid"]==1);
        $wasmod=($wasmodid==$badgeid);
        $wasvol=($wasvolid==$badgeid);
        $wasint=($wasintid==$badgeid);
        $wasaid=($wasaidid==$badgeid);
//echo "i: $i | isasgn: $isasgn | wasasgn: $wasasgn | ismod: $ismod | wasmod: $wasmod <BR>\n";        
        if (!$isasgn and $wasasgn) {
                $query="DELETE FROM ParticipantOnSession where badgeid=\"$badgeid\" ";
                $query.="and sessionid=$selsessionid";
                }
            elseif (!$wasasgn and $isasgn) {
                $query="INSERT INTO ParticipantOnSession set badgeid=\"$badgeid\", "; 
                $query.="sessionid=$selsessionid, moderator=".($ismod?1:0);
		$query.=", volunteer=".($isvol?1:0);
		$query.=", introducer=".($isint?1:0);
		$query.=", aidedecamp=".($isaid?1:0);
                }
            elseif (($ismod and !$wasmod) or (!$ismod and $wasmod) or
		    ($isvol and !$wasvol) or (!$isvol and $wasvol) or
		    ($isint and !$wasint) or (!$isint and $wasint) or
		    ($isaid and !$wasaid) or (!$isaid and $wasaid)) {
                $query="UPDATE ParticipantOnSession set moderator=".($ismod?1:0);
		$query.=", volunteer=".($isvol?1:0);
		$query.=", introducer=".($isint?1:0);
		$query.=", aidedecamp=".($isaid?1:0);
                $query.=" WHERE badgeid=\"$badgeid\" and sessionid=\"$selsessionid\"";
                }
            else {
                continue;
                }
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        }
    if ($asgnpart!=0) {
        $query="INSERT INTO ParticipantSessionInterest SET badgeid=\"".$asgnpart."\", ";
        $query.="sessionid=".$selsessionid;
        $result=mysql_query($query,$link);
        if (!$result) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $query="INSERT INTO ParticipantOnSession set badgeid=\"$asgnpart\", ";
        $query.="sessionid=$selsessionid, moderator=0, volunteer=0, introducer=0, aidedecamp=0;";
        $result=mysql_query($query,$link);
//        error_log("Zambia query: $query\n");
        if (!$result) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        }
    }


?>    
