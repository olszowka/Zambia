<?php
require_once('StaffCommonCode.php');
global $link;

$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$GohBadgeList=GOH_BADGE_LIST; // make it a variable so it can be substituted
$mybadgeid=$_SESSION['badgeid']; // make it a simple variable so it can be substituted
$conid=$_SESSION['conid']; // make it a simple variable so it can be substituted

// Get the various length limits for substitution
$limit_array=getLimitArray();

// Tests for the substituted variables
if ($BioDB=="BIODB") {unset($BioDB);}
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($GohBadgeList=="GOH_BADGE_LIST") {$GohBadgeList=('');}

// LOCALIZATIONS
$showreport=0;
$reportid=$_GET["reportid"];
$reportname=$_GET["reportname"];
$title="General Report Generator";
$additionalinfo="";

// Check for addtion
if (isset($_POST['addto'])) {
  add_flow_report($_POST['addto'],$_POST['addphase'],"Personal","",$title,$description);
 }

// Switch on which way this is called
if (!$reportname) {
  $_SESSION['return_to_page']="genreport.php?reportid=$reportid";
  $description="<P>If you are seeing this, something failed trying to get report: $reportid.</P>\n";
 } else {
  $_SESSION['return_to_page']="genreport.php?reportname=$reportname";
  $description="<P>If you are seeing this, something failed trying to get report: $reportname.</P>\n";
  $showreport++;
 }
if ($reportid) {$showreport++;}

// No reportid, load the all-reports page
if ($showreport==0) {
  $_SESSION['return_to_page']="genreport.php";
  $title="List of all reports";
  $description="<P>Here is a list of all the reports that are available to be generated.</P>\n";
  $query = <<<EOD
SELECT
    concat("<A HREF=genreport.php?reportid=",reportid,">",reporttitle,"</A> (<A HREF=genreport.php?reportid=",reportid,"&csv=y>csv</A>)") AS Title,
    reportdescription AS Description
  FROM
      $ReportDB.Reports
  ORDER BY
    reportname
EOD;

  // Retrieve query
  list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

  // Page Rendering
  topofpagereport($title,$description,$additionalinfo);
  echo renderhtmlreport(1,$rows,$header_array,$report_array);
  correct_footer();
 } else {

  $query = <<<EOD
SELECT
    reportname,
    reportid,
    reporttitle,
    reportdescription,
    reportadditionalinfo,
    reportquery
  FROM
      $ReportDB.Reports
  WHERE
EOD;

  if (!$reportid) {
    $query.="\n    reportname = '$reportname'";
  } else {
    $query.="\n    reportid = '$reportid'";
  }

  // Retrieve query
  list($returned_reports,$reportnumber_array,$report_array)=queryreport($query,$link,$title,$description,0);

  $basereportid=$report_array[1]['reportid'];
  $mybadgeid=$_SESSION['badgeid'];

  // Get the personal flow previous and next
  $query = <<<EOD
SELECT
    reportid
  FROM
      $ReportDB.PersonalFlow
      LEFT JOIN $ReportDB.Phase P USING (phasetypeid)
  WHERE
    badgeid='$mybadgeid' AND
    (phasetypeid is null OR
     (phasestate = TRUE AND
      P.conid=$conid))
  ORDER BY
    pfloworder
EOD;

  // Retrieve query
  if (!$result=mysql_query($query,$link)) {
    $message.=$query."<BR>Error querying database.<BR>";
    RenderError($title,$message);
    exit();
  }

  while ($row=mysql_fetch_assoc($result)) {
    $pflow_array[]=$row['reportid'];
  }
  // Start with a blank $personal, walk the array, set the previous and next
  $personal="Personal Flow: ";
  for ($i=0; $i<count($pflow_array); $i++) {
    if ($pflow_array[$i]==$basereportid) {
      if ($i > 1) {
	$personal.="<A HREF=genreport.php?reportid=".$pflow_array[$i-1].">Prev</A> ";
      }
      if ($i < count($pflow_array)-1) {
	$personal.="<A HREF=genreport.php?reportid=".$pflow_array[$i+1].">Next</A>";
      }
    }
  }
  if ($personal=="Personal Flow: ") {$personal="";}

  // Get the Groups that this report is part of
  $query = <<<EOD
SELECT
    GF.gflowname
  FROM
      $ReportDB.Reports R,
      $ReportDB.GroupFlow GF
  WHERE
    R.reportid=GF.reportid AND
    R.reportid=$basereportid
EOD;

  // Retrieve query
  list($grouplistrows,$grouplistheader_array,$grouplistreport_array)=queryreport($query,$link,$title,$description,0);

  $groups="";

  // Iterate accross the Groups getting the previous and next
  for ($i=1; $i<=$grouplistrows; $i++) {
    $cgroup=$grouplistreport_array[$i]['gflowname'];
    $query = <<<EOD
SELECT
    DISTINCT reportid
  FROM
      $ReportDB.GroupFlow
      LEFT JOIN $ReportDB.Phase USING (phasetypeid)
  WHERE
    gflowname='$cgroup' AND
      (phasetypeid is null OR (phasestate = TRUE and conid=$conid))
  ORDER BY
    gfloworder
EOD;

    // Retrieve query
    list($gflowrows,$gflowheader_array,$gflow_array)=queryreport($query,$link,$title,$description,0);

    // Start with a blank $personal, walk the array, set the previous and next
    $cgroups=" $cgroup Flow: ";
    for ($j=1; $j<=$gflowrows; $j++) {
      if ($gflow_array[$j]['reportid']==$basereportid) {
	if ($j > 1) {
	  $cgroups.="<A HREF=genreport.php?reportid=".$gflow_array[$j-1]['reportid'].">Prev</A> ";
	}
	if ($j < $gflowrows) {
	  $cgroups.="<A HREF=genreport.php?reportid=".$gflow_array[$j+1]['reportid'].">Next</A>";
	}
      }
    }
    if ($cgroups==" $cgroup Flow: ") {$cgroups="";}
    $groups.=$cgroups;
  }

  for ($i=1; $i<=$returned_reports; $i++) {

    // Fix references in the string so variables can be substituted in.
    $report_array[$i]['reportquery']=eval("return<<<EOF\n".$report_array[$i]['reportquery']."\nEOF;\n");

    // Retrieve secondary query
    list($rows,$header_array,$class_array)=queryreport($report_array[$i]['reportquery'],$link,$report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$reportid);
    $report_array[$i]['reportadditionalinfo'].="<P><A HREF=\"genreport.php?reportid=".$report_array[$i]['reportid']."&csv=y\" target=_blank>csv</A> file\n";
    $report_array[$i]['reportadditionalinfo'].="<A HREF=\"genreport.php?reportid=".$report_array[$i]['reportid']."&print_p=y\" target=_blank>print</A> file</P>\n";
    $report_array[$i]['reportadditionalinfo'].="<P><FORM name=\"addto\" method=POST action=\"genreport.php?reportid=".$report_array[$i]['reportid']."\">";
    $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"hidden\" name=\"addto\" value=\"".$report_array[$i]['reportid']."\">";
    $report_array[$i]['reportadditionalinfo'].=" <INPUT type=submit value=\"Add\">";
    $report_array[$i]['reportadditionalinfo'].=" this report to your Personal Flow. (If you wish, put in the phase number: ";
    $report_array[$i]['reportadditionalinfo'].="<LABEL for=\"addphase\" ID=\"addphase\"></LABEL>";
    $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"text\" name=\"addphase\" size=\"1\">.)";
    $report_array[$i]['reportadditionalinfo'].="</FORM>\n";
    $report_array[$i]['reportadditionalinfo'].="<P>".$personal.$groups."</P>";
    if ($returned_reports > 1) {$report_array[$i]['reportadditionalinfo'].="<P>Report $i of $returned_reports</P>\n";}

    // Page Rendering
    if ($_GET["csv"]=="y") {
      topofpagecsv($report_array[$i]['reportname'].".csv");
      echo rendercsvreport(1,$rows,$header_array,$class_array);
    } elseif ($_GET["print_p"]=="y") {
        require_once('../../tcpdf/config/lang/eng.php');
        require_once('../../tcpdf/tcpdf.php');
	$logo=CON_LOGO;
	$pdf = new TCPDF('p', 'mm', 'letter', true, 'UTF-8', false);
	$pdf->SetCreator('Zambia');
	$pdf->SetAuthor('Programming Team');
	$pdf->SetTitle('Logistics Grid');
	$pdf->SetSubject('Logistics Grid');
	$pdf->SetKeywords('Zambia, Rooms, Logistics, Services, Features, Tech Notes, Grid');
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
	$htmlstring=renderhtmlreport(1,$rows,$header_array,$class_array);
	$pdf->AddPage();
	$pdf->writeHTML($htmlstring, true, false, true, false, '');
	$pdf->Output(CON_NAME.'-grid.pdf', 'I');
    } else {
      topofpagereport($report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$report_array[$i]['reportadditionalinfo']);
      echo renderhtmlreport(1,$rows,$header_array,$class_array);
      if ($i==$returned_reports) {
	correct_footer();
      }
    }
  }
 }
?>
