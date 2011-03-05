<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
  } else {
  require_once('PartCommonCode.php');
  }
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');

/* Global Variables */
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$logo=CON_LOGO; // make it a variable so it can be substituted

// LOCALIZATIONS
$NumOfColumns=3; // Number of columns at the top of the page.
$_SESSION['return_to_page']="StaffFeedback.php";
$print_p=$_GET['print_p'];
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
  <LI><A HREF="StaffFeedback.php?selday=Friday">Friday</A>
  <LI><A HREF="StaffFeedback.php?selday=Saturday Early">Saturday Early</A>
  <LI><A HREF="StaffFeedback.php?selday=Saturday Late">Saturday Late</A>
  <LI><A HREF="StaffFeedback.php?selday=Sunday">Sunday</A>
</UL>
<?php
  correct_footer();
  exit();
 }
  

$title=CON_NAME." $dayname Feedback";
$description="<P>Not sure which class?  Check the <A HREF=StaffDescriptions.php>descriptions</A>, <A HREF=StaffBios.php>bios</A>, <A HREF=StaffSchedule.php>timeslots</A>, or <A HREF=StaffTracks.php>tracks</A> pages.</P>";
$additionalinfo="<P><A HREF=\"StaffFeedback.php?selday=$selday&print_p=y\">Printable</A> version.</P>\n";
$additionalinfo.="<P>Done with this time block?  Pick a different one:</P>\n";
$additionalinfo.="<UL>\n  <LI><A HREF=\"StaffFeedback.php?selday=Friday\">Friday</A>\n";
$additionalinfo.="  <LI><A HREF=\"StaffFeedback.php?selday=Saturday Early\">Saturday Early</A>\n";
$additionalinfo.="  <LI><A HREF=\"StaffFeedback.php?selday=Saturday Late\">Saturday Late</A>\n";
$additionalinfo.="  <LI><A HREF=\"StaffFeedback.php?selday=Sunday\">Sunday</A></UL>\n";

// Document information
class MYPDF extends TCPDF {
  public function Footer() {
    $this->SetY(-15);
    $this->SetFont("helvetica", 'I', 8);
    $this->Cell(0, 10, "Copyright 2011 New England Leather Alliance, a Coalition Partner of NCSF and a subscribing organization of CARAS", 'T', 1, 'C');
  }
}

$pdf = new MYPDF('p', 'mm', 'letter', true, 'UTF-8', false);
$pdf->SetCreator('Zambia');
$pdf->SetAuthor('Programming Team');
$pdf->SetTitle('Volunteer Introduction Sheets');
$pdf->SetSubject('Introductions for the Classes and Panels');
$pdf->SetKeywords('Zambia, Presenters, Volunteers, Introductions, Intros');
$pdf->SetHeaderData($logo, 70, CON_NAME, CON_URL);
$pdf->setHeaderFont(Array("helvetica", '', 10));
$pdf->setFooterFont(Array("helvetica", '', 8));
$pdf->SetDefaultMonospacedFont("courier");
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_HEADER, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 8, '', true);
// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

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

/* Get the number of elements into $NumOfColumns rows */
$NumPerColumn=ceil($elements/$NumOfColumns);

/* Printing body. */
$printstring="<TABLE border=\"0\" cellpadding=\"4\"><TR><TD colspan=\"$NumOfColumns\" align=\"center\">Please, indicate the $dayname class you are offering feedback on.</TD></TR>";
$printstring.="<TR><TD>";
$formstring.="<FORM name=\"feedbackform\" method=POST action=\"StaffFeedback.php?selday=$selday\">\n";
if ($sessionid!="") {
  $formstring.="<INPUT type=\"hidden\" name=\"selsess\" value=\"".$element_array[1]['sessionid']."\">\n";
  $formstring.="<P>Feedback on ".$element_array[1]['title']." (".$element_array[1]['time'].")</P>\n";
 } else {
  $formstring.="<DIV><LABEL for=\"feedbackclass\">Select the $dayname class you are offering feedback on.</LABEL>\n";
  $formstring.="<SELECT name=\"selsess\">\n";
  $formstring.="    <OPTION value=0 SELECTED>Select Session</OPTION>\n";
  for ($i=1; $i<=$elements; $i++) {
    $printstring.="<img border=\"1\" src=\"images/whitebox.png\"> ";
    $printstring.=$element_array[$i]['title']." (".$element_array[$i]['time'].")<br>";
    $formstring.="    <OPTION value=\"".$element_array[$i]['sessionid']."\">";
    $formstring.=$element_array[$i]['title']." (".$element_array[$i]['time'].")</OPTION>\n";
    if ($i % $NumPerColumn == 0) {
      $printstring.="</TD><TD>";
    }
  }
  $printstring.="</TD></TR>";
  $printstring.="</TABLE>";
  $formstring.="</SELECT></DIV>\n";
 }

$printheaders="  <TR><TH colspan=\"2\">&nbsp;</TH><TH align=\"center\">Totally Agree</TH>";
$printheaders.="<TH align=\"center\">Somewhat Agree</TH><TH align=\"center\">Neutral</TH>";
$printheaders.="<TH align=\"center\">Somewhat Disagree</TH><TH align=\"center\">Totally Disagree</TH></TR>";
$printchoices="<TD align=\"center\">5</TD><TD align=\"center\">4</TD><TD align=\"center\">3</TD>";
$printchoices.="<TD align=\"center\">2</TD><TD align=\"center\">1</TD></TR>";

$formheaders="  <TR><TH>&nbsp;</TH><TH>Totally Agree</TH><TH>Somewhat Agree</TH><TH>Neutral</TH>";
$formheaders.="<TH>Somewhat Disagree</TH><TH>Totally Disagree</TH></TR>";

$printstring.="<TABLE border=\"1\">";
$printstring.="<TR><TD colspan=\"7\" align=\"center\">Please answer the following questions where 5 = totally agree, 1 = totally disagree.</TD></TR>";
$formstring.="<P>&nbsp;&nbsp;Please answer the following questions from totally agree to totally disagree.";
$formstring.="<TABLE border=1>";
$printstring.=$printheaders;
$formstring.=$formheaders."\n";
for ($i=1; $i<=$questioncount; $i++) {
  $printstring.="  <TR><TD colspan=\"2\">".$question_array[$i]['questiontext'].":<br>&nbsp;</TD>".$printchoices;
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
$printstring.="</TABLE></P><hr>";
$formstring.="</TABLE></P>\n";
$formstring.="<LABEL for=\"classcomment\">Other comments on this class:</LABEL>\n<br>\n";
$formstring.="  <TEXTAREA name=\"classcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
$formstring.="<LABEL for=\"progcomment\">Comments on the FFF in general:</LABEL>\n<br>\n";
$formstring.="  <TEXTAREA name=\"progcomment\" rows=6 cols=72></TEXTAREA>\n<br>\n";
$formstring.="<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Send Feedback</BUTTON>\n";
$formstring.="</FORM>\n";
$printstring.="<P>Other comments/ideas/questions/feedback about the class or the flea:";

if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo);
  echo $formstring;
  correct_footer();
 } else {
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=true, $fill=false, $reseth=true, $align='', $autopadding=true);
  $pdf->writeHTMLCell($w=0, $h=0, $x=PDF_MARGIN_LEFT, $y=144, $printstring, $border=0, $ln=true, $fill=false, $reseth=true, $align='', $autopadding=true);
  $pdf->Output($dayname.'Feedback.pdf', 'I');
 }
?>