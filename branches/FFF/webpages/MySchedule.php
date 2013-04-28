<?php
require_once('PartCommonCode.php'); // initialize db; check login;
$conid=$_SESSION['conid'];
$ConStartDatim=CON_START_DATIM; //make it a variable so it will be substituted
$ProgramEmail=PROGRAM_EMAIL; //Use it a variable locally
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$title="My Schedule";
// require_once('renderMySessions2.php');
if (!may_I('my_schedule')) {
  $message_error="You do not currently have permission to view this page.<BR>\n";
  RenderError($title,$message_error);
  exit();
 }
// set $badgeid from session

// General presenter information
// Gather the comments offered on this presenter into pcommentarray, if any
$query = <<<EOD
SELECT
    comment
  FROM
      CommentsOnParticipants
  WHERE
    badgeid="$badgeid"
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$pcommentrows=mysql_num_rows($result);
for ($i=1; $i<=$pcommentrows; $i++) {
  $pcommentarray[$i]=mysql_fetch_assoc($result);
 }

// Get the state of registration into $regmessage
$query = <<<EOD
SELECT
    message
  FROM
      $ReportDB.CongoDump
    LEFT JOIN $ReportDB.RegTypes USING (regtype)
  WHERE
    badgeid="$badgeid"
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$row=mysql_fetch_array($result, MYSQL_NUM);
$regmessage=$row[0];

// Get the number of pannels the participant is introducing
$query = <<<EOD
SELECT
    count(*) 
  FROM
      ParticipantOnSession POS,
      Schedule SCH
  WHERE
    POS.sessionid=SCH.sessionid and
    POS.introducer=1 and
    badgeid=$badgeid 
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$row=mysql_fetch_array($result, MYSQL_NUM);
$intro_p=$row[0];

    // Get the number of pannels the participant is on
$query = <<<EOD
SELECT
    count(*) 
  FROM
      ParticipantOnSession POS,
      Schedule SCH
  WHERE
    POS.sessionid=SCH.sessionid and
    badgeid=$badgeid
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$row=mysql_fetch_array($result, MYSQL_NUM);
$poscount=$row[0];

// Message about state of registration, (on more than 3 pannels programming will ask for a comp).
if (!$regmessage) {
  if ($poscount>=3) {
    $regmessage="not registered.</span><span>  Programming has requested a comp membership for you";
  }
  else {
    $regmessage="not registered.</span><span>  Panelists on 3 or more panels receive complementary memberships from Programming.  If you are interested in increasing your number of panels to take advantage of this, please contact us and we will work with you to see if it is possible.  If you are expecting a comp from helping another division, that will show up here shortly after registration processes it.  Please contact that division or registration with questions";
  }
 }

// Get all the written feedback on the sessions, and the graph of the questions.
$feedback_array=getFeedbackData($badgeid);

// Build the schedule of classes into schdarray
$query = <<<EOD
SELECT
    POS.sessionid,
    trackname,
    concat(S.title, if(estatten,concat(" (estimated attendance: ",estatten,")"),'')) as title,
    roomname,
    pocketprogtext,
    progguiddesc,
    if ((THQT.conid=$conid),if((THQT.questiontypeid IS NULL),"",THQT.questiontypeid),"") AS questiontypeid,
    DATE_FORMAT(ADDTIME('$ConStartDatim', starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    persppartinfo,
    notesforpart,
    concat(if((servicenotes!=''),servicenotes,""),
           if(((servicenotes!='') AND (servicelist!='')),", ",""),
           if((servicelist!=''),servicelist,''),
           if((((servicenotes!='') OR (servicelist!='')) AND (featurelist!='')),", ",""),
           if((featurelist!=''),featurelist,'')) AS Needed
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN ParticipantOnSession POS USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN $ReportDB.Tracks T USING (trackid)
    LEFT JOIN $ReportDB.TypeHasQuestionType THQT USING (typeid)
    LEFT JOIN (SELECT
           S.sessionid, 
           title,
           GROUP_CONCAT(DISTINCT servicename SEPARATOR ', ') as 'servicelist'
         FROM
             Sessions S, 
             SessionHasService SS, 
             $ReportDB.Services SE
         WHERE
           S.sessionid=SS.sessionid and
           SE.serviceid=SS.serviceid and
	   SE.conid=$conid
         GROUP BY
           S.sessionid) X USING (sessionid)
    LEFT JOIN (SELECT
           S.sessionid, 
           title,
           GROUP_CONCAT(DISTINCT featurename SEPARATOR ', ') as 'featurelist'
         FROM
             Sessions S, 
             SessionHasFeature SF, 
             Features F
         WHERE
           S.sessionid=SF.sessionid and
           F.featureid=SF.featureid
         GROUP BY
           S.sessionid) Y USING (sessionid)
  WHERE
    badgeid="$badgeid"
  ORDER BY
    starttime
EOD;
// error_log("Zambia: $query");
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$schdrows=mysql_num_rows($result);
for ($i=1; $i<=$schdrows; $i++) {
  $schdarray[$i]=mysql_fetch_assoc($result);
  $schdredirect.=" <A HREF=#".$schdarray[$i]["sessionid"].">".htmlspecialchars($schdarray[$i]["title"])."</A>";
  $feedback_file=sprintf("../Local/Feedback/%s.jpg",$schdarray[$i]["sessionid"]);
  if (file_exists($feedback_file)) {
    $schdarray[$i]["feedbackgraph"]="  <TR>\n    <TD>&nbsp;</TD>\n    <TD colspan=6 class=border1000>Feedback graph from surveys:<br>";
    $schdarray[$i]["feedbackgraph"].="<img src=\"$feedback_file\"></TD>\n  </TR>\n";
  }
  if (isset($feedback_array['graph'][$schdarray[$i]["sessionid"]])) {
    $schdarray[$i]["autofeedbackgraph"]="  <TR>\n    <TD>&nbsp;</TD>\n    <TD colspan=6 class=border1000>Feedback graph from surveys:<br>";
    $schdarray[$i]["autofeedbackgraph"].=generateSvgString($schdarray[$i]["sessionid"]);
    $schdarray[$i]["autofeedbackgraph"].="</TD>\n  </TR>\n";
  }
  if (isset($feedback_array[$schdarray[$i]["sessionid"]])) {
    $schdarray[$i]["feedbackwritten"]="  <TR>\n    <TD>&nbsp;</TD>\n    <TD colspan=6 class=border1000>Written feedback from surveys:\n";
    $schdarray[$i]["feedbackwritten"].=$feedback_array[$schdarray[$i]["sessionid"]]."</TD>\n  </TR>\n";
  }
 }

// Build the list of individuals associated with each class into partarray
$query = <<<EOD
SELECT
    POS.sessionid,
    CD.badgename,
    P.pubsname,
    POS.moderator,
    POS.volunteer,
    POS.introducer,
    POS.aidedecamp,
    PSI.comments AS PresenterComments
  FROM
      ParticipantOnSession POS
    JOIN $ReportDB.CongoDump CD USING(badgeid)
    JOIN $ReportDB.Participants P USING(badgeid)
    LEFT JOIN ParticipantSessionInterest PSI USING(sessionid,badgeid)
  WHERE
    POS.sessionid in (SELECT
                          sessionid 
                        FROM
                            ParticipantOnSession
                        WHERE badgeid='$badgeid')
  ORDER BY
    sessionid,
    moderator DESC
EOD;
if (!$result=mysql_query($query,$link)) {
  $message.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }
$partrows=mysql_num_rows($result);
for ($i=1; $i<=$partrows; $i++) {
  $partarray[$i]=mysql_fetch_assoc($result);
 }

// Begin the presentation of the information
participant_header($title);
if (file_exists("../Local/Verbiage/MySchedule_0")) {
  echo file_get_contents("../Local/Verbiage/MySchedule_0");
 } else {
  echo "<P>Below is the list of all the schedule elements for which you are scheduled.  If you need any changes\n";
  echo "to this schedule please contact <A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.</P>\n";
  echo "<P>In order to put together the entire schedule, we had to schedule some panels outside of\n";
  echo "the times that certain panelists requested.  If this happened to you, we would love to have\n";
  echo "you on the panel, but understand if you cannot make it.  Please let us know if you cannot.</P>\n";
  echo "<P>Several of the panels we are running this year were extremely popular with over 20 potential\n";
  echo "panelists signing up.  Choosing whom to place on those panels was difficult.  There is always a\n";
  echo "possibility that one of the panelists currently scheduled will be unavailable so feel free to\n";
  echo "check with us to see if a space has opened up on a panel on which you'd still like to participate.</P>\n";
  echo "<P>To facilitate communication yet also preserve privacy, we provide you the option of putting your\n";
  echo "contact information in the comments field for each panel (under the\n";
  echo "<A HREF=\"./my_sessions2.php\">\"My Panel Interests\"</A> tab).  That will expose it to other\n";
  echo "panelists who can then email or call you as appropriate to discuss the panel in advance.  If you\n";
  echo "check back in a day or two you may find other panelists' information.</P>\n";
 }
echo "<P>You can also take a look at all that is going on by <A HREF=\"StaffSchedule.php\">timeslot</A>,\n";
echo "<A HREF=\"StaffDescriptions.php\">descriptions</A>, <A HREF=\"StaffTracks.php\">tracks</A>, visit the\n";
echo "<A HREF=\"grid.php?programming=y&unpublished=y\">grid</A>, or people's <A HREF=\"StaffBios.php\">bio</A>.</P>\n";
echo "<P><A HREF=\"MyScheduleIcal.php\">Here</A> is an iCal (Calendar standard) calendar of your schedule.\n";
echo "<A HREF=\"SchedulePrint.php?print_p=T&individual=".$_SESSION['badgeid']."\">Print</A> a PDF of your schedule.\n";
if ($intro_p > 0) {
  echo "<A HREF=\"ClassIntroPrint.php\">Print</A> a PDF of all of your class and panel introductions.\n";
 }
echo "</P>\n";
echo "<P>Your registration status is <SPAN class=\"hilit\">$regmessage.</SPAN></P>\n";
if ($pcommentrows > 0) {
  echo "<P>General <A HREF=#genfeedback>Feedback</A> received about or for you.\n</P>";
 }
echo "<P>Go directly to the class: $schdredirect</P>\n";
echo "<P>Thank you -- <A HREF=\"mailto:$ProgramEmail\">Programming</a>\n";
echo "<TABLE>\n";
echo "  <COL><COL width=\"30%\"><COL width=\"20%\"><COL><COL width=\"6%\"><COL><COL width=\"18%\">\n";
for ($i=1; $i<=$schdrows; $i++) {
  echo "  <TR>\n";
  echo "    <TD class=\"hilit\"><A NAME=".$schdarray[$i]["sessionid"]."></A>".$schdarray[$i]["sessionid"]."</TD>\n";
  echo "    <TD class=\"hilit\">".htmlspecialchars($schdarray[$i]["title"])."</TD>\n";
  echo "    <TD class=\"hilit\">".$schdarray[$i]["roomname"]."</TD>\n";
  echo "    <TD class=\"hilit\">".$schdarray[$i]["trackname"]."</TD>\n";
  echo "    <TD class=\"hilit\">&nbsp;</TD>\n";
  echo "    <TD class=\"hilit\">".$schdarray[$i]["Start Time"]."</TD>\n";
  echo "    <TD class=\"hilit\">Duration: ".$schdarray[$i]["Duration"]."</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD colspan=6 class=\"border0010\">Web: ".htmlspecialchars($schdarray[$i]["progguiddesc"])."</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD colspan=6 class=\"border0010\">Book: ".htmlspecialchars($schdarray[$i]["pocketprogtext"])."</TD>\n";
  echo "  </TR>\n";
  if ($schdarray[$i]["persppartinfo"] != "") {
    echo "  <TR>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD colspan=6 class=\"border0010\">Requirements: ".htmlspecialchars($schdarray[$i]["persppartinfo"])."</TD>\n";
    echo "  </TR>\n";
  }
  if ($schdarray[$i]["notesforpart"] != "") {
    echo "  <TR>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD colspan=6 class=\"border0010\">Participant notes: ".htmlspecialchars($schdarray[$i]["notesforpart"])."</TD>\n";
    echo "  </TR>\n";
  }
  if ($schdarray[$i]["Needed"] != "") {
    echo "  <TR>\n";
    echo "    <TD>&nbsp;</TD>\n";
    echo "    <TD colspan=6 class=\"border0010\">Support requests: ".htmlspecialchars($schdarray[$i]["Needed"])."</TD>\n";
    echo "  </TR>\n";
  }
  echo "  <TR>\n";
  echo "    <TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
  echo "  <TR>\n";
  echo "    <TD>&nbsp;</TD>\n";
  echo "    <TD class=\"usrinp\">Panelists' Publication Names (Badge Names)</TD>\n";
  echo "    <TD colspan=5 class=\"usrinp\">Their Comments</TD>\n";
  echo "  </TR>\n";
  echo "  <TR>\n";
  echo "    <TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
  for ($j=1; $j<=$partrows; $j++) {
    if ($partarray[$j]["sessionid"]!=$schdarray[$i]["sessionid"]) {
      continue;
    }
    if ($partarray[$j+1]["sessionid"]==$schdarray[$i]["sessionid"]) {
      $class="border0010";
    }
    else {
      $class="";
    }
    echo "  <TR>\n    <TD>&nbsp;</TD>\n";
    echo "    <TD class=\"$class\">".htmlspecialchars($partarray[$j]["pubsname"]);
    if ($partarray[$j]["pubsname"]!=$partarray[$j]["badgename"]) echo " (".htmlspecialchars($partarray[$j]["badgename"]).")";
    if ($partarray[$j]["moderator"]) {
      echo " <I>mod</I> ";
    }
    if ($partarray[$j]["volunteer"]) {
      echo " <I>volunteer</I> ";
    }
    if ($partarray[$j]["introducer"]) {
      echo " <I>introducer</I> ";
    }
    if ($partarray[$j]["aidedecamp"]) {
      echo " <I>assistant</I> ";
    }
    echo "</TD>\n";
    echo "    <TD colspan=5 class=\"$class\">".htmlspecialchars(fix_slashes($partarray[$j]["PresenterComments"]));
    echo "</TD>\n";
    echo "  </TR>\n";
  }
  echo $schdarray[$i]["feedbackgraph"];
  echo $schdarray[$i]["autofeedbackgraph"];
  echo $schdarray[$i]["feedbackwritten"];
  echo "  <TR>\n    <TD colspan=7 class=\"border0020\">&nbsp;</TD>\n  </TR>\n";
  echo "  <TR>\n    <TD colspan=7 class=\"border0000\">&nbsp;</TD>\n  </TR>\n";
 }
echo "</TABLE>\n"; 
if ($pcommentrows > 0) {
  echo "<hr>\n<P><A NAME=genfeedback></A>Personal Feedback:</A></P>\n";
  echo "<UL>\n";
  for ($i=1; $i<=$pcommentrows; $i++) {
    echo "  <LI>".$pcommentarray[$i]["comment"]."\n";
  }
  echo "</UL>\n<br>\n";
 }
correct_footer();
?>
