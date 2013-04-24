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
$_SESSION['return_to_page']="ClassIntroPrint.php";
$title="Class Introduction Printing";
$print_p=$_GET['print_p'];
$individual=$_GET['individual'];
$print_short=$_GET['print_short'];

// If the individual isn't a staff member, only serve up their schedule information
if ($_SESSION['role']=="Participant") {$individual=$_SESSION['badgeid'];}

$description="<P>A way to <A HREF=\"ClassIntroPrint.php?print_p=T";
if ($individual != "") {$description.="&individual=$individual";}
$description.="\">print</A> the appropriate Class/Panel introduction(s).</P>\n<hr>\n";

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
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setLanguageArray($l);
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 10, '', true);

/* This query returns the pubsname, title of the class, Start time,
 Room Name, and Sessionid for Classes/Panels only at this time, for
 the Volunteer, who is announcing it.  This now uses the typename,
 and we are looking for Panel and Class, if this changes, it should
 change here and other places as well.  The individual switch allows
 us to print one person's information, as well. */
$query = <<<EOD
SELECT
    pubsname, 
    title,
    DATE_FORMAT(ADDTIME("$ConStartDatim",starttime), '%a %l:%i %p') as StartTime,
    roomname,
    sessionid,
    typename
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN ParticipantOnSession POS USING (sessionid)
    JOIN $ReportDB.Participants P USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles PR USING (permroleid)
    JOIN $ReportDB.Types T USING (typeid)
  WHERE
    permrolename in ('Programming') and
    introducer=1 and
    UHPR.conid=$conid and
    typename in ('Panel','Class')
EOD;

if ($individual) {$query.=" and
    POS.badgeid='$individual'";}
$query.="
  ORDER BY
    pubsname, SCH.starttime";

// Retrieve query
list($classcount,$classcount_header,$classlist_array)=queryreport($query,$link,$title,$description,0);

/* Get the Bio(s) of the presenter(s). This is currently skipped
 if it is a panel, and let them introduce themselves. */
$query1 = <<<EOD
SELECT 
    P.pubsname,
    BWE.biotext,
    S.sessionid,
    POS.moderator
  FROM
      Sessions S
    LEFT JOIN ParticipantOnSession POS ON S.sessionid=POS.sessionid
    LEFT JOIN $ReportDB.Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN (SELECT 
                   badgeid,
                   biotext
                 FROM
                     $BioDB.Bios
                   JOIN $BioDB.BioTypes USING (biotypeid)
                   JOIN $BioDB.BioStates USING (biostateid)
                 WHERE
                   biotypename in ('web') AND
	           biostatename in ('edited')) BWE on P.badgeid=BWE.badgeid
  WHERE
    POS.aidedecamp=0 AND
    POS.volunteer=0 AND
    POS.introducer=0
  ORDER BY
    POS.moderator DESC
EOD;

// Retrieve query
list($presentercount,$presentercount_header,$presenter_array)=queryreport($query1,$link,$title,$description,0);

// Grab the intro blurb, assign it to $intro
if (file_exists("../Local/Verbiage/Introduction_Blurb_0")) {
  $intro= file_get_contents("../Local/Verbiage/Introduction_Blurb_0");
 }

// Grab the Volunteer duty description, assign it to $roles
if (file_exists("../Local/Verbiage/Volunteer_Jobs_0")) {
  $roles= file_get_contents("../Local/Verbiage/Volunteer_Jobs_0");
 }

// setup for viewing instead of printing
if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo);
  echo "$roles<hr>";
 }

for ($i=1; $i<=$classcount; $i++) {
  $sessionid=$classlist_array[$i]['sessionid'];
  $name=$classlist_array[$i]['pubsname'];
  $classname=$classlist_array[$i]['title'];
  $starttime=$classlist_array[$i]['StartTime'];
  $roomname=$classlist_array[$i]['roomname'];
  $typename=$classlist_array[$i]['typename'];

  // Generic header info.
  $printstring = "<P>&nbsp;</P><P>$name, this is the information for:</P>";
  $printstring.= "<P><TABLE border=\"1\" cellspacing=\"3\" cellpadding=\"4\">";
  $printstring.= "<TR><TD colspan=\"2\"><H2>$classname</H2></TD><TD><H2>$starttime</H2></TD><TD><H2>$roomname</H2></TD></TR>";
  $printstring.= "<TR><TD>30 minute<br>headcount</TD><TD></TD><TD>60 minute<br>headcount</TD><TD></TD></TR></TABLE></P>";
  $printstring.= "<P>Introduction:</P>";
  $printstring.= "<P>Hi and welcome!  How is everybody doing?</P>";
  $printstring.= "<P>I'm $name ";

  // Pull in the intro-blurb.
  $printstring.=$intro;

  // Add the Name(s) and Bio(s) of the Presenter(s).
  $bios="";
  for ($j=1; $j<=$presentercount; $j++) {
    if ($presenter_array[$j]['sessionid'] == $sessionid) {
      if (($typename == "Panel") AND ($presenter_array[$j]['moderator'] == "1")) {
	$bios="<P>I'd like to turn this over to ".$presenter_array[$j]['pubsname'];
        $bios.=", our moderator, for the $classname.</P>";
      }
      if ($typename == "Class") {
	$bios.="<P>".$presenter_array[$j]['pubsname']." ".$presenter_array[$j]['biotext']."</P>";
      }
    }
  }
  if ($bios == "") {
    $bios="<P>I'd like to turn this over to our Presenter(s), for the $classname.</P>";
  }
  $printstring.=" $bios";
  if ($print_p == "") {
    echo "$printstring<hr>";
  } else {
    if ($print_short != "True") {
      if ($classlist_array[$i-1]['pubsname'] != $name) {
        $pdf->AddPage();
        $pdf->writeHTML($roles, true, false, true, false, '');
        }
      }
    $pdf->AddPage();
    $pdf->writeHTML($printstring, true, false, true, false, '');
  }
 }

if ($print_p == "") {
  staff_footer();
 } else {
  if ($individual != "") {
    $pdf->Output('ClassIntro'.$name.'.pdf', 'I');
  } else {
    $pdf->Output('ClassIntroAll.pdf', 'I');
  }
 }

?>
