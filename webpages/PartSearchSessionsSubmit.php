<?php
// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $congoinfo, $linki, $message2, $message_error, $participant, $title;
$title = "Session Search Results";
require('PartCommonCode.php'); // initialize db; check login; retrieve $badgeid
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');
$trackid = getInt('track');
$tagArr = getArrayOfInts('tags');
$titlesearch = getString('title');
$tagmatch = getString('tagmatch');
// List of sessions that match search criteria 
// Includes sessions in which participant is already interested if they do match match search
// Use "Session Interests" page to just see everything in which you are interested
$queryArray["sessions"] = <<<EOD
SELECT
        S.sessionid, T.trackname, S.title, GROUP_CONCAT(TA.tagname ORDER BY TA.display_order SEPARATOR ', ') AS taglist,
        CASE
            WHEN (minute(S.duration)=0) THEN date_format(S.duration,'%l hr')
            WHEN (hour(S.duration)=0) THEN date_format(S.duration, '%i min')
            ELSE date_format(S.duration,'%l hr, %i min')
            END
            AS duration,
        TY.typename, S.progguiddesc, S.persppartinfo, PSI.badgeid
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN Types TY USING (typeid)
             JOIN SessionStatuses SST USING (statusid)
        LEFT JOIN
                  (SELECT
                          badgeid, sessionid
                      FROM
                          ParticipantSessionInterest
                      WHERE badgeid='$badgeid'
                  ) as PSI USING (sessionid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
        LEFT JOIN Tags TA USING (tagid)
    WHERE
            SST.may_be_scheduled=1
        AND S.Sessionid in
            (SELECT
                    S2.Sessionid
                FROM
                         Sessions S2
                    JOIN Tracks T USING (trackid)
                    JOIN Types Y USING (typeid)
                WHERE
                         S2.invitedguest=0
                     AND T.selfselect=1
                     AND Y.selfselect=1
EOD;
if ($trackid !== false && $trackid != 0) {
    $queryArray["sessions"] .= "                     AND S2.trackid=$trackid\n";
}
if (!empty($titlesearch)) {
    $x = mysqli_real_escape_string($linki, $titlesearch);
    $queryArray["sessions"] .= "                     AND S2.title LIKE \"%$x%\"\n";
}
if ($tagArr !== false && count($tagArr) > 0) {
    if ($tagmatch =='all') {
        foreach ($tagArr as $tag) {
            $queryArray["sessions"] .= " AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S2.sessionid AND tagid = $tag)";
        }
    } else {
        $tagidList = implode(',', $tagArr);
        $queryArray["sessions"] .= " AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S2.sessionid AND tagid IN ($tagidList))";
    }
}
$queryArray["sessions"] .= ") GROUP BY S.sessionid ORDER BY T.trackname, S.sessionid;";
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
$paramArray = array();
$paramArray['may_I'] = may_I('my_panel_interests') ? "1" : "0";
$paramArray['conName'] = CON_NAME;
$paramArray["trackIsPrimary"] = TRACK_TAG_USAGE === "TRACK_ONLY" || TRACK_TAG_USAGE === "TRACK_OVER_TAG";
$paramArray["showTrack"] = TRACK_TAG_USAGE !== "TAG_ONLY";
$paramArray["showTags"] = TRACK_TAG_USAGE !== "TRACK_ONLY";
participant_header($title, false, 'Normal', true);
echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
RenderXSLT('PartSearchSessionsSubmit.xsl', $paramArray, $resultXML);
participant_footer();
?>
