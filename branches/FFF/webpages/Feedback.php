<?php
require_once('PostingCommonCode.php');

/* Global Variables */
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

// LOCALIZATIONS
$NumOfColumns=3; // Number of columns at the top of the page.
$_SESSION['return_to_page']="Feedback.php";
$formstring="";

/* This query pulls the questions, to be surveyed */
$query=<<<EOD
SELECT
   questiontext,
   questionid
  FROM
      QuestionsForSurvey
  ORDER BY
    display_order

EOD;

// Retrive query
list($questioncount,$header_array1,$question_array)=queryreport($query,$link,$title,$description,0);

if ((isset($_POST["selsess"])) && ($_POST["selsess"]!=0)) {
  $query= "INSERT INTO Feedback (sessionid,questionid,questionvalue) VALUES ";
  for ($i=1; $i<=$questioncount; $i++) {
    if ((isset($_POST["$i"])) && ($_POST["$i"]!="")) {
      $query.="(".$_POST['selsess'].",".$i.",".$_POST["$i"]."),";
    }
  }
  $query=substr($query,0,-1);
  if (!mysql_query($query,$link)) {
    $message_error=$query."<BR>Error updating $table.  Database not updated.";
    RenderError($title,$message_error);
    exit;
  }
  if ((isset($_POST['classcomment'])) && ($_POST['classcomment']!="")) {
    $query="INSERT INTO CommentsOnSessions (sessionid,rbadgeid,commenter,comment) VALUES (".$_POST['selsess'].",0,'Annonymous','".mysql_real_escape_string($_POST['classcomment'])."')";
    if (!mysql_query($query,$link)) {
      $message_error=$query."<BR>Error updating $table.  Database not updated.";
      RenderError($title,$message_error);
      exit;
    }
  }
  if ((isset($_POST['progcomment'])) && ($_POST['progcomment']!="")) {
    $query="INSERT INTO CommentsOnProgramming (rbadgeid,commenter,comment) VALUES (0,'Annonymous','".mysql_real_escape_string($_POST['progcomment'])."')";
    if (!mysql_query($query,$link)) {
      $message_error=$query."<BR>Error updating $table.  Database not updated.";
      RenderError($title,$message_error);
      exit;
    }
  }
  $message="Database updated successfully.<BR>";
  $formstring.="<P class=\"regmsg\">".$message."\n";
 }

$sessionid=$_GET['sessionid'];
$selday=$_GET['selday'];

if ($selday=="Friday") {
  $dayname="Friday";
  $time_start=0;
  $time_end=87000;
 } elseif ($selday=="Saturday Early") {
   $dayname="Saturday Early";
   $time_start=100000;
   $time_end=140000;
 } elseif ($selday=="Saturday Late") {
   $dayname="Saturday Late";
   $time_start=140000;
   $time_end=200000;
 } elseif ($selday=="Sunday") {
  $dayname="Sunday";
  $time_start=200000;
  $time_end=400000;
 } elseif ($sessionid!="") {
  $dayname="";
  $time_start=0;
  $time_end=400000;
 } else {
  $title="Feedback Page";
  $description="<P>Please select the day you wish to generate the feedback form for:</P>\n";
  topofpagereport($title,$description,$additionalinfo);
?>
<UL>
  <LI><A HREF="Feedback.php?selday=Friday">Friday</A>
  <LI><A HREF="Feedback.php?selday=Saturday Early">Saturday Early</A>
  <LI><A HREF="Feedback.php?selday=Saturday Late">Saturday Late</A>
  <LI><A HREF="Feedback.php?selday=Sunday">Sunday</A>
</UL>
<?php
  correct_footer();
  exit();
 }
  

$title=CON_NAME." $dayname Feedback";
$description="<P>Not sure which class?  Check the <A HREF=Descriptions.php>descriptions</A>, <A HREF=Bios.php>bios</A>, <A HREF=Schedule.php>timeslots</A>, or <A HREF=Tracks.php>tracks</A> pages.</P>";
$additionalinfo="<P>Done with this time block?  Pick a different one:</P>\n";
$additionalinfo.="<UL>\n  <LI><A HREF=\"Feedback.php?selday=Friday\">Friday</A>\n";
$additionalinfo.="  <LI><A HREF=\"Feedback.php?selday=Saturday Early\">Saturday Early</A>\n";
$additionalinfo.="  <LI><A HREF=\"Feedback.php?selday=Saturday Late\">Saturday Late</A>\n";
$additionalinfo.="  <LI><A HREF=\"Feedback.php?selday=Sunday\">Sunday</A></UL>\n";

/* This query grabs all the schedule elements to be rated, for the selected time period. */
$query=<<<EOD
SELECT
    DISTINCT S.title,
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime), '%l:%i %p') as time,
    S.sessionid
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
  WHERE
    (typeid = 1 OR
     typeid = 2) AND
    Time_TO_SEC(SCH.starttime) > $time_start AND
    Time_TO_SEC(SCH.starttime) < $time_end

EOD;

if ($sessionid!="") {
  $query.=" AND sessionid=$sessionid";
 }
$query.=" ORDER BY S.title";

// Retrive query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Printing body. */
$formstring.="<FORM name=\"feedbackform\" method=POST action=\"Feedback.php?selday=$selday\">\n";
if ($sessionid!="") {
  $formstring.="<INPUT type=\"hidden\" name=\"selsess\" value=\"".$element_array[1]['sessionid']."\">\n";
  $formstring.="<P>Feedback on ".$element_array[1]['title']." (".$element_array[1]['time'].")</P>\n";
 } else {
  $formstring.="<DIV><LABEL for=\"feedbackclass\">Select the $dayname class you are offering feedback on.</LABEL>\n";
  $formstring.="<SELECT name=\"selsess\">\n";
  $formstring.="    <OPTION value=0 SELECTED>Select Session</OPTION>\n";
  for ($i=1; $i<=$elements; $i++) {
    $formstring.="    <OPTION value=\"".$element_array[$i]['sessionid']."\">";
    $formstring.=$element_array[$i]['title']." (".$element_array[$i]['time'].")</OPTION>\n";
  }
  $formstring.="</SELECT></DIV>\n";
 }

$formheaders="  <TR><TH>&nbsp;</TH><TH>Totally Agree</TH><TH>Somewhat Agree</TH><TH>Neutral</TH>";
$formheaders.="<TH>Somewhat Disagree</TH><TH>Totally Disagree</TH></TR>";

$formstring.="<P>&nbsp;&nbsp;Please answer the following questions from totally agree to totally disagree.";
$formstring.="<TABLE border=1>";
$formstring.=$formheaders."\n";
for ($i=1; $i<=$questioncount; $i++) {
  $formstring.="  <TR><TD>".$question_array[$i]['questiontext'].":<br>&nbsp;</TD>";
  $formstring.="<TD align=\"center\">";
  $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"5\">";
  $formstring.="</TD><TD align=\"center\">";
  $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"4\">";
  $formstring.="</TD><TD align=\"center\">";
  $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"3\">";
  $formstring.="</TD><TD align=\"center\">";
  $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"2\">";
  $formstring.="</TD><TD align=\"center\">";
  $formstring.="<INPUT type=\"radio\" name=\"".$question_array[$i]['questionid']."\" id=\"".$question_array[$i]['questionid']."\" value=\"1\">";
  $formstring.="</TD></TR>\n";
 }
$formstring.="</TABLE></P>\n";
$formstring.="<LABEL for=\"classcomment\">Other comments on this class:</LABEL>\n<br>\n";
$formstring.="  <TEXTAREA name=\"classcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
$formstring.="<LABEL for=\"progcomment\">Comments on the FFF in general:</LABEL>\n<br>\n";
$formstring.="  <TEXTAREA name=\"progcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
$formstring.="<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Send Feedback</BUTTON>\n";
$formstring.="</FORM>\n";

topofpagereport($title,$description,$additionalinfo);
echo $formstring;
correct_footer();
?>