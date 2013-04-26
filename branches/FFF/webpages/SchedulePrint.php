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
$conid=$_SESSION['conid'];
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$logo=CON_LOGO; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="SchedulePrint.php";
$title="Schedule Printing";
$print_p=$_GET['print_p'];
$individual=$_GET['individual'];
$group=$_GET['group'];

// If the individual isn't a staff member, only serve up their schedule information
if ($_SESSION['role']=="Participant") {$individual=$_SESSION['badgeid'];}

// If an individual request, make sure it pulls all of the schedule for that person
if ($individual != "") {$group = "Participant','Programming','SuperProgramming','General','Watch','Registration','Vendor','Events','Logistics','Sales','Fasttrack";}

// If no group is set, presume that you want the Participants
if ($group == "") {$group='Participant';}

$description="<P>A way to <A HREF=\"SchedulePrint.php?print_p=T&group=$group";
if ($individual != "") {$description.="&individual=$individual";}
$description.="\">print</A> the appropriate schedule";
if ($individual == "") {$description.="s";}
$description.=".  <A HREF=\"StaffAssignParticipants.php\">Adjust</A> the results.</P>\n<hr>\n";

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

/* This query returns the title with any role appended followed by the
 time of the class the duration and the room, as well as the pubsname
 for the participants who are either Presenters or Presenters,
 Programming Volunteers and General Volunteers.  The individual switch
 lets us work from the premise of printing just one person's
 information. */
$query = <<<EOD
SELECT 
    DISTINCT CONCAT(S.title, 
        if((moderator=1),' (moderating)',''), 
        if ((aidedecamp=1),' (assisting)',''), 
        if((volunteer=1),' (outside wristband checker)',''), 
        if((introducer=1),' (announcer/inside room attendant)',''),
        ' - ',
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),
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
    JOIN ParticipantOnSession POS USING (sessionid)
    JOIN $ReportDB.Participants P USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles PR USING (permroleid)
  WHERE
    permrolename in ('$group') AND
    UHPR.conid=$conid
EOD;
if ($individual) {$query.=" and
    badgeid='$individual'";}
$query.="
  ORDER BY
    starttime";

// Retrieve query
list($rows,$schedule_header,$schedule_array)=queryreport($query,$link,$title,$description,0);

for ($i=1; $i<=$rows; $i++) {
  $name=$schedule_array[$i]['pubsname'];
  $participant_array[$name]['name']=$schedule_array[$i]['pubsname'];
  $participant_array[$name]['schedule'].="<P>".$schedule_array[$i]['Title']."</P>";
 }

// Reorder so they are alphabetical by the pubsname (keys)
ksort($participant_array);

foreach ($participant_array as $participant) {
  // Generic header info.
  $printstring.= "<P>&nbsp;</P><P>Greetings ".$participant['name'].",</P>";

  // Pull in the intro-blurb.
  if (file_exists("../Local/Verbiage/Schedule_Blurb_0")) {
    $printstring.= file_get_contents("../Local/Verbiage/Schedule_Blurb_0");
  }

  // Add the schedule.
  $printstring.= $participant['schedule'];

  // If not on paper, add a <hr>, else a new page.
  if ($print_p == "") {
    echo "$printstring<hr>\n";
  }
  else {
    $pdf->AddPage();
  }
}

// Display, with the option of printing.
if ($print_p == "") {
  topofpagereport($title,$description,$additionalinfo);
  echo "$printstring";
  correct_footer();
} else {
  $pdf->AddPage();
  $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
  if ($individual != "") {
    $pdf->Output('Schedule'.$name.'.pdf', 'I');
  } else {
    $pdf->Output('ScheduleAll.pdf', 'I');
  }
}
?>
