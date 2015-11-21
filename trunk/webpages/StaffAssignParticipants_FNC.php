<?php 
function SubmitAssignParticipants() {
    global $link;
	$message="";
//    print_r($_POST);
    $asgnpart=filter_var($_POST["asgnpart"], FILTER_VALIDATE_INT);
    $numrows=$_POST["numrows"];
    $moderator=$_POST["moderator"];
    $wasmodid=$_POST["wasmodid"];
    $selsessionid=filter_var($_POST["selsess"], FILTER_VALIDATE_INT);
	// use XSL position for row#.  That starts at 1.
    for ($i=1; $i<=$numrows; $i++) {
        $badgeid=filter_var($_POST["row$i"], FILTER_VALIDATE_INT);
        $ismod=($moderator==$badgeid);
        $isasgn=(isset($_POST["asgn$badgeid"]) or $ismod);
        $wasasgn=($_POST["wasasgn$badgeid"]==1);
        $wasmod=($wasmodid==$badgeid);
//echo "i: $i | isasgn: $isasgn | wasasgn: $wasasgn | ismod: $ismod | wasmod: $wasmod <BR>\n";        
        if (!$isasgn and $wasasgn) {
                $query="DELETE FROM ParticipantOnSession where badgeid=\"$badgeid\" ";
                $query.="and sessionid=$selsessionid";
                }
            elseif (!$wasasgn and $isasgn) {
                $query="INSERT INTO ParticipantOnSession set badgeid=\"$badgeid\", "; 
                $query.="sessionid=$selsessionid, moderator=".($ismod?1:0);
                }
            elseif (($ismod and !$wasmod) or (!$ismod and $wasmod)) {
                $query="UPDATE ParticipantOnSession set moderator=".($ismod?1:0);
                $query.=" WHERE badgeid=\"$badgeid\" and sessionid=$selsessionid";
                }
            else {
                continue;
                }
        if (!mysql_query($query, $link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"alert alert-error\">".$message."\n";
            staff_footer();
            exit();
            }
        }
	$message="Participant assignments updated. ";	
    if ($asgnpart!=0) {
        $query="INSERT INTO ParticipantSessionInterest SET badgeid=\"".$asgnpart."\", ";
        $query.="sessionid=".$selsessionid;
        $result=mysql_query($query, $link);
        if (!$result) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"alert alert-error\">".$message."\n";
            staff_footer();
            exit();
            }
        $query="INSERT INTO ParticipantOnSession set badgeid=\"$asgnpart\", ";
        $query.="sessionid=$selsessionid, moderator=0;";
        $result=mysql_query($query, $link);
//        error_log("Zambia query: $query\n");
        if (!$result) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"alert alert-error\">".$message."\n";
            staff_footer();
            exit();
            }
		$message.="Participant added to session. ";
		}
    if (isset($_POST["NPStext"])) {
		$NPStext=mysql_real_escape_string($_POST["NPStext"], $link);
		$query="UPDATE Sessions SET notesforprog = \"$NPStext\" WHERE sessionid = $selsessionid;";
		$result=mysql_query($query, $link);
		if (!$result) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"alert alert-error\">".$message."\n";
            staff_footer();
            exit();
            }
		$query=<<<EOD
INSERT INTO SessionEditHistory
		(sessionid, badgeid, name, email_address, sessioneditcode, editdescription, statusid)
	SELECT
			$selsessionid, CD.badgeid, CONCAT(CD.firstName, " ", CD.lastname), CD.email, 3,
				"Edit notes for program committee",
				(SELECT statusid FROM Sessions WHERE sessionid = $selsessionid)
		FROM
			CongoDump CD
		WHERE
			badgeid = "{$_SESSION['badgeid']}";
EOD;
		$result=mysql_query($query, $link);
		if (!$result) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"alert alert-error\">".$message."\n";
            staff_footer();
            exit();
            }
		$message.="Notes for program staff updated. ";	
		}
	echo "<P class=\"alert alert-success\">".$message."\n";	
	}
?>    
