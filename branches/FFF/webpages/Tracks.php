<?php
require_once('PostingCommonCode.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$Grid_Spacer=GRID_SPACER; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="Tracks.html";
$title="Event Tracks Schedule";
$description="<P>Track Schedules for all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php\">bio</A>, the time to visit the session's\n";
$additionalinfo.="<A HREF=\"Schedule.php\">timeslot</A>, or visit the <A HREF=\"Postgrid.php\">grid</A>.</P>\n";
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
    if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.php#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    concat('<A HREF=\"Schedule.php#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') as Roomname,
    Sessionid,
    GROUP_CONCAT(DISTINCT concat('<A NAME=\"',trackname,'\">',trackname,'</A> <A HREF=TrackScheduleIcal.php?trackid=',trackid,'><I>(iCal)</I></A>')) as 'Track',
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<A HREF=\"Descriptions.php#',sessionid,'\">',title,'</A>') as Title,
    concat('<P>',progguiddesc,'</P>') as Description
  FROM
      Sessions
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid)
    LEFT JOIN Participants USING (badgeid)
    JOIN PubStatuses USING (pubstatusid)
  WHERE
    pubstatusname in ('Public') AND
    volunteer=0 AND
    introducer=0 AND
    aidedecamp=0
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
  echo sprintf("</DT>\n<DD>%s",$element_array[$i]['Description']);
  if ($element_array[$i]['Participants']) {
    echo sprintf("<i>%s</i>",$element_array[$i]['Participants']);
  }
  echo "</DD></P>\n";
 }
echo "</DL>\n";
correct_footer();
