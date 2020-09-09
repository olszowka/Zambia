<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $congoinfo, $linki, $message2, $message_error, $participant, $title;
$title = "Search Results";
require('PartCommonCode.php'); // initialize db; check login; retrieve $badgeid
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');
$trackid = $_POST["track"];
$titlesearch = stripslashes($_POST["title"]);
// List of sessions that match search criteria 
// Includes sessions in which participant is already interested if they do match match search
// Use "Session Interests" page to just see everything in which you are interested
$queryArray["sessions"] = <<<EOD
SELECT
        S.sessionid, T.trackname, S.title,
        CASE
            WHEN (minute(S.duration)=0) THEN date_format(S.duration,'%l hr')
            WHEN (hour(S.duration)=0) THEN date_format(S.duration, '%i min')
            ELSE date_format(S.duration,'%l hr, %i min')
            END
            as duration,
        S.progguiddesc, S.persppartinfo, PSI.badgeid
    FROM
        Sessions S JOIN
        Tracks T USING (trackid) JOIN
        SessionStatuses SST USING (statusid) LEFT JOIN
                (SELECT
                         badgeid, sessionid
                     FROM
                         ParticipantSessionInterest
                     WHERE badgeid='$badgeid') as PSI USING (sessionid)
    WHERE
        SST.may_be_scheduled=1 AND
        S.Sessionid in
            (SELECT S2.Sessionid FROM
                     Sessions S2 JOIN
                     Tracks T USING (trackid) JOIN
                     Types Y USING (typeid)
                 WHERE
                     S2.invitedguest=0 AND
                     T.selfselect=1 AND
                     Y.selfselect=1
EOD;
    if ($trackid != 0) {
        $queryArray["sessions"] .= "                     AND S.trackid=$trackid\n";
        }
    if ($titlesearch != "") {
        $x=mysqli_real_escape_string($linki, $titlesearch);
        $queryArray["sessions"] .= "                     AND S.title LIKE \"%$x%\"\n";
        }
    $queryArray["sessions"] .= ") ORDER BY T.trackname, S.sessionid;";
    $queryArray["interested"] = <<<EOD
SELECT
        P.interested
    FROM
        Participants P
    WHERE
        P.badgeid = '$badgeid';
EOD;
	if (($resultXML = mysql_query_XML($queryArray)) === false) {
	    RenderError($message_error);
        exit();
        }
    $docNode = $resultXML->getElementsByTagName("doc")->item(0);
    $variablesNode = $resultXML->createElement("variables");
    $variablesNode = $docNode->appendChild($variablesNode);
    $variablesNode->setAttribute("may_I", may_I('my_panel_interests') ? "1" : "0");
    $variablesNode->setAttribute("conName", CON_NAME);
    participant_header($title);
    RenderXSLT('SearchMySessions1.xsl', array(), $resultXML);
	participant_footer();
?>
