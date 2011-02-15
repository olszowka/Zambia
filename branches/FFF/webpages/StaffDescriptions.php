<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="StaffDescriptions.php";
$title="Session Descriptions";
$description="<P>Descriptions for all sessions.</P>\n";
$additionalinfo="<P>Click on the time to visit the session's <A HREF=\"StaffSchedule.php\">timeslot</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"StaffTracks.php\">track</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if (strtotime($ConStartDatim) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the descriptions to be printed. */
$query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffSchedule.php#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') as 'Start Time',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffTracks.php#',T.trackname,'\">',T.trackname,'</A>')) as 'Track',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT R.roomname SEPARATOR ', ') as Roomname,
    S.sessionid as Sessionid,
    concat('<A NAME=\"',S.sessionid,'\"></A>',S.title) as Title,
    S.secondtitle AS Subtitle,
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',S.sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',S.sessionid,'>(Feedback)</A>') AS Feedback,
    concat(S.progguiddesc,'</P>') as 'Web Description',
    concat(S.pocketprogtext,'</P>') as 'Book Description'
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    S.pubstatusid = 2 AND
    POS.volunteer=0 AND
    POS.introducer=0 AND
    POS.aidedecamp=0
  GROUP BY
    sessionid
  ORDER BY
    S.title
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the Descriptions. */
topofpagereport($title,$description,$additionalinfo);
echo "<DL>\n";
for ($i=1; $i<=$elements; $i++) {
  echo sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    echo sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Track']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Track']);
  }
  if ($element_array[$i]['Start Time']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Start Time']);
  }
  if ($element_array[$i]['Duration']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Duration']);
  }
  if ($element_array[$i]['Roomname']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
  }
  if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
    echo sprintf("&mdash; %s",$element_array[$i]['iCal']);
  }
  if (strtotime($ConStartDatim) < time()) {
    echo sprintf("&mdash; %s",$element_array[$i]['Feedback']);
  }
  if ($_SESSION['role']=="Participant") {
    echo sprintf("</DT>\n<DD><P>%s",$element_array[$i]['Web Description']);
  } else {
    echo sprintf("</DT>\n<DD><P>Web: %s",$element_array[$i]['Web Description']);
    echo sprintf("</DT>\n<DD><P>Book: %s",$element_array[$i]['Book Description']);
  }
  if ($element_array[$i]['Participants']) {
    echo sprintf("<i>%s</i>",$element_array[$i]['Participants']);
  }
  echo "</DD></P>\n";
 }
echo "</DL>\n";
if ($_SESSION['role']=="Participant") {
  participant_footer();
 } else {
  staff_footer();
 }
