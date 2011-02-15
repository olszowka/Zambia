<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

## LOCALIZATIONS
$_SESSION['return_to_page']="StaffTracks.html";
$title="Event Tracks Schedule";
$description="<P>Track Schedules for all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php\">bio</A>, the time to visit the session's\n";
$additionalinfo.="<A HREF=\"StaffSchedule.php\">timeslot</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the <I>(iCal)</i> next to the track name to have an iCal Calendar sent to your machine for\n";
  $additionalinfo.="automatic inclusion, and the (iCal) next to the particular activity for one of that activity.</P>";
 }
if (strtotime($ConStartDatim) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the schedule to be printed. */
$query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    concat('<A HREF=\"StaffSchedule.php#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') as 'Start Time',
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
    GROUP_CONCAT(DISTINCT concat('<A NAME=\"',T.trackname,'\">',T.trackname,'</A> <A HREF=StaffTrackScheduleIcal.php?trackid=',T.trackid,'><I>(iCal)</I></A>')) as 'Track',
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',S.sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',S.sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<A HREF=\"StaffDescriptions.php#',S.sessionid,'\">',S.title,'</A>') as Title,
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
    T.trackname,
    SCH.starttime,
    R.display_order
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the Schedule. */
topofpagereport($title,$description,$additionalinfo);
echo "<DL>\n";
$printtrack="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Track'] != $printtrack) {
    $printtrack=$element_array[$i]['Track'];
    echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtrack);
  }
  echo sprintf("<P><DT><B>%s</B> &mdash; %s &mdash; <i>%s</i>",
	       $element_array[$i]['Title'],$element_array[$i]['Start Time'],$element_array[$i]['Duration']);
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
