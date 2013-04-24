<?php
require_once('StaffCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
global $link;
$logo=CON_LOGO; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="genreport.php?reportname=meetingagendadisplay";
$title="Agenda Printing";
$print_p=$_GET['print_p'];

if (isset($_GET['agendaid'])) {
  $agendaid=$_GET['agendaid'];
 } elseif (isset($_POST['agendaid'])) {
  $agendaid=$_POST['agendaid'];
 } else {
  $description="<P>Please select which agenda you wish to be able to print.</P>\n";
  topofpagereport($title,$description,$additionalinfo);

$query=<<<EOD
SELECT
    agendaid,
    concat(permrolename, ": ", agendaname, " at ", meetingtime)
  FROM
      AgendaList
    JOIN $ReportDB.PermissionRoles USING (permroleid)
  ORDER BY
    permrolename,
    agendaname
EOD;
  ?>

<FORM name="selagendaform" method=POST action="MeetingAgendaPrint.php">
<DIV><LABEL for="agendaid">Select Agenda</LABEL>
<SELECT name="agendaid">
<?php populate_select_from_query($query, 0, "Select Agenda from the List Below", true); ?>
</SELECT>
</DIV>
<DIV class="SubmitDiv">
<BUTTON type="submit" name="submit" class="SubmitButton">Select</BUTTON>
</DIV>
</FORM>
<?php
  correct_footer();
  exit();
 }

$description="<P>A way to <A HREF=\"MeetingAgendaPrint.php?print_p=T&agendaid=$agendaid\">print</A> the appropriate agenda</P>\n<hr>\n";

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
$pdf->SetTitle('Personal Schedule Information');
$pdf->SetSubject('Schedules for each individual.');
$pdf->SetKeywords('Zambia, Presenters, Volunteers, Schedules');
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
$pdf->SetFont('times', '', 12, '', true);

// Get the agenda, and attendent information
$query = <<<EOD
SELECT
    agendaname,
    agenda,
    permrolename,
    agendanotes,
    meetingtime
  FROM
      AgendaList
    JOIN $ReportDB.PermissionRoles USING (permroleid)
  WHERE
    agendaid='$agendaid'

EOD;

// Retrieve query
list($rows,$agenda_header,$agenda_array)=queryreport($query,$link,$title,$description,0);

if ($print_p =="") {topofpagereport($title,$description,$additionalinfo);}


// Generic header info, including name and time.
$printstring = "<P>&nbsp;</P><P>Greetings ".$agenda_array[1]['permrolename'].",</P>";
$printstring.= "<P>Here is the agenda (and agenda notes, if the meeting has occured)\n";
$printstring.= " for the ".$agenda_array[1]['permrolename'].": ".$agenda_array[1]['agendaname']."\n";
$printstring.= " occuring/occured on ".$agenda_array[1]['meetingtime'].".</P>\n";

// Add the agenda.
$printstring.= "<P>".$agenda_array[1]['agenda']."</P>\n";

// Add the meeting notes if they exist.
if ($agenda_array[1]['agendanotes']!="") {
  $printstring.="<hr>\n<P>NOTES:</P>\n<P>".$agenda_array[1]['agendanotes']."</P>\n";
 }

// Display, with the option of printing.
if ($print_p == "") {
  echo "$printstring";
 } else {
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
 }

if ($print_p == "") {
  correct_footer();
 } else {
$pdf->Output('Schedule'.$agenda_array[1]['permrolename']."_".$agenda_array[1]['agendaname'].'.pdf', 'I');
 }

?>
