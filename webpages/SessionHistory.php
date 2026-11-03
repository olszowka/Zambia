<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Session History";
require_once('StaffCommonCode.php');

staff_header($title, 'bs5');

$queryArray = array();
$selsessionid = getInt('selsess', 0);
$queryArray["chooseSession"] = <<<EOD
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
if (($resultXML=mysql_query_XML($queryArray)) === false) {
    $message="Error querying database. Unable to continue.<br>";
    echo "<p class\"alert alert-error\">$message</p>\n";
    staff_footer();
    exit();
}
if ($selsessionid != 0) {
    $querySQLArr = array();
    $queryParamTypesArr = array();
    $queryParamsArr = array();

    $querySQLArr["title"] = <<<EOD
SELECT title FROM Sessions WHERE sessionid = ?;
EOD;
    $queryParamTypesArr["title"] = "i";
    $queryParamsArr["title"] = array($selsessionid);

    $querySQLArr["timestamps"]=<<<EOD
(SELECT createdts AS timestamp FROM ParticipantOnSessionHistory WHERE sessionid = ?)
    UNION
(SELECT inactivatedts AS timestamp FROM ParticipantOnSessionHistory WHERE sessionid = ?)
    UNION
(SELECT timestamp FROM SessionEditHistory WHERE sessionid = ?)
ORDER BY timestamp DESC;
EOD;
    $queryParamTypesArr["timestamps"] = "iii";
    $queryParamsArr["timestamps"] = array($selsessionid, $selsessionid, $selsessionid);

    $querySQLArr["currentAssignments"] = <<<EOD
SELECT
        COALESCE(POS.moderator, 0) AS moderator,
        P.badgeid,
        P.pubsname
    FROM
             ParticipantOnSession POS
        JOIN Participants P USING (badgeid)
    WHERE
        POS.sessionid = ?
    ORDER BY
        moderator DESC;
EOD;
    $queryParamTypesArr["currentAssignments"] = "i";
    $queryParamsArr["currentAssignments"] = array($selsessionid);

    $querySQLArr["participantedits"] = <<<EOD
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
        POSH.sessionid = ?;
EOD;
    $queryParamTypesArr["participantedits"] = "i";
    $queryParamsArr["participantedits"] = array($selsessionid);

    $querySQLArr["sessionedits"] = <<<EOD
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
        SEH.sessionid = ?;
EOD;
    $queryParamTypesArr["sessionedits"] = "i";
    $queryParamsArr["sessionedits"] = array($selsessionid);

    if (!$detailXML = mysql_prepare_query_XML($querySQLArr, $queryParamTypesArr, $queryParamsArr)) {
        exit(); // Should have exited already
    }
    $docNode = $resultXML->getElementsByTagName("doc")->item(0);
    foreach ($detailXML->getElementsByTagName("doc")->item(0)->childNodes as $queryNode) {
        $docNode->appendChild($resultXML->importNode($queryNode, true));
    }
}
$parametersNode = $resultXML->createElement("parameters");
$docNode = $resultXML->getElementsByTagName("doc")->item(0);
$parametersNode = $docNode->appendChild($parametersNode);
$parametersNode->setAttribute("selsessionid", $selsessionid);
RenderXSLT('SessionHistory.xsl', array(), $resultXML);
staff_footer();
?>
