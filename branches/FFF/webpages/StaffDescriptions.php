<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

if (isset($_GET['feedback'])) {
  $feedbackp='?feedback=y';
} else {
  $feedbackp='';
}

// LOCALIZATIONS
$_SESSION['return_to_page']="StaffDescriptions.php$feedbackp";
$title="Session Descriptions";
$description="<P>Descriptions for all sessions.</P>\n";
$additionalinfo="<P>Click on the time to visit the session's <A HREF=\"StaffSchedule.php$feedbackp\">timeslot</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php$feedbackp\">bio</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"StaffTracks.php$feedbackp\">track</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>Click on the (iCal) tag to download the iCal calendar for the particular activity you want added to your calendar.</P>\n";
 }
if (strtotime($ConStartDatim) < time()) {
  $additionalinfo.="<P>Click on the (Feedback) tag to give us feedback on a particular scheduled event.</P>\n";
 }

// Generate the constraints on what is shown
if (may_I('General')) {$pubstatus_array[]='\'Volunteer\'';}
if (may_I('Programming')) {$pubstatus_array[]='\'Prog Staff\'';}
if (may_I('Participant')) {$pubstatus_array[]='\'Public\'';}
if (may_I('Events')) {$pubstatus_array[]='\'Event Staff\'';}
if (may_I('Registration')) {$pubstatus_array[]='\'Reg Staff\'';}
if (may_I('Watch')) {$pubstatus_array[]='\'Watch Staff\'';}
if (may_I('Vendor')) {$pubstatus_array[]='\'Vendor Staff\'';}
if (may_I('Sales')) {$pubstatus_array[]='\'Sales Staff\'';}
if (may_I('Fasttrack')) {$pubstatus_array[]='\'Fast Track\'';}
$pubstatus_string=implode(",",$pubstatus_array);

/* This query grabs everything necessary for the descriptions to be printed. */
$query = <<<EOD
SELECT
    if ((pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php$feedbackp#',pubsname,'\">',pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) AS 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffSchedule.php$feedbackp#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') AS 'Start Time',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffTracks.php$feedbackp#',trackname,'\">',trackname,'</A>')) as 'Track',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') AS Roomname,
    estatten AS Attended,
    Sessionid,
    if ((THQT.conid=$conid),if((THQT.questiontypeid IS NULL),"",THQT.questiontypeid),"") AS questiontypeid,
    concat('<A NAME=\"',sessionid,'\"></A>',title) as Title,
    secondtitle AS Subtitle,
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',sessionid,'>(Feedback)</A>') AS Feedback,
    concat(progguiddesc,'</P>') AS 'Web Description',
    concat(pocketprogtext,'</P>') AS 'Book Description'
  FROM
      Sessions S
    JOIN Schedule USING (sessionid)
    JOIN Rooms USING (roomid)
    JOIN $ReportDB.Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid)
    LEFT JOIN $ReportDB.Participants USING (badgeid)
    LEFT JOIN $ReportDB.TypeHasQuestionType THQT USING (typeid)
    JOIN $ReportDB.PubStatuses USING (pubstatusid)
  WHERE
    pubstatusname in ($pubstatus_string) AND
    (volunteer=0 OR volunteer IS NULL) AND
    (introducer=0 OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp IS NULL)
  GROUP BY
    sessionid
  ORDER BY
    S.title
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

if (isset($_GET['feedback'])) {
  $feedback_array=getFeedbackData("");
 }

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
    if ($element_array[$i]['Attended']) {
      echo sprintf("&mdash; About %s Attended",$element_array[$i]['Attended']);
    }
    echo sprintf("&mdash; %s",$element_array[$i]['Feedback']);
  }
  if ($_SESSION['role']=="Participant") {
    echo sprintf("</DT>\n<DD><P>%s",$element_array[$i]['Web Description']);
  } else {
    echo sprintf("  </DT>\n  <DD><P>Web: %s</P>\n",$element_array[$i]['Web Description']);
    echo sprintf("  </DD>\n  <DD><P>Book: %s</P>\n",$element_array[$i]['Book Description']);
    $feedback_file=sprintf("../Local/Feedback/%s.jpg",$element_array[$i]["Sessionid"]);
    if ((file_exists($feedback_file)) and (isset($_GET['feedback']))) {
      echo "  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
      echo sprintf ("<img src=\"%s\">\n<br>\n",$feedback_file);
    }
    if (isset($feedback_array['graph'][$element_array[$i]["Sessionid"]])) {
      echo "  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
      $graphstring=generateSvgString($element_array[$i]["Sessionid"]);
      echo $graphstring;
    }
    if ($feedback_array[$element_array[$i]["Sessionid"]]) {
      echo "  </DD>\n    <DD>Written feedback from surveys:\n<br>\n";
      echo sprintf("%s<br>\n",$feedback_array[$element_array[$i]["Sessionid"]]);
    }
  }
  if ($element_array[$i]['Participants']) {
    echo sprintf("</DD>\n<DD><i>%s</i>",$element_array[$i]['Participants']);
  }
  echo "</DD></P>\n";
 }
echo "</DL>\n";
correct_footer();
?>
