<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
  } else {
  require_once('PartCommonCode.php');
  }
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$logo=CON_LOGO; // make it a variable so it can be substituted

## LOCALIZATIONS
$_SESSION['return_to_page']="WelcomeLettersPrint.php";
$title="Welcome Letters Printing";
$print_p=$_GET['print_p'];
$individual=$_GET['individual'];

## If the individual isn't a staff member, only serve up their schedule information
if ($_SESSION['role']=="Participant") {$individual=$_SESSION['badgeid'];}

$description="<P>A way to <A HREF=\"WelcomeLettersPrint.php?print_p=T";
if ($individual != "") {$description.="&individual=$individual";}
$description.="\">print</A> the appropriate Welcome letter";
if ($individual == "") {$description.="s";}
$description.=".</P>\n<hr>\n";

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
$pdf->SetTitle('Welcome Letters');
$pdf->SetSubject('Welcome Letters for our Participants');
$pdf->SetKeywords('Zambia, Presenters, Volunteers, Welcome Letters');
$pdf->SetHeaderData($logo, 70, CON_NAME, CON_URL);
$pdf->setHeaderFont(Array("helvetica", '', 10));
$pdf->setFooterFont(Array("helvetica", '', 8));
$pdf->SetDefaultMonospacedFont("courier");
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(true);
$pdf->SetFont('freesans', '', 12, '', true);

/* This query returns the  pubsname, and permroleid for the participants
 who are either Presenters or Volunteers who are attending.  The
 UserHasPermissionRole is 3=Presenter 5=Volunteer, if this changes in
 the database, it should change here and other places, as well.  The
 individual switch lets us work from the premise of printing just one
 person's information. */
$query = <<<EOD
SELECT 
    P.pubsname,
    GROUP_CONCAT(UP.permroleid) as Role
  FROM
      Participants P
    JOIN UserHasPermissionRole UP USING (badgeid)
  WHERE
    (UP.permroleid=5 OR
     UP.permroleid=3) AND
    P.interested=1

EOD;
if ($individual) {$query.=" and
    P.badgeid='$individual'";}
$query.="
  GROUP BY
    P.pubsname
  ORDER BY
    P.pubsname";

## Retrieve query
list($rows,$participant_header,$participant_array)=queryreport($query,$link,$title,$description,0);

if ($print_p =="") {topofpagereport($title,$description,$additionalinfo);}

foreach ($participant_array as $participant) {
  // Generic header info.
  $printstring = "<P>&nbsp;</P><P>Dear ".$participant['pubsname'].",</P>";

  // Determine what letter.
  if (($participant['Role'] == "5,3") OR ($participant['Role'] == "3,5")) {
    if (file_exists("../Local/Verbiage/Welcome_Letter_Presenters_and_Volunteers_0")) {
      $printstring.= file_get_contents("../Local/Verbiage/Welcome_Letter_Presenters_and_Volunteers_0");
    }
  } elseif ($participant['Role'] == "5") {
    if (file_exists("../Local/Verbiage/Welcome_Letter_Volunteers_0")) {
      $printstring.= file_get_contents("../Local/Verbiage/Welcome_Letter_Volunteers_0");
    }
  } elseif ($participant['Role'] == "3") {
    if (file_exists("../Local/Verbiage/Welcome_Letter_Presenters_0")) {
      $printstring.= file_get_contents("../Local/Verbiage/Welcome_Letter_Presenters_0");
    }
  }

  // Display, with the option of printing.
  if ($print_p == "") {
    echo "$printstring<hr>";
  } else {
    $pdf->AddPage();
    $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
  }
 }

if ($print_p == "") {
  staff_footer();
 } else {
  if ($individual != "") {
    $pdf->Output('WelcomeLetterFor'.$participant_array[1]['pubsname'].'.pdf', 'I');
  } else {
    $pdf->Output('AllWelcomeLetters.pdf', 'I');
  }
 }

?>
