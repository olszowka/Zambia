<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
  } else {
  require_once('PartCommonCode.php');
  }
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

## LOCALIZATIONS
$_SESSION['return_to_page']="SchedulePrint.php";
$title="Schedule Printing";
$logo="../../../images/nelaLogoHeader.gif";
$print_p=$_GET['print_p'];
$individual=$_GET['individual'];

## If the individual isn't a staff member, only serve up their schedule information
if ($_SESSION['role']=="Participant") {$individual=$_SESSION['badgeid'];}

$description="<P>A way to <A HREF=\"SchedulePrint.php?print_p=T";
if ($individual != "") {$description.="&individual=$individual";}
$description.="\">print</A> the appropriate schedule";
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
$pdf->SetTitle('Personal Schedule Information');
$pdf->SetSubject('Schedules for each individual.');
$pdf->SetKeywords('Zambia, Presenters, Volunteers, Schedules');
$pdf->SetHeaderData($logo, 70, CON_NAME, "nelaonline.org/Zambia-FFF36");
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

/* This query returns the badgeid, email, pubsname, and permroleid for
 the participants who are either Presenters or Volunteers The
 UserHasPermissionRole is 3=Presenter 5=Volunteer, if this changes in
 the database, it should change here and other places, as well.  The
 individual switch lets us work from the premise of printing just one
 person's information. */
$query = <<<EOD
SELECT 
    DISTINCT CONCAT(S.title, 
        if((moderator=1),' (moderating)',''), 
        if ((aidedecamp=1),' (assisting)',''), 
        if((volunteer=1),' (outside wristband checker)',''), 
        if((introducer=1),' (announcer/inside room attendant)',''),
        ' - ',
        DATE_FORMAT(ADDTIME('2010-02-12 00:00:00',starttime),'%a %l:%i %p'),
        ' - ',
        CASE
          WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
          WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
          ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
          END,
        ' in room ',
	roomname) as Title,
    P.pubsname
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS USING (sessionid)
    LEFT JOIN Participants P USING (badgeid)
    LEFT JOIN UserHasPermissionRole UP USING (badgeid)
  WHERE
    (UP.permroleid=5 or
     UP.permroleid=3)
EOD;
if ($individual) {$query.=" and
    POS.badgeid='$individual'";}
$query.="
  ORDER BY
    starttime";

## Retrieve query
list($rows,$schedule_header,$schedule_array)=queryreport($query,$link,$title,$description,0);

if ($print_p =="") {topofpagereport($title,$description,$additionalinfo);}
for ($i=1; $i<=$rows; $i++) {
  $name=$schedule_array[$i]['pubsname'];
  $participant_array[$name]['name']=$schedule_array[$i]['pubsname'];
  $participant_array[$name]['schedule'].="<P>".$schedule_array[$i]['Title']."</P>";
 }

// Reorder so they are alphabetical by the pubsname (keys)
ksort($participant_array);

foreach ($participant_array as $participant) {
  // Generic header info.
  $printstring = "<P>&nbsp;</P><P>Greetings ".$participant['name'].",</P>";

  // Pull in the intro-blurb.
  if (file_exists("../Local/Verbiage/Schedule_Blurb_0")) {
    $printstring.= file_get_contents("../Local/Verbiage/Schedule_Blurb_0");
  }

  // Add the schedule.
  $printstring.= $participant['schedule'];

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
    $pdf->Output('Schedule'.$name.'.pdf', 'I');
  } else {
    $pdf->Output('ScheduleAll.pdf', 'I');
  }
 }

?>
