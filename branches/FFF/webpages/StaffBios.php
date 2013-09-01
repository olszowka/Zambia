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
$_SESSION['return_to_page']="StaffBios.php$feedbackp";
$title="Bios for Presenters";
$description="<P>List of all Presenters biographical information.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php$feedbackp\">description</A>,\n";
$additionalinfo.="the time to visit the <A HREF=\"StaffSchedule.php$feedbackp\">timeslot</A>, the track name to visit the particular\n";
$additionalinfo.="<A HREF=\"StaffTracks.php$feedbackp\">track</A>, or visit the <A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
  $additionalinfo.="<P>To get an iCal calendar of all the classes of this Presenter, click on the (Fan iCal) after their\n";
  $additionalinfo.="Bio entry, or the (iCal) after the particular activity, to create a calendar for just that activity.</P>\n";
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

/* This complex query grabs the name, and class information.
 Most, if not all of the formatting is done within the query, as opposed to in
 the post-processing. The bio information is grabbed seperately. */
$query = <<<EOD
SELECT
    concat('<A NAME=\"',P.pubsname,'\"></A>',P.pubsname) AS 'Participants',
    concat('<A HREF=\"StaffDescriptions.php$feedbackp#',S.sessionid,'\"><B>',S.title,'</B></A>') AS Title,
    S.secondtitle AS Subtitle,
    if((moderator=1),' (m)','') AS Moderator,
    concat('<A HREF=\"StaffTracks.php$feedbackp#',T.trackname,'\">',T.trackname,'</A>') AS Track,
    concat('<A HREF=\"StaffSchedule.php$feedbackp#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') AS 'Start Time',
    CASE 
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    R.roomname as Roomname,
    S.estatten AS Attended,
    S.sessionid AS Sessionid,
    if ((THQT.conid=$conid),if((THQT.questiontypeid IS NULL),"",THQT.questiontypeid),"") AS questiontypeid,
    concat('<A HREF=StaffPrecisScheduleIcal.php?sessionid=',S.sessionid,'>(iCal)</A>') AS iCal,
    concat('<A HREF=StaffFeedback.php?sessionid=',S.sessionid,'>(Feedback)</A>') AS Feedback,
    badgeid
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN $ReportDB.Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid)
    LEFT JOIN $ReportDB.Participants P USING (badgeid)
    LEFT JOIN $ReportDB.TypeHasQuestionType THQT USING (typeid)
    JOIN $ReportDB.PubStatuses PS USING (pubstatusid)
  WHERE
    PS.pubstatusname in ($pubstatus_string) AND
    (volunteer=0 OR volunteer IS NULL) AND
    (introducer=0 OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp IS NULL)
  ORDER BY
    P.pubsname,
    starttime
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

if (isset($_GET['feedback'])) {
  $feedback_array=getFeedbackData("");
 }

// Gather the comments offered on presenters into pcomment_array
$query = <<<EOD
SELECT
    badgeid,
    comment
  FROM
      CommentsOnParticipants
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }

while ($row=mysql_fetch_assoc($result)) {
  $pcomment_array[$row['badgeid']].="    <br>\n    --\n    <br>\n    <PRE>".fix_slashes($row['comment'])."</PRE>";
 }

/* Printing body.  Uses the page-init then creates the bio page. */
topofpagereport($title,$description,$additionalinfo);
$printparticipant="";
for ($i=1; $i<=$elements; $i++) {
  if ($element_array[$i]['Participants'] != $printparticipant) {
    if ($printparticipant != "") {
      echo "    </TD>\n  </TR>\n</TABLE>\n";
    }
    $printparticipant=$element_array[$i]['Participants'];
    $bioinfo=getBioData($element_array[$i]['badgeid']);
    /* Presenting all the type pieces, in whatever
       languages we have, grouping by language, then type.
       Currently we are using edited as the state, at some
       point we should move to good. */
    $namecount=0;
    $tablecount=0;
    $biostate='edited'; // for ($l=0; $l<count($bioinfo['biostate_array']); $l++) {
    for ($k=0; $k<count($bioinfo['biolang_array']); $k++) {
      $bioout=array();
      for ($j=0; $j<count($bioinfo['biotype_array']); $j++) {

	// Setup for keyname, to collapse all three variables into one passed name.
	$biotype=$bioinfo['biotype_array'][$j];
	$biolang=$bioinfo['biolang_array'][$k];
	// $biostate=$bioinfo['biostate_array'][$l];
	$keyname=$biotype."_".$biolang."_".$biostate."_bio";

	// Set up the useful pieces.
	if (isset($bioinfo[$keyname])) {$bioout[$biotype]=$bioinfo[$keyname];}
      }

      // Still in the language switch, but have set the $bioout array.
      if (isset($bioout['picture'])) {
	if ($tablecount == 0) {
	  echo "<TABLE>\n  <TR>\n    <TD valign=\"top\" width=310>";
	  $tablecount++;
	} else {
	  echo "    </TD>\n  </TR>\n  <TR>\n    <TD width=310>";
	}
	echo sprintf("<img width=300 src=\"%s\"</TD>\n<TD>",$bioout['picture']);
      } else {
	if ($tablecount == 0) {
	  echo "<TABLE>\n  <TR>\n    <TD>";
	  $tablecount++;
	}
      }
      if ($_SESSION['role']=="Participant") {$preweb="";} else {$preweb="Web: ";}
      if (isset($bioout['web'])) {
	echo sprintf("<P>%s<B>%s</B>%s</P>\n",$preweb,$printparticipant,$bioout['web']);
	$namecount++;
      }
      if (($preweb != "") and (isset($bioout['book']))) {
	echo sprintf("<P>Book: <B>%s</B>%s</P>\n",$printparticipant,$bioout['book']);
	$namecount++;
      }
      if (isset($bioout['uri'])) {
	if ($namecount==0) {
	  echo sprintf("<P><B>%s:</B><br>%s</P>\n",$printparticipant,$bioout['uri']);
	} else {
	  echo sprintf("<P>%s</P>\n",$bioout['uri']);
	}
      }
    }
    // If there were no bios
    if ($namecount==0) { echo sprintf("<P><B>%s</B>",$printparticipant);}
    if ((isset($_GET['feedback'])) and ($pcomment_array[$element_array[$i]['badgeid']])) {
      echo sprintf("<P> Feedback on Presenter: %s</P>\n",$pcomment_array[$element_array[$i]['badgeid']]);
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
    if ($element_array[$i]['Attended']) {
      echo sprintf("&mdash; About %s Attended",$element_array[$i]['Attended']);
    }
    echo sprintf("&mdash; %s",$element_array[$i]['Feedback']);
  }
  if ($_SESSION['role']=="Staff") {
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
}
echo "    </TD>\n  </TR>\n</TABLE>\n";
correct_footer();
?>
