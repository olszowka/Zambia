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
$_SESSION['return_to_page']="WelcomeLettersPrint.php";
$title="Welcome Letters Printing";
$print_p=$_GET['print_p'];
$individual=$_GET['individual'];
$type_array=array("'Panel'","'Class'","'Author Reading'","'SIG/BOF/MnG'","'Lounge'");
$role_array=array("'Participant'","'Programming'","'SuperProgramming'");
$type_string=implode(",",$type_array);
$role_string=implode(",",$role_array);

$replace_array = array("/"," ");
$drop_array = array("'");

foreach ($role_array as $role) {
  $firstrole = str_replace($drop_array,"",$role);
  $rolecheck_array[$role]=$firstrole;
  $secondrole = str_replace($replace_array,"_",$firstrole);
  $rolename_array[$role]=$secondrole;
}
foreach ($type_array as $type) {
  $firsttype = str_replace($drop_array,"",$type);
  $typecheck_array[$type]=$firsttype;
  $secondtype = str_replace($replace_array,"_",$firsttype);
  $typename_array[$type]=$secondtype;
}

// If the individual isn't a staff member, only serve up their schedule information
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

/* This query returns the pubsname, and permrolename for the
 participants who are either Presenters or Volunteers who are
 attending.  The individual switch lets us work from the premise of
 printing just one person's information. */

$query = <<<EOD
SELECT 
    pubsname,
    GROUP_CONCAT(DISTINCT permrolename) as Role,
    GROUP_CONCAT(DISTINCT typename) as Type
  FROM
      ParticipantOnSession
    JOIN $ReportDB.Participants USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
    JOIN Sessions USING (sessionid)
    JOIN $ReportDB.Types USING (typeid)
  WHERE
    UHPR.conid=$conid AND
    permrolename IN ($role_string) AND
    typename IN ($type_string)

EOD;
if ($individual) {$query.=" and
    badgeid='$individual'";}
$query.="
  GROUP BY
    pubsname
  ORDER BY
    pubsname";

// Retrieve query
list($rows,$participant_header,$participant_array)=queryreport($query,$link,$title,$description,0);

if ($print_p =="") {topofpagereport($title,$description,$additionalinfo);}

foreach ($participant_array as $participant) {
  $printstring = "<P>&nbsp;</P><P>Dear ".$participant['pubsname'].",</P>";
  if (file_exists("../Local/Verbiage/Welcome_Letter_0")) {
    $printstring .= file_get_contents("../Local/Verbiage/Welcome_Letter_0");
  }
  foreach ($role_array as $role) {
    foreach ($type_array as $type) {
      $filename="../Local/Verbiage/Welcome_Letter_".$rolename_array[$role]."_".$typename_array[$type]."_0";
      if ((strpos($participant['Role'],$rolecheck_array[$role]) !== false) AND (strpos($participant['Type'],$typecheck_array[$type]) !== false)) {
	if (file_exists($filename)) {      
	  $checkstring = file_get_contents($filename);
          if (strpos($printstring,$checkstring) === false) { $printstring .= $checkstring; }
	}
      }
    }
  }
  if (file_exists("../Local/Verbiage/Welcome_Letter_1")) {
    $printstring .= file_get_contents("../Local/Verbiage/Welcome_Letter_1");
  }
  if ($print_p == "") {
    echo "$printstring\n<hr>\n";
    correct_footer();
  } else {
    $pdf->AddPage();
    $pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $printstring, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
  }
  if ($individual != "") {
    $pdf->Output('WelcomeLetterFor'.$participant_array[1]['pubsname'].'.pdf', 'I');
  } else {
    $pdf->Output('AllWelcomeLetters.pdf', 'I');
  }
 }

?>
