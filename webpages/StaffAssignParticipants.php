<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Assign Participants";
require_once('StaffCommonCode.php');
require_once('StaffAssignParticipants_FNC.php');
staff_header($title);

$topsectiononly = true; // no room selected -- flag indicates to display only the top section of the page
if (isset($_POST["numrows"])) {
    SubmitAssignParticipants();
}
$selsessionid = getInt("selsess", 0);
if ($selsessionid != 0) {
    $topsectiononly = false;
} else {
    unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
}
$query = <<<EOD
SELECT
        T.trackname, S.sessionid, S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
    WHERE
        SS.may_be_scheduled=1
    ORDER BY
        T.trackname, S.sessionid, S.title;
EOD;
$Sresult = mysqli_query_exit_on_error($query);
echo "<form id=\"selsesformtop\" name=\"selsesform\" class=\"form-inline\" method=\"get\" action=\"StaffAssignParticipants.php\">\n";
echo "<div><label for=\"selsess\">Select Session:</label>\n";
echo "<select id=\"sessionDropdown\" class=\"span6\" name=\"selsess\">\n";
echo "     <option value=0" . (($selsessionid == 0) ? "selected" : "") . ">Select Session</option>\n";
while (list($trackname, $sessionid, $title) = mysqli_fetch_array($Sresult, MYSQLI_NUM)) {
    echo "     <option value=\"$sessionid\" " . (($selsessionid == $sessionid) ? "selected" : "");
    echo ">" . htmlspecialchars($trackname) . " - ";
    echo htmlspecialchars($sessionid) . " - " . htmlspecialchars($title) . "</option>\n";
}
mysqli_free_result($Sresult);
echo "</select>\n";
echo "<button id=\"sessionBtn\" type=\"submit\" name=\"submit\" class=\"btn btn-primary\">Select Session</button>\n";
if (isset($_SESSION['return_to_page'])) {
    echo "<a href=\"" . $_SESSION['return_to_page'] . "\">Return to report</a>";
}
echo "</div></form>\n";
if ($topsectiononly) {
    staff_footer();
    exit();
}
$queryArray["timestampsetup1"] = "SET @maxcr = (SELECT max(createdts) FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid);";
$queryArray["timestampsetup2"] = "SET @maxin = (SELECT max(inactivatedts) FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid);";
$queryArray["maxtimestamp"] = "SELECT IF(@maxcr IS NULL, @maxin, IF(@maxin IS NULL, @maxcr, IF(@maxcr > @maxin, @maxcr, @maxin))) AS maxtimestamp;";
$queryArray["sessionInfo"] = <<<EOD
SELECT
		sessionid, title, progguiddesc, persppartinfo, notesforpart, notesforprog
	FROM
		Sessions
	WHERE
		sessionid=$selsessionid;
EOD;
$queryArray["participantInterest"] = <<<EOD
SELECT
		POS.badgeid AS posbadgeid,
		COALESCE(POS.moderator, 0) AS moderator,
		P.badgeid,
		P.pubsname,
		P.staff_notes,
		IFNULL(PSI.rank, 99) AS rank,
		PSI.willmoderate,
		PSI.comments,
		P.bio,
		PHR.roleid
	FROM
					Participants AS P
			JOIN
					(SELECT DISTINCT badgeid, sessionid FROM
						(SELECT badgeid, sessionid FROM ParticipantOnSession WHERE sessionid=$selsessionid
						UNION
						SELECT badgeid, sessionid FROM ParticipantSessionInterest WHERE sessionid=$selsessionid) AS R2
						) AS R USING (badgeid)
		LEFT JOIN	ParticipantSessionInterest AS PSI ON R.badgeid = PSI.badgeid AND R.sessionid = PSI.sessionid
		LEFT JOIN	ParticipantOnSession AS POS ON R.badgeid = POS.badgeid AND R.sessionid = POS.sessionid
		LEFT JOIN	ParticipantHasRole AS PHR ON P.badgeid = PHR.badgeid and PHR.roleid = 10 /* moderator */
	WHERE
			POS.sessionid = $selsessionid
		OR	POS.sessionid IS NULL
	ORDER BY
		moderator DESC,
		IFNULL(POS.badgeid, '~') ASC,
		rank ASC,
		P.pubsname ASC;
EOD;
if (($resultXML = mysql_query_XML($queryArray)) === false) {
    $message = $query . "<br>Error querying database. Unable to continue.<br>";
    echo "<p class\"alert alert-error\">" . $message . "</p>\n";
    staff_footer();
    exit();
}
$otherParticipantsQuery = <<<EOD
SELECT
        P.pubsname, P.badgeid, CD.lastname
    FROM
        Participants P
    JOIN
        CongoDump CD USING(badgeid)
    WHERE
            P.interested = 1
        AND NOT EXISTS (
			SELECT *
				FROM
					ParticipantSessionInterest
				WHERE
						sessionid = $selsessionid
					AND badgeid = P.badgeid
            );
EOD;
$otherParticipantsResult = mysqli_query_exit_on_error($otherParticipantsQuery);

$docNode = $resultXML->getElementsByTagName("doc")->item(0);

$queryNode = $resultXML->createElement("query");
$queryNode = $docNode->appendChild($queryNode);
$queryNode->setAttribute("queryName", "otherParticipants");
$regexArr = array();
while ($row = mysqli_fetch_assoc($otherParticipantsResult)) {
    $rowNode = $resultXML->createElement("row");
    $rowNode = $queryNode->appendChild($rowNode);
    $badgeid = $row["badgeid"];
    $rowNode->setAttribute("badgeid", $badgeid);
    $pubsname = $row["pubsname"];
    if (mb_ereg_match("\w", $pubsname)) {
        $pattern = "(.*)(\b" . preg_quote($row["lastname"]) . "\b)(.*)";
        if (mb_ereg($pattern, $pubsname, $regexArr)) {
            $sortableName = $regexArr[2] . ($regexArr[3] ? $regexArr[3] : "") . ", " . $regexArr[1];
        } else {
            $sortableName = $pubsname;
        }
    } else {
        $sortableName = $pubsname;
    }
    $rowNode->setAttribute("sortableName", $sortableName);
    $rowNode->setAttribute("sortableNameLc", mb_convert_case($sortableName, MB_CASE_LOWER));
}

$parametersNode = $resultXML->createElement("parameters");
$parametersNode = $docNode->appendChild($parametersNode);
if (may_I('EditSesNtsAsgnPartPg')) {
    $parametersNode->setAttribute("editSessionNotes", "true");
}
//echo($resultXML->saveXML()); //for debugging only
$xsl = new DomDocument;
$xsl->load('xsl/StaffAssignParticipants.xsl');
$xslt = new XsltProcessor();
$xslt->importStylesheet($xsl);
$html = $xslt->transformToXML($resultXML);
echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
staff_footer();
?>
