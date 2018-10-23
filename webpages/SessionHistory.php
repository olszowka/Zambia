<?php
// Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title="Session History";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');

staff_header($title);

$queryArray = array();
if (isset($_POST["selsess"])) {
        $selsessionid=filter_var($_POST["selsess"], FILTER_VALIDATE_INT);
		}
    elseif (isset($_GET["selsess"])) {
        $selsessionid=filter_var($_GET["selsess"], FILTER_VALIDATE_INT);
        }
    else {
        $selsessionid=0; // room was not yet selected.
        }
$queryArray["chooseSession"]=<<<EOD
SELECT
		T.trackname, S.sessionid, S.title
	FROM
			 Sessions S
		JOIN Tracks T USING (trackid)
		JOIN SessionStatuses SS USING (statusid)
	WHERE
		SS.may_be_scheduled = 1
	ORDER BY
		T.trackname, S.sessionid, S.title;
EOD;
if ($selsessionid != 0) {
	$queryArray["title"]=<<<EOD
SELECT title FROM Sessions WHERE sessionid = $selsessionid;
EOD;
	$queryArray["timestamps"]=<<<EOD
(SELECT createdts AS timestamp FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid)
	UNION
(SELECT inactivatedts AS timestamp FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid)
	UNION
(SELECT timestamp FROM SessionEditHistory WHERE sessionid = $selsessionid)
ORDER BY timestamp DESC;
EOD;
	$queryArray["currentAssignments"]=<<<EOD
SELECT
		COALESCE(POS.moderator, 0) AS moderator,
		P.badgeid,
		P.pubsname
	FROM
			 ParticipantOnSession POS
		JOIN Participants P USING (badgeid)
	WHERE
		POS.sessionid=$selsessionid
	ORDER BY
		moderator DESC;
EOD;
	$queryArray["participantedits"]=<<<EOD
SELECT
		POSH.badgeid,
		COALESCE(POSH.moderator, 0) AS moderator,
		POSH.createdbybadgeid,
		POSH.createdts,
		DATE_FORMAT(POSH.createdts, "%c/%e/%y %l:%i %p") AS createdtsformat,
		POSH.inactivatedbybadgeid,
		POSH.inactivatedts,
		DATE_FORMAT(POSH.inactivatedts, "%c/%e/%y %l:%i %p") AS inactivatedtsformat,
		PartOS.pubsname,
		PartCR.pubsname AS crpubsname,
		PartInact.pubsname AS inactpubsname
	FROM
				  ParticipantOnSessionHistory POSH
			 JOIN Participants PartOS ON PartOS.badgeid = POSH.badgeid
			 JOIN Participants PartCR ON PartCR.badgeid = POSH.createdbybadgeid
		LEFT JOIN Participants PartInact ON PartInact.badgeid = POSH.inactivatedbybadgeid
	WHERE
		POSH.sessionid=$selsessionid;
EOD;
	$queryArray["sessionedits"]=<<<EOD
SELECT
		SEH.badgeid,
		SEH.name,
		SEH.editdescription,
		SEH.timestamp,
		DATE_FORMAT(SEH.timestamp, "%c/%e/%y %l:%i %p") AS tsformat,
		SEC.description AS codedescription,
		SS.statusname
	FROM
			 SessionEditHistory SEH
		JOIN SessionEditCodes SEC USING (sessioneditcode)
		JOIN SessionStatuses SS USING (statusid)
	WHERE
		SEH.sessionid=$selsessionid;
EOD;
	}
if (($resultXML=mysql_query_XML($queryArray))===false) {
	$message="Error querying database. Unable to continue.<br>";
	echo "<p class\"alert alert-error\">$message</p>\n";
	staff_footer();
	exit();
	}
$parametersNode = $resultXML->createElement("parameters");
$docNode = $resultXML->getElementsByTagName("doc")->item(0);
$parametersNode = $docNode->appendChild($parametersNode);
$parametersNode->setAttribute("selsessionid", $selsessionid);
echo(mb_ereg_replace("<(row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
$xsl = new DomDocument;
$xsl->load('xsl/SessionHistory.xsl');
$xslt = new XsltProcessor();
$xslt->importStylesheet($xsl);
$html = $xslt->transformToXML($resultXML);
echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
staff_footer();
?>
