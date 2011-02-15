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
$_SESSION['return_to_page']="StaffBios.php";
$title="Bios for Presenters";
$description="<P>List of all Presenters biographical information.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php\">description</A>,\n";
$additionalinfo.="the time to visit the <A HREF=\"StaffSchedule.php\">timeslot</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"StaffTracks.php\">track</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>To get an iCal calendar of all the classes of this Presenter, click on the (Fan iCal) after their\n";
  $additionalinfo.="Bio entry, or the (iCal) after the particular activity, to create a calendar for just that activity.</P>\n";
 }
if (strtotime($ConStartDatim) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

/* This complex query grabs the name, class information, editedbio, and progeditedbio (if they exist)
 Most, if not all of the formatting is done within the query, as opposed to in
 the post-processing. */
$query = <<<EOD
SELECT
    concat('<A NAME=\"',P.pubsname,'\"></A>',P.pubsname) as 'Participants',
    concat('<A HREF=\"StaffDescriptions.php#',S.sessionid,'\"><B>',S.title,'</B></A>') AS Title,
    S.secondtitle AS Subtitle,
    if((moderator=1),' (m)','') AS Moderator,
    concat('<A HREF=\"StaffTracks.php#',T.trackname,'\">',T.trackname,'</A>') AS Track,
    concat('<A HREF=\"StaffSchedule.php#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') AS 'Start Time',
    CASE 
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    R.roomname as Roomname,
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',S.sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',S.sessionid,'>(Feedback)</A>') AS Feedback,
    if ((P.editedbio is NULL),' ',P.editedbio) as Bio,
    if ((P.progeditedbio is NULL),' ',P.progeditedbio) as PubsBio,
    P.pubsname,
    P.badgeid
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    P.badgeid AND
    POS.volunteer=0 AND
    POS.introducer=0 AND
    POS.aidedecamp=0
  ORDER BY
    P.pubsname
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init then creates the bio page. */
topofpagereport($title,$description,$additionalinfo);
$printparticipant="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Participants'] != $printparticipant) {
    if ($printparticipant != "") {
      echo "    </TD>\n  </TR>\n</TABLE>\n";
    }
    $printparticipant=$element_array[$i]['Participants'];
    $picture=sprintf("../Local/Participant_Images/%s.jpg",$element_array[$i]['pubsname']);
    if (file_exists($picture)) {
      echo "<TABLE>\n  <TR>\n    <TD width=310>";
      echo sprintf("<img width=300 src=\"%s\"</TD>\n<TD>",$picture);
    } else {
      echo "<TABLE>\n  <TR>\n    <TD>";
    }
    if ($_SESSION['role']=="Participant") {
      echo sprintf("<P><B>%s</B>",$element_array[$i]['Participants']);
      if ($element_array[$i]['Bio'] != ' ') {
	echo sprintf("%s",$element_array[$i]['Bio']);
      }
    } else {
      echo sprintf("<P>Web: <B>%s</B>",$element_array[$i]['Participants']);
      if ($element_array[$i]['Bio'] != ' ') {
	echo sprintf("%s",$element_array[$i]['Bio']);
      }
      echo sprintf("<P>Book: <B>%s</B>",$element_array[$i]['Participants']);
      if ($element_array[$i]['PubsBio'] != ' ') {
	echo sprintf("%s",$element_array[$i]['PubsBio']);
      }
    }
    if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
      echo sprintf(" <A HREF=\"MyScheduleIcal.php?badgeid=%s\">(Fan iCal)</A></P>\n<P>",$element_array[$i]['badgeid']);
    }
  }
  echo sprintf("<DT>%s",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    echo sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Moderator']) {
    echo sprintf("%s",$element_array[$i]['Moderator']);
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
 }
correct_footer();

