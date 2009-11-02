<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Show Search Session Results";
    require ('PartCommonCode.php'); // initialize db; check login; retrieve $badgeid
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    $trackid=$_POST["track"];
    $titlesearch=stripslashes($_POST["title"]);
// List of sessions that match search criteria 
// Does not includes sessions in which participant is interested if they do match match search
// Use "My Panel Interests" page to just see everything in which you are interested
    $query = <<<EOD
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
        ParticipantSessionInterest PSI USING (sessionid)
    WHERE
        SST.may_be_scheduled=1 AND
        (PSI.badgeid=$badgeid OR PSI.badgeid is null) AND
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
        $query.="                     AND S.trackid=$trackid\n";
        }
    if ($titlesearch!="") {
        $x=mysql_real_escape_string($titlesearch,$link);
        $query.="                     AND S.title LIKE \"%$x%\"\n";
        }
    $query.=")\n";
    if (!$result=mysql_query($query,$link)) {
        $message=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    participant_header($title);
    //echo $query."<BR>\n";
    require ('RenderMySessions1.php');    
    RenderMySessions1($result);
    participant_footer();
    exit();
?>
