<?php
require_once('PostingCommonCode.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$Grid_Spacer=GRID_SPACER; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="Schedule.html";
$title="Event Schedule";
$description="<P>Schedule for all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"Tracks.php\">track</A>, or visit the <A HREF=\"Postgrid.php\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if (strtotime($ConStartDatim) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This query grabs everything necessary for the schedule to be printed. */
if (strtoupper(DOUBLE_SCHEDULE)=="TRUE") {
  $query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', concat('<A HREF=\"Bios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)',''))) as 'Participants',
    concat('<A NAME=\"',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\"></A>',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p')) as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    R.roomname as Roomname,
    S.sessionid as Sessionid,
    concat('<A HREF=\"Tracks.php#',T.trackname,'\">',T.trackname,'</A>')) as 'Track',
    concat('<A HREF=\"Descriptions.php#',S.sessionid,'\">',S.title,'</A>') as Title,
    S.secondtitle AS Subtitle,
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',S.sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',S.sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<P>',S.progguiddesc,'</P>') as Description
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
  ORDER BY
    SCH.starttime,
    R.display_order
EOD;
 } else {
  $query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    concat('<A NAME=\"',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\"></A>',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p')) as 'Start Time',
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
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"Tracks.php#',T.trackname,'\">',T.trackname,'</A>')) as 'Track',
    concat('<A HREF=\"Descriptions.php#',S.sessionid,'\">',S.title,'</A>') as Title,
    S.secondtitle AS Subtitle,
    concat('<A HREF=PrecisScheduleIcal.php?sessionid=',S.sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=Feedback.php?sessionid=',S.sessionid,'>(Feedback)</A>') AS Feedback,
    concat('<P>',S.progguiddesc,'</P>') as Description
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
    SCH.starttime,
    R.display_order
EOD;
    }

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the Schedule. */
topofpagereport($title,$description,$additionalinfo);
echo "<DL>\n";
$printtime="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Start Time'] != $printtime) {
    $printtime=$element_array[$i]['Start Time'];
    echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtime);
  }
  echo sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    echo sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Track']) {
    echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Track']);
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
