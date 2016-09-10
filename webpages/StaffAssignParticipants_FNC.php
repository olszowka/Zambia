<?php
//	$Header$
//	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.

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
	if ($_POST["maxtimestamp"] != "") {
			$pageTimestamp = DateTime::createFromFormat('Y-m-d G:i:s', $_POST["maxtimestamp"]);
			}
		else {
			$pageTimestamp = "empty";
			}
    $asgnpart=filter_var($_POST["asgnpart"], FILTER_VALIDATE_INT);
    $numrows=$_POST["numrows"];
    $moderator=mysql_real_escape_string($_POST["moderator"], $link);
	if (!isset($_POST["asgn$moderator"])) {
		$moderator = "";
		}
    $wasmodid=mysql_real_escape_string($_POST["wasmodid"], $link);
    $selsessionid=filter_var($_POST["selsess"], FILTER_VALIDATE_INT);
	$query = "SET @maxcr = (SELECT max(createdts) FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid);";
	mysql_query_exit_on_error($query);
	$query = "SET @maxin = (SELECT max(inactivatedts) FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid);";
	mysql_query_exit_on_error($query);
	$query = "SELECT IF(@maxcr IS NULL, @maxin, IF(@maxin IS NULL, @maxcr, IF(@maxcr > @maxin, @maxcr, @maxin))) AS maxtimestamp;";
	$result = mysql_query_exit_on_error($query);
	$recentTimestampResult = mysql_result($result, 0);
	if ($recentTimestampResult != null) {
			$recentTimestamp = DateTime::createFromFormat('Y-m-d G:i:s', $recentTimestampResult);
			}
		else {
			$recentTimestamp = "empty";
			}
	if ($pageTimestamp != $recentTimestamp) {
			$badgeidQueryArray = array();
			$addParticipantArray = array();
			$removeParticipantArray = array();
			if ($moderator == $wasmodid) {
					$moderatorChangeCode = "";
					}
				else {
					if ($moderator == "") {
							$moderatorChangeCode = "RM"; // remove moderator
							$badgeidQueryArray[] = "'$wasmodid'";
							}
						elseif ($wasmodid == "") {
								$moderatorChangeCode = "AM"; // add moderator
								$badgeidQueryArray[] = "'$moderator'";
								}
						else {
							$moderatorChangeCode = "CM"; // change moderator
							$badgeidQueryArray[] = "'$wasmodid'";
							$badgeidQueryArray[] = "'$moderator'";
							}
					}
			for ($i=1; $i<=$numrows; $i++) {
				$badgeid=mysql_real_escape_string($_POST["row$i"], $link);
				$isasgn=isset($_POST["asgn$badgeid"]);
				$wasasgn=($_POST["wasasgn$badgeid"]==1);
				if ($isasgn && !$wasasgn) {
						$addParticipantArray[] = $badgeid;
						$badgeidQueryArray[] = "'$badgeid'";
						}
					 else if ($wasasgn && !$isasgn) {
						$removeParticipantArray[] = $badgeid;
						$badgeidQueryArray[] = "'$badgeid'";
						}
				}
			if ($asgnpart != 0) {
				$addParticipantArray[] = $asgnpart;
				$badgeidQueryArray[] = "'$asgnpart'";
				}
			$badgeidQueryString = join(',', $badgeidQueryArray);
			$queryArray = array();
			$queryArray["participants"] = "SELECT DISTINCT P.badgeid, P.pubsname FROM Participants P WHERE P.badgeid in ($badgeidQueryString);";
			if (($resultXML=mysql_query_XML($queryArray))===false) {
				$message=$query."<BR>Error querying database. Unable to continue.<BR>";
				echo "<P class\"alert alert-error\">".$message."\n";
				staff_footer();
				exit();
				}
			$docNode = $resultXML->getElementsByTagName("doc")->item(0);
			if ($moderatorChangeCode != "") {
				$moderatorNode = $resultXML->createElement("moderator");
				$moderatorNode = $docNode->appendChild($moderatorNode);
				$moderatorNode->setAttribute("changecode", $moderatorChangeCode);
				switch ($moderatorChangeCode) {
					case "RM":
						$moderatorNode->setAttribute("frommoderator", $wasmodid);
						break;
					case "AM":
						$moderatorNode->setAttribute("tomoderator", $moderator);
						break;
					case "CM":
						$moderatorNode->setAttribute("frommoderator", $wasmodid);
						$moderatorNode->setAttribute("tomoderator", $moderator);
						break;
					}
				}
			$participantChangeNode = $resultXML->createElement("participantchanges");
			//$docNode = $resultXML->getElementsByTagName("doc")->item(0);
			$participantChangeNode = $docNode->appendChild($participantChangeNode);
			foreach ($addParticipantArray as $addParticipant) {
				$addParticipantNode = $resultXML->createElement("addparticipant");
				$addParticipantNode = $participantChangeNode->appendChild($addParticipantNode);
				$addParticipantNode->setAttribute("badgeid", $addParticipant);
				}
			foreach ($removeParticipantArray as $removeParticipant) {
				$removeParticipantNode = $resultXML->createElement("removeparticipant");
				$removeParticipantNode = $participantChangeNode->appendChild($removeParticipantNode);
				$removeParticipantNode->setAttribute("badgeid", $removeParticipant);
				}
			echo($resultXML->saveXML()); //for debugging only
			$xsl = new DomDocument;
			$xsl->load('xsl/StaffAssignParticipantsBadTimestamp.xsl');
			$xslt = new XsltProcessor();
			$xslt->importStylesheet($xsl);
			$html = $xslt->transformToXML($resultXML);
			echo mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i");	
			} // close of timestamps don't match
		else { // timestamps match -- update db
			// use XSL position for row#.  That starts at 1.
			// If moderator is changing, must inactivate old one before inserting new one because trigger will not
			//     permit more than 1 moderator for the session, even momentarily.
			$firstQueryArray = array("SET AUTOCOMMIT = 0;", "START TRANSACTION;", "SET @mynow = NOW();");
			$queryArray = array();
			for ($i=1; $i<=$numrows; $i++) {
				$badgeid=mysql_real_escape_string($_POST["row$i"], $link);
				$ismod=($moderator==$badgeid);
				$isasgn=isset($_POST["asgn$badgeid"]);
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
			if ($asgnpart!=0) {
				$queryArray[] = "INSERT INTO ParticipantOnSessionHistory (badgeid, sessionid, moderator, createdts, createdbybadgeid) " .
								"VALUES (\"$asgnpart\", $selsessionid, 0, @mynow, \"$userbadgeid\");";
				}
				
				
				
			$queryArray[] = "COMMIT;";
			$queryArray[] = "SET AUTOCOMMIT = 0;";
			foreach ($firstQueryArray as $query) {
				mysql_query_exit_on_error($query);
				}
			foreach ($queryArray as $query) {
				mysql_query_exit_on_error($query);
				}
			$message="Participant assignments updated. ";
			if ($asgnpart!=0) {
				$query="INSERT INTO ParticipantSessionInterest set badgeid=\"$asgnpart\", sessionid=$selsessionid;";
				$result=mysql_query_exit_on_error($query);
				$message.="Participant added to session. ";
				}
			if (isset($_POST["NPStext"])) {
				$NPStext=mysql_real_escape_string($_POST["NPStext"], $link);
				$query="UPDATE Sessions SET notesforprog = \"$NPStext\" WHERE sessionid = $selsessionid;";
				$result=mysql_query_exit_on_error($query);
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
				$result=mysql_query_exit_on_error($query);
				$message.="Notes for program staff updated. ";	
				}
			if (isset($message_error)) {
				echo $message_error;
				}
			echo "<p class=\"alert alert-success\">$message</p>\n";
			} // close of timestamps match -- update db
	} // end of function SubmitAssignParticipants()
?>    
