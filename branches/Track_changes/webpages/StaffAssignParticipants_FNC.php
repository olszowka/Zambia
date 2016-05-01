<?php 
function SubmitAssignParticipants() {
	// NOTES
	// U - added to panel not as moderator
	// V - added to panel as moderator
	// W - removed from panel was moderator
	// X - removed from panel was not moderator
	// Y - changed to moderator
	// Z - changed to not moderator
    global $link;
	$message="";
	$userbadgeid = $_SESSION['badgeid'];
//    print_r($_POST);
    $asgnpart=filter_var($_POST["asgnpart"], FILTER_VALIDATE_INT);
    $numrows=$_POST["numrows"];
    $moderator=$_POST["moderator"];
    $wasmodid=$_POST["wasmodid"];
    $selsessionid=filter_var($_POST["selsess"], FILTER_VALIDATE_INT);
	// use XSL position for row#.  That starts at 1.
	// If moderator is changing, must inactivate old one before inserting new one because trigger will not
	//     permit more than 1 moderator for the session, even momentarily.
	$firstQueryArray = array("SET AUTOCOMMIT = 0;", "START TRANSACTION;", "SET @mynow = NOW();");
	$queryArray = [];
    for ($i=1; $i<=$numrows; $i++) {
        $badgeid=mysql_real_escape_string($_POST["row$i"], $link);
        $ismod=($moderator==$badgeid);
        $isasgn=(isset($_POST["asgn$badgeid"]) or $ismod);
        $wasasgn=($_POST["wasasgn$badgeid"]==1);
        $wasmod=($wasmodid==$badgeid);
//echo "i: $i | isasgn: $isasgn | wasasgn: $wasasgn | ismod: $ismod | wasmod: $wasmod <BR>\n";
		if ($wasmod and !$ismod) { // W or Z
				$firstQueryArray[] = "UPDATE ParticipantOnSessionHistory SET inactivatedbybadgeid=\"$userbadgeid\", inactivatedts=@mynow WHERE " .
				    "sessionid=$selsessionid AND badgeid=\"$badgeid\" AND inactivatedts IS NULL;";
				if ($isasgn) { // Z
					$firstQueryArray[] = "INSERT INTO ParticipantOnSessionHistory (badgeid, sessionid, moderator, createdts, createdbybadgeid) " .
					    "VALUES (\"$badgeid\", $selsessionid, 0, @mynow, \"$userbadgeid\");";
					}
				}
			else {
				if ($wasasgn and (!$isasgn or ($isasgn and $ismod and !$wasmod))) { // X or Y (inact only)
					$queryArray[] = "UPDATE ParticipantOnSessionHistory SET inactivatedbybadgeid=\"$userbadgeid\", inactivatedts=@mynow WHERE " .
					    "sessionid=$selsessionid AND badgeid=\"$badgeid\" AND inactivatedts IS NULL;";
					}
				if ($isasgn and (!$wasasgn or (!$wasmod and $ismod))) { // V or U or Y (ins only)
					$queryArray[] = "INSERT INTO ParticipantOnSessionHistory (badgeid, sessionid, moderator, createdts, createdbybadgeid) " .
						"VALUES (\"$badgeid\", $selsessionid, " . ($ismod ? "1" : "0") . ", @mynow, \"$userbadgeid\");";
					}
                }
		}
	$queryArray[] = "COMMIT;";
	$queryArray[] = "SET AUTOCOMMIT = 0;";
	foreach ($firstQueryArray as $query) {
		if (!mysql_query($query, $link)) {
			$foo = mysql_error($link);
			$message=$query."<BR>Error updating database.<BR>";
			echo "<P class=\"alert alert-error\">".$message."\n";
			staff_footer();
			exit();
			}
		}
	foreach ($queryArray as $query) {
		if (!mysql_query($query, $link)) {
			$foo = mysql_error($link);
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
