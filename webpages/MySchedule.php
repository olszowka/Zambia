<?php
// Copyright (c) 2005-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "My Schedule";
require('PartCommonCode.php'); // initialize db; check login;
$CON_START_DATIM = CON_START_DATIM; //make it a variable so it will be substituted
$PROGRAM_EMAIL = PROGRAM_EMAIL; //make it a variable so it will be substituted
require_once('ParticipantHeader.php');
// require_once('renderMySessions2.php');
if (!may_I('my_schedule')) {
    $message_error = "You do not currently have permission to view this page.<br>\n";
    RenderError($message_error);
    exit();
}
// set $badgeid from session
$queryArr = array();
$queryArr["sessions"] = <<<EOD
SELECT
            POS.sessionid,
            T.trackname,
            S.title,
            R.roomname,
            S.progguiddesc,
            TY.typename,
            DATE_FORMAT(ADDTIME('$CON_START_DATIM', SCH.starttime),'%a %l:%i %p') AS starttime,
            left(S.duration, 5) AS duration,
            S.persppartinfo,
            S.notesforpart
    FROM
            ParticipantOnSession POS
       JOIN Sessions S USING (sessionid)
       JOIN Schedule SCH USING (sessionid)
       JOIN Rooms R USING (roomid)
       JOIN Tracks T USING (trackid)
       JOIN Types TY USING (typeid)
    WHERE
            POS.badgeid='$badgeid'
    ORDER BY
            SCH.starttime;
EOD;
$queryArr["participants"] = <<<EOD
SELECT
        POS.sessionid,
        CD.badgename,
        P.pubsname,
        P.sortedpubsname,
        IF (P.share_email=1, CD.email, NULL) AS email,
        POS.moderator,
        PSI.comments
    FROM
                  ParticipantOnSession POS
             JOIN CongoDump CD USING(badgeid)
             JOIN Participants P USING(badgeid)
        LEFT JOIN ParticipantSessionInterest PSI USING(sessionid, badgeid)
    WHERE
        POS.sessionid IN (
            SELECT sessionid FROM ParticipantOnSession WHERE badgeid='$badgeid'
        )
    ORDER BY sessionid, moderator DESC;
EOD;
$resultXML = mysql_query_XML($queryArr);
if (!$resultXML) {
    RenderErrorAjax($message_error);
    exit();
}
//echo($resultXML->saveXML());
//exit();
$query = "SELECT message FROM CongoDump C LEFT JOIN RegTypes R USING (regtype) ";
$query .= "WHERE C.badgeid='$badgeid'";
if (!$result = mysqli_query_with_error_handling($query)) {
    exit(); // Should have exited already
}
$row = mysqli_fetch_array($result, MYSQLI_NUM);
mysqli_free_result($result);
$regmessage = $row[0];
$query = "SELECT count(*) FROM ParticipantOnSession POS JOIN Schedule SCH USING (sessionid) ";
$query .= "WHERE badgeid='$badgeid'";
if (!$result = mysqli_query_with_error_handling($query)) {
    exit(); // Should have exited already
}
$row = mysqli_fetch_array($result, MYSQLI_NUM);
mysqli_free_result($result);
$poscount = $row[0];
if (!$regmessage) {
    if ($poscount >= 3) {
        $regmessage = "not registered.</span><span> " . fetchCustomText("enough_panels");
    } else {
        $regmessage = "not registered.</span><span> " . fetchCustomText("not_enough_panels");
    }
}
participant_header($title, false, 'Normal', true);
echo "<div class=\"alert alert-primary mt-2\"><p>Below is the list of all the panels for which you are scheduled.  If you need any changes";
echo " to this schedule please contact <a class=\"alert-link\" href=\"mailto:$PROGRAM_EMAIL\"> Programming </a>.</p>\n";
echo fetchCustomText("all_panelists_1");
echo fetchCustomText("all_panelists_2");
echo "<p>Your registration status is <span class=\"hilit\">$regmessage</span>\n";
echo "<p>Thank you -- <a class=\"alert-link\" href=\"mailto:$PROGRAM_EMAIL\"> Programming </a></div>\n";
?>

<div class="card">
    <div class="card-body">
<?php
RenderXSLT('my_schedule.xsl', array(), $resultXML);
?>
    </div>
</div>
<?php
participant_footer();
?>
