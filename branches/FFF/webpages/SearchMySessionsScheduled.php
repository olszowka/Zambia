<?php
global $participant,$message_error,$message2,$congoinfo;
require_once('PartCommonCode.php');

$title="Show Search Session Results";
// initialize db; check login; retrieve $badgeid
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$ConStartDatim=CON_START_DATIM;
$trackid=$_POST["track"];
$titlesearch=stripslashes($_POST["title"]);
// List of sessions that match search criteria 
// Does not includes sessions in which participant is interested if they do match match search
// Use "My Panel Interests" page to just see everything in which you are interested
$query = <<<EOD
SELECT
        S.sessionid,
        T.trackname,
        S.title,
        CASE
            WHEN (minute(S.duration)=0) AND (SCH.starttime) THEN CONCAT(DATE_FORMAT(S.duration,'%l hr starting '), DATE_FORMAT(ADDTIME('$ConStartDatim', SCH.starttime), '%a %l:%i %p'))
            WHEN (minute(S.duration)=0) THEN DATE_FORMAT(S.duration,'%l hr')
            WHEN (hour(S.duration)=0) AND (SCH.starttime) THEN CONCAT(DATE_FORMAT(S.duration, '%i min starting '), DATE_FORMAT(ADDTIME('$ConStartDatim', SCH.starttime), '%a %l:%i %p'))
            WHEN (hour(S.duration)=0) THEN DATE_FORMAT(S.duration, '%i min')
            WHEN (SCH.starttime) THEN CONCAT(DATE_FORMAT(S.duration,'%l hr, %i min starting '), DATE_FORMAT(ADDTIME('$ConStartDatim', SCH.starttime), '%a %l:%i %p'))
            ELSE DATE_FORMAT(S.duration,'%l hr, %i min')
            END
            as duration,
        S.pocketprogtext,
        S.progguiddesc,
        S.persppartinfo,
        PSI.badgeid
    FROM
        Sessions S JOIN
        $ReportDB.Tracks T USING (trackid) JOIN
        Schedule SCH USING (sessionid) JOIN
        $ReportDB.SessionStatuses SST USING (statusid) LEFT JOIN
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
                     $ReportDB.Tracks T USING (trackid) JOIN
                     $ReportDB.Types Y USING (typeid)
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
