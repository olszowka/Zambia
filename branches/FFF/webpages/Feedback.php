<?php
require_once('StaffCommonCode.php');
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');
require_once('tmp_chart-3.php');

/* Global Variables */
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

// LOCALIZATIONS
$NumOfColumns=3; // Number of columns at the top of the page.
$_SESSION['return_to_page']="Feedback.php";
$logo="../../../images/nelaLogoHeader.gif";
$print_p=$_GET['print_p'];
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
 }

$title=CON_NAME." $dayname Feedback";
$description="<P>Please, indicate the $dayname class you are offering feedback on.</P>\n";
$additionalinfo="<P><A HREF=\"Feedback.php?selday=$selday&print_p=y\">Printable</A> version.</P>\n";

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
$pdf->SetHeaderData($logo, 70, CON_NAME, "nelaonline.org/Zambia-FFF36");
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
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime), '%l:%i %p') as time
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
  WHERE
    (typeid = 1 OR
     typeid = 2) AND
    Time_TO_SEC(SCH.starttime) > $time_start AND
    Time_TO_SEC(SCH.starttime) < $time_end
  ORDER BY
    S.title

EOD;

// Retrive query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

/* Get the number of elements into $NumOfColumns rows */
$NumPerColumn=ceil($elements/$NumOfColumns);

/* This query pulls the questions, to be surveyed */
$query=<<<EOD
SELECT
   questiontext
  FROM
      QuestionsForSurvey
  ORDER BY
    display_order

EOD;

// Retrive query
list($questioncount,$header_array1,$question_array)=queryreport($query,$link,$title,$description,0);

/* Printing body.  Uses the page-init from above adds informational line
 then creates the Descriptions. */
$printstring="<TABLE border=\"0\" cellpadding=\"4\"><TR><TD colspan=\"$NumOfColumns\" align=\"center\">Please, indicate the $dayname class you are offering feedback on.</TD></TR>";
$printstring.="<TR><TD>";
$printstring1 ="<TABLE border=0>\n  <TR>\n    <TD>\n    <UL>\n      <UL>\n";
for ($i=1; $i<=$elements; $i++) {
  $printstring.="<img border=\"1\" src=\"images/whitebox.png\"> ";
  $printstring.=$element_array[$i]['title']." (".$element_array[$i]['time'].")<br>";
  $printstring1.="  <LI><B>".$element_array[$i]['title']."</B> (".$element_array[$i]['time'].")\n";
  if ($i % $NumPerColumn == 0) {
    $printstring.="</TD><TD>";
    $printstring1.="      </UL>\n    </UL>\n    </TD>\n";
    $printstring1.="    <TD>\n    <UL>\n      <UL>\n";
  }
 }
$printstring.="</TD></TR>";
$printstring.="</TABLE>";
$printstring1.="      </UL>\n    </UL>\n    </TD>\n  </TR>\n</TABLE>\n";
$printstring1.="<hr>\n";

$headers="  <TR><TH colspan=\"2\">&nbsp;</TH><TH align=\"center\">Totally Agree</TH><TH align=\"center\">Somewhat Agree</TH><TH align=\"center\">Neutral</TH><TH align=\"center\">Somewhat Disagree</TH><TH align=\"center\">Totally Disagree</TH></TR>";
$headers1="  <TR><TH>&nbsp;</TH><TH>Totally Agree</TH><TH>Somewhat Agree</TH><TH>Neutral</TH><TH>Somewhat Disagree</TH><TH>Totally Disagree</TH></TR>";
$choices="<TD align=\"center\">5</TD><TD align=\"center\">4</TD><TD align=\"center\">3</TD><TD align=\"center\">2</TD><TD align=\"center\">1</TD></TR>";

$printstring.="<TABLE border=\"1\">";
$printstring.="<TR><TD colspan=\"7\" align=\"center\">Please answer the following questions where 5 = totally agree, 1 = totally disagree.</TD></TR>";
$printstring1.="<P>&nbsp;&nbsp;Please answer the following questions where 5 = totally agree, 1 = totally disagree.";
$printstring1.="<TABLE border=1>";
$printstring.=$headers;
$printstring1.=$headers1."\n";
for ($i=1; $i<=$questioncount; $i++) {
  $printstring.="  <TR><TD colspan=\"2\">".$question_array[$i]['questiontext'].":<br>&nbsp;</TD>".$choices;
  $printstring1.="  <TR><TD>".$question_array[$i]['questiontext'].":<br>&nbsp;</TD>".$choices."\n";
 }
$printstring.="</TABLE></P><hr>";
$printstring1.="</TABLE></P>\n<hr>\n";
$printstring.="<P>Other Comments:";
$printstring1.="<P>Other Comments:</P>";

// Test data
$info['q1']="12";
$info['q2']="30";
$info['q3']="21";
$info['q4']="40";
$info['q5']="52";

if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo);
  echo $printstring1;
  //echo "<img border=\"0\" src=\"tmp_chart-4.php?sessionid=$sessionid\">";
  //echo "<img border=\"0\" src=\"tmp_chart-3.php?graphvalues=".$info."\">";
  //echo "<img border=\"0\" src=\"thermometer.php?Current=3000&Goal=10000&Width=60&Height=150&Font=1\">";
  staff_footer();
 } else {
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=true, $fill=false, $reseth=true, $align='', $autopadding=true);
  $pdf->writeHTMLCell($w=0, $h=0, $x=PDF_MARGIN_LEFT, $y=144, $printstring, $border=0, $ln=true, $fill=false, $reseth=true, $align='', $autopadding=true);
  $pdf->Output($dayname.'Feedback.pdf', 'I');
 }

?>