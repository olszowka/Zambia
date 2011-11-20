<?php
require_once('PostingCommonCode.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted
$Grid_Spacer=GRID_SPACER; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="Descriptions.php";
$title="Session Descriptions";
$description="<P>Descriptions for all sessions.</P>\n";
$additionalinfo="<P>Click on the time to visit the session's <A HREF=\"Schedule.php\">timeslot</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"Tracks.php\">track</A>, or visit the <A HREF=\"Postgrid.php\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if (strtotime($ConStartDatim) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the descriptions to be printed. */
$query = <<<EOD
SELECT
    if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.php#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"Schedule.php#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') as 'Start Time',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"Tracks.php#',trackname,'\">',trackname,'</A>')) as 'Track',
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
    concat('<A NAME=\"',sessionid,'\"></A>',title) as Title,
    secondtitle AS Subtitle,
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<P>',progguiddesc,'</P>') as Description
  FROM
      Sessions S
    JOIN Schedule USING (sessionid)
    JOIN Rooms USING (roomid)
    JOIN Tracks USING (trackid)
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
  echo sprintf("</DT>\n<DD>%s",$element_array[$i]['Description']);
  if ($element_array[$i]['Participants']) {
    echo sprintf("<i>%s</i>",$element_array[$i]['Participants']);
  }
  echo "</DD></P>\n";
 }
echo "</DL>\n";
correct_footer();
