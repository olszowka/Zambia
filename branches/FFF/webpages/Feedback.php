<?php
require_once('PostingCommonCode.php');

/* Global Variables */
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$conid=$_SESSION['conid']; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$conend=CON_NUM_DAYS*86400; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="Feedback.php";
$formstring="";

/* This query pulls the questions, to be surveyed */
$query=<<<EOD
SELECT
    questiontext,
    questionid,
    questiontypeid
  FROM
      $ReportDB.QuestionsForSurvey
  ORDER BY
    display_order

EOD;

// Retrive query
list($questioncount,$header_array1,$question_array)=queryreport($query,$link,$title,$description,0);

/* This query pulls the page description information for presentation */
$query=<<<EOD
SELECT
    fpageid,
    fpagedesc,
    fpagestart,
    fpageend,
    fpagecols,
    questiontypeid
  FROM
      $ReportDB.FeedbackPages
  WHERE
    conid=$conid

EOD;

// Retrive query
list($fpagecount,$fpageheader_array,$fpage_array)=queryreport($query,$link,$title,$description,0);

// Find single class and establish selday_array
for ($i=1; $i<=$fpagecount; $i++) {
  if ($fpage_array[$i]['fpagedesc']=="single class") {
    $single_class_no=$i;
  }
  $selday_array[$fpage_array[$i]['fpageid']]=$i;
}

// Insert the passed values.
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
    $query="INSERT INTO CommentsOnSessions (sessionid,rbadgeid,commenter,comment) VALUES (".$_POST['selsess'].",0,'Annonymous','".mysql_real_escape_string(stripslashes($_POST['classcomment']))."')";
    if (!mysql_query($query,$link)) {
      $message_error=$query."<BR>Error updating $table.  Database not updated.";
      RenderError($title,$message_error);
      exit;
    }
  }
  if ((isset($_POST['progcomment'])) && ($_POST['progcomment']!="")) {
    $query="INSERT INTO CommentsOnProgramming (rbadgeid,commenter,comment) VALUES (0,'Annonymous','".mysql_real_escape_string(stripslashes($_POST['progcomment']))."')";
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

// Set selday to "single class"
if ($sessionid!="") {$selday=$fpage_array[$single_class_no]['fpageid'];}

// Set the passed variables, or drop to default page
if (isset($selday) and ($selday!="")) {
  $dayname=$fpage_array[$selday_array[$selday]]['fpagedesc'];
  $time_start=$fpage_array[$selday_array[$selday]]['fpagestart'];
  $time_end=$fpage_array[$selday_array[$selday]]['fpageend'];
  $fpageid=$fpage_array[$selday_array[$selday]]['fpageid'];
  $questiontypeid=$fpage_array[$selday_array[$selday]]['questiontypeid'];
} else {
  $title="Feedback Page";
  $description="<P>Please select the day/type you wish to generate the feedback form for:</P>\n";
  topofpagereport($title,$description,$additionalinfo);
  echo "<UL>\n";
  for ($i=1; $i<=$fpagecount; $i++) {
    if ($i!=$single_class_no) {
      echo "  <LI><A HREF=\"Feedback.php?selday=" . $fpage_array[$i]['fpageid'] . "\">" . $fpage_array[$i]['fpagedesc'] . "</A></LI>\n";
    }
  }
  echo "</UL>\n";
  correct_footer();
  exit();
}

// Set standard headers across the pages.
$title=CON_NAME." $dayname Feedback";
$description="<P>Not sure which class?  Check the <A HREF=Descriptions.php>descriptions</A>, <A HREF=Bios.php>bios</A>, <A HREF=Schedule.php>timeslots</A>, or <A HREF=Tracks.php>tracks</A> pages.</P>";
$additionalinfo="<P>Done with this time block?  Pick a different one:</P>\n<UL>\n";
  for ($i=1; $i<=$fpagecount; $i++) {
    if ($i!=$single_class_no) {
      $additionalinfo.="  <LI><A HREF=\"Feedback.php?selday=" . $fpage_array[$i]['fpageid'] . "\">" . $fpage_array[$i]['fpagedesc'] . "</A></LI>\n";
    }
  }
$additionalinfo.="</UL>\n";

/* This query finds what Type(s) of schedule elements need to be selected */
$query=<<<EOD
SELECT
    typeid
  FROM
      $ReportDB.FeedbackPageHasType
  WHERE
    fpageid=$fpageid

EOD;

// Retrive query
list($typescount,$typesheader_array,$types_array)=queryreport($query,$link,$title,$description,0);

// Reduce query to a string of types, to be passed to the next query.
// Currently with a failure for full-con, so we might fix that later.
if ($typescount > 0) {
  for ($i=1; $i<=$typescount; $i++) {
    $shorttypes_array[]=$types_array[$i]['typeid'];
  }
  $types_string="(typeid = " . implode(" OR typeid = ",$shorttypes_array) . ") AND";
} else {$types_string="(typeid = 1 and typeid = 2) AND";}

/* This query grabs all the schedule elements to be rated, for the selected time period. */
$query=<<<EOD
SELECT
    DISTINCT title,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime), '%l:%i %p') as time,
    sessionid,
    questiontypeid
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN $ReportDB.TypeHasQuestionType USING (typeid)
  WHERE
    $types_string
    Time_TO_SEC(SCH.starttime) > $time_start AND
    Time_TO_SEC(SCH.starttime) < $time_end

EOD;

if ($sessionid!="") {
  $query.=" AND sessionid=$sessionid";
 }
$query.=" ORDER BY S.title";

// Retrive query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

// Fix the questiontypeid for a single page
if ($sessionid!="") {$questiontypeid=$element_array[1]['questiontypeid'];}
  
if ($elements > 0) {

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
}

$formheaders="  <TR><TH>&nbsp;</TH><TH>Totally Agree</TH><TH>Somewhat Agree</TH><TH>Neutral</TH>";
$formheaders.="<TH>Somewhat Disagree</TH><TH>Totally Disagree</TH></TR>";

$formstring.="<P>&nbsp;&nbsp;Please answer the following questions from totally agree to totally disagree.";
$formstring.="<TABLE border=1>";
$formstring.=$formheaders."\n";
for ($i=1; $i<=$questioncount; $i++) {
  if ($question_array[$i]['questiontypeid'] == $questiontypeid) {
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