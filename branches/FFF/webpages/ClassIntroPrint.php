<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
  } else {
  require_once('PartCommonCode.php');
  }
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');
//require_once('Test_PDF.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

## LOCALIZATIONS
$_SESSION['return_to_page']="ClassIntroPrint.php";
$title="Class Introduction Printing";
$logo="../../../images/nelaLogoHeader.gif";
$print_p=$_GET['print_p'];
$individual=$_GET['individual'];

## If the individual isn't a staff member, only serve up their schedule information
if ($_SESSION['role']=="Participant") {$individual=$_SESSION['badgeid'];}

$description="<P>A way to <A HREF=\"ClassIntroPrint.php?print_p=T";
if ($individual != "") {$description.="&individual=$individual";}
$description.="\">print</A> the appropriate Class/Panel introduction(s).</P>\n<hr>\n";

// Document information
$pdf = new TCPDF('p', 'mm', 'letter', true, 'UTF-8', false);
$pdf->SetCreator('Zambia');
$pdf->SetAuthor('Programming Team');
$pdf->SetTitle('Volunteer Introduction Sheets');
$pdf->SetSubject('Introductions for the Classes and Panels');
$pdf->SetKeywords('Zambia, Presenters, Volunteers, Introductions, Intros');
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
$pdf->SetFont('helvetica', '', 10, '', true);

/* This query returns the pubsname, title of the class, Start time,
 Room Name, and Sessionid for Classes/Panels only at this time, for
 the Volunteer, who is announcing it.  The type is 1=panel 2=class, if
 this changes in the database, it should change here and other places,
 as well.  The individual switch allows us to print one person's
 information, as well. */
$query = <<<EOD
SELECT
    P.pubsname, 
    S.title,
    DATE_FORMAT(ADDTIME('2010-02-12 00:00:00',SCH.starttime), '%a %l:%i %p') as StartTime,
    R.roomname,
    S.sessionid,
    S.typeid
  FROM
      ParticipantOnSession POS, 
      Sessions S, 
      Participants P,
      Schedule SCH,
      Rooms R,
      UserHasPermissionRole UP
  WHERE
    POS.badgeid=P.badgeid and
    POS.sessionid=S.sessionid and
    POS.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid and
    POS.badgeid=UP.badgeid and
    UP.permroleid=5 and
    POS.introducer=1 and
    (S.typeid=1 OR S.typeid=2)

EOD;

if ($individual) {$query.=" and
    POS.badgeid='$individual'";}
$query.="
  ORDER BY
    pubsname, SCH.starttime";

## Retrieve query
list($classcount,$classcount_header,$classlist_array)=queryreport($query,$link,$title,$description,0);

/* Get the Bio(s) of the presenter(s). This is currently skipped
 if it is a panel, and let them introduce themselves. */
$query1 = <<<EOD
SELECT 
    P.pubsname,
    P.editedbio,
    S.sessionid,
    POS.moderator
  FROM
      Sessions S
    LEFT JOIN ParticipantOnSession POS ON S.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
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
  $type=$classlist_array[$i]['typeid'];

  // Generic header info.
  $printstring = "<P>&nbsp;</P><P>$name, this is the information for:</P>";
  $printstring.= "<P><TABLE border=\"1\" cellspacing=\"3\" cellpadding=;\"4\">";
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
      if (($type == "1") AND ($presenter_array[$j]['moderator'] == "1")) {
	$bios="<P>I'd like to turn this over to ".$presenter_array[$j]['pubsname'];
        $bios.=", our moderator, for the $classname.</P>";
      }
      if ($type == "2") {
	$bios.="<P>".$presenter_array[$j]['pubsname']." ".htmlspecialchars($presenter_array[$j]['editedbio'])."</P>";
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
    if ($classlist_array[$i-1]['pubsname'] != $name) {
      $pdf->AddPage();
      $pdf->writeHTML($roles, true, false, true, false, '');
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
