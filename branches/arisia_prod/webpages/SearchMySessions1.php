<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Search Results";
    require ('PartCommonCode.php'); // initialize db; check login; retrieve $badgeid
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    $trackid=$_POST["track"];
    $titlesearch=stripslashes($_POST["title"]);
// List of sessions that match search criteria 
// Includes sessions in which participant is already interested if they do match match search
// Use "My Panel Interests" page to just see everything in which you are interested
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
    if ($trackid!=0) {
        $queryArray["sessions"].="                     AND S.trackid=$trackid\n";
        }
    if ($titlesearch!="") {
        $x=mysql_real_escape_string($titlesearch,$link);
        $queryArray["sessions"].="                     AND S.title LIKE \"%$x%\"\n";
        }
    $queryArray["sessions"].=") ORDER BY T.trackname, S.sessionid;";
	$queryArray["may_I"] = "select ".(may_I('my_panel_interests') ? "1" : "0"). " AS my_panel_interests;";
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	participant_header($title);
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('xsl/SearchMySessions1.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers do not support empty div, iframe, script and textarea tags
	participant_footer();
?>
