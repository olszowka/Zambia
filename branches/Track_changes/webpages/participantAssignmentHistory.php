<?php
//	$Header: https://svn.code.sf.net/p/zambia/code/branches/arisia_prod/webpages/StaffAssignParticipants.php 1150 2015-11-21 22:40:54Z polszowka $
//	Created by Peter Olszowka on 2016-05-11;
//	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.

$title="Participant Assignment History";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');

staff_header($title);

$queryArray = [];
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
	$queryArray["edits"]=<<<EOD
SELECT
		POSH.badgeid,
		COALESCE(POSH.moderator, 0) AS moderator,
		POSH.createdbybadgeid,
		POSH.createdts,
		DATE_FORMAT(POSH.createdts, "%c/%e/%y %l:%i %p") AS createdtsFormat,
		POSH.inactivatedbybadgeid,
		POSH.inactivatedts,
		DATE_FORMAT(POSH.inactivatedts, "%c/%e/%y %l:%i %p") AS inactivatedtsFormat,
		PartOS.pubsname,
		PartCR.pubsname AS crPubsname,
		PartInact.pubsname AS inactPubsname
	FROM
				  ParticipantOnSessionHistory POSH
			 JOIN Participants PartOS ON PartOS.badgeid = POSH.badgeid
			 JOIN Participants PartCR ON PartCR.badgeid = POSH.createdbybadgeid
		LEFT JOIN Participants PartInact ON PartInact.badgeid = POSH.inactivatedbybadgeid
	WHERE
		POSH.sessionid=$selsessionid;
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
$xsl->load('xsl/participantAssignmentHistory.xsl');
$xslt = new XsltProcessor();
$xslt->importStylesheet($xsl);
$html = $xslt->transformToXML($resultXML);
echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
staff_footer();
?>
