<?php
require_once('StaffCommonCode.php');
require_once('../../tcpdf/config/lang/eng.php');
require_once('../../tcpdf/tcpdf.php');
// require_once('ChartSVGFeedback.php');

/* Global Variables */
global $link;
$logo=CON_LOGO; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$conid=$_SESSION['conid'];  // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

/* Localizations */
$_SESSION['return_to_page']="FeedbackPrint.php";
$title="Feedback Printing";
$print_p=$_GET['print_p'];
$description="<P>A way to <A HREF=\"FeedbackPrint.php?print_p=T\">print</A> the feedback.</P>\n<hr>\n";

/* Populate feedback array */
$feedback_array=getFeedbackData("");

/* Get class data */
$query=<<<EOD
SELECT
    if ((pubsname is NULL), '', GROUP_CONCAT(DISTINCT concat(pubsname,if((moderator=1),'(m)','')) SEPARATOR ', ')) AS 'Participants',
    GROUP_CONCAT(DISTINCT DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') SEPARATOR ', ') AS 'Start Time',
    GROUP_CONCAT(DISTINCT trackname SEPARATOR ', ') as 'Track',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT roomname SEPARATOR ', ') AS Roomname,
    estatten AS Attended,
    Sessionid,
    if ((THQT.conid=$conid),if((THQT.questiontypeid IS NULL),"",THQT.questiontypeid),"") AS questiontypeid,
    Title,
    secondtitle AS Subtitle,
    concat(progguiddesc,'</P>') AS 'Web Description',
    concat(pocketprogtext,'</P>') AS 'Book Description'
  FROM
      Sessions S
    JOIN Schedule USING (sessionid)
    JOIN Rooms USING (roomid)
    JOIN $ReportDB.Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession USING (sessionid)
    LEFT JOIN $ReportDB.Participants USING (badgeid)
    LEFT JOIN $ReportDB.TypeHasQuestionType THQT USING (typeid)
    JOIN $ReportDB.PubStatuses USING (pubstatusid)
  WHERE
    pubstatusname in ('Public') AND
    (volunteer=0 OR volunteer IS NULL) AND
    (introducer=0 OR introducer IS NULL) AND
    (aidedecamp=0 OR aidedecamp IS NULL)
  GROUP BY
    sessionid
  ORDER BY
    S.title
EOD;

// Retrieve query
list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

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
$pdf->SetTitle('Feedback' . CON_NAME);
$pdf->SetSubject('Feedback for the Classes and Panels');
$pdf->SetKeywords('Zambia, Presenters, Feedback');
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
$pdf->SetFont('helvetica', '', 8, '', true);
$pdf->AddPage();

$printstring1 ="<svg xmlns=\"http://www.w3.org/2000/svg\" version=\"1.1\">\n";
$printstring1.="  <circle cx=\"100\" cy=\"50\" r=\"40\" stroke=\"black\"";
$printstring1.="  stroke-width=\"2\" fill=\"red\"/>\n";
$printstring1.="</svg>\n";

$printstring2=<<<EOD
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="738px" height="756px" version="1.1">
<g font-size="9px" font-family="helvetica" fill="#000">
<text x="52" y="423" text-anchor="end">0%-</text>
<text x="52" y="342" text-anchor="end">20%-</text>
<text x="52" y="261" text-anchor="end">40%-</text>
<text x="52" y="180" text-anchor="end">60%-</text>
<text x="52" y="99" text-anchor="end">80%-</text>
<text x="52" y="18" text-anchor="end">100%-</text>
<rect height="363.103448276" x="54" y="50.8965517241" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="27.9310344828" x="54" y="386.068965517" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.9655172414" x="54" y="400.034482759" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="94.5" y="432" text-anchor="middle">Q1</text>
<text x="94.5" y="450" text-anchor="middle">Out of 29</text>
<rect height="378" x="144" y="36" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="27" x="144" y="387" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<text x="184.5" y="432" text-anchor="middle">Q2</text>
<text x="184.5" y="450" text-anchor="middle">Out of 30</text>
<rect height="391.034482759" x="234" y="22.9655172414" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="13.9655172414" x="234" y="400.034482759" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<text x="274.5" y="432" text-anchor="middle">Q3</text>
<text x="274.5" y="450" text-anchor="middle">Out of 29</text>
<rect height="364.5" x="324" y="49.5" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="27" x="324" y="387" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.5" x="324" y="400.5" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="364.5" y="432" text-anchor="middle">Q4</text>
<text x="364.5" y="450" text-anchor="middle">Out of 30</text>
<rect height="391.5" x="414" y="22.5" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="13.5" x="414" y="400.5" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<text x="454.5" y="432" text-anchor="middle">Q5</text>
<text x="454.5" y="450" text-anchor="middle">Out of 30</text>
<rect height="307.24137931" x="504" y="106.75862069" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="83.7931034483" x="504" y="330.206896552" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.9655172414" x="504" y="400.034482759" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="544.5" y="432" text-anchor="middle">Q6</text>
<text x="544.5" y="450" text-anchor="middle">Out of 29</text>
<rect height="378" x="594" y="36" width="81" style="stroke:#000;stroke-width:1ps;fill:blue;"/>
<rect height="13.5" x="594" y="400.5" width="64.8" style="stroke:#000;stroke-width:1ps;fill:green;"/>
<rect height="13.5" x="594" y="400.5" width="48.6" style="stroke:#000;stroke-width:1ps;fill:yellow;"/>
<text x="634.5" y="432" text-anchor="middle">Q7</text>
<text x="634.5" y="450" text-anchor="middle">Out of 30</text>
<text x="686" y="423" text-anchor="start">-0%</text>
<text x="686" y="342" text-anchor="start">-20%</text>
<text x="686" y="261" text-anchor="start">-40%</text>
<text x="686" y="180" text-anchor="start">-60%</text>
<text x="686" y="99" text-anchor="start">-80%</text>
<text x="686" y="18" text-anchor="start">-100%</text>
<text x="370" y="486" text-anchor="middle">Feedback results for A Fist Full of Fun</text>
<text x="54" y="522" fill="blue" text-anchor="start">Totally Agree=blue</text>
<text x="54" y="540" fill="green" text-anchor="start">Somewhat Agree=green</text>
<text x="54" y="558" fill="yellow" text-anchor="start">Neutral=yellow</text>
<text x="54" y="576" fill="orange" text-anchor="start">Somewhat Disagree=orange</text>
<text x="54" y="594" fill="red" text-anchor="start">Totally Disagree=red</text>
<text x="54" y="630" text-anchor="start">Q 1: This class/panel matched the Web or Program Book description</text>
<text x="54" y="648" text-anchor="start">Q 2: I had fun AND learned in this class/panel</text>
<text x="54" y="666" text-anchor="start">Q 3: I'd recommend the class/panel to a friend</text>
<text x="54" y="684" text-anchor="start">Q 4: This class/panel has inspired me to try something new</text>
<text x="54" y="702" text-anchor="start">Q 5: The presenter(s) really knew their stuff</text>
<text x="54" y="720" text-anchor="start">Q 6: My interests and curiosities were represented in this year's programming</text>
<text x="54" y="738" text-anchor="start">Q 7: Bring this presenter back next year</text>
</g>
</svg>
EOD;

$workstring="<DL>\n";
for ($i=1; $i<=$elements; $i++) {
  $workstring.=sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
  if ($element_array[$i]['Subtitle'] !='') {
    $workstring.=sprintf(": %s",$element_array[$i]['Subtitle']);
  }
  if ($element_array[$i]['Participants']) {
    $workstring.=sprintf(" by <B>%s</B> ",$element_array[$i]['Participants']);
  }
  if ($element_array[$i]['Track']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Track']);
  }
  if ($element_array[$i]['Start Time']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Start Time']);
  }
  if ($element_array[$i]['Duration']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Duration']);
  }
  if ($element_array[$i]['Roomname']) {
    $workstring.=sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
  }
  if ((strtotime($ConStartDatim)+(60*60*24*$ConNumDays)) > time()) {
    $workstring.=sprintf("&mdash; %s",$element_array[$i]['iCal']);
  }
  if ($element_array[$i]['Attended']) {
    $workstring.=sprintf("&mdash; About %s Attended",$element_array[$i]['Attended']);
  }
  if ($element_array[$i]['Web Description']) {
    $workstring.=sprintf("  </DT>\n  <DD><P>Web: %s</P>\n",$element_array[$i]['Web Description']);
  }
  if ($element_array[$i]['Book Description']) {
    $workstring.=sprintf("  </DD>\n  <DD><P>Book: %s</P>\n",$element_array[$i]['Book Description']);
  }
  if ($feedback_array[$element_array[$i]['Sessionid']]) {
    $workstring.="  </DD>\n    <DD>Written feedback from surveys:\n<br>\n";
    $workstring.=sprintf("%s<br>\n",$feedback_array[$element_array[$i]['Sessionid']]);
  }
  // Gather up the info before the graph
  $printstring.=$workstring;
  $pdf->writeHTML($workstring, true, false, true, false, "");
  $workstring="";
  $feedback_file=sprintf("../Local/Feedback/%s.jpg",$element_array[$i]["Sessionid"]);
  if (file_exists($feedback_file)) {
    $printstring.="  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
    $printstring.=sprintf ("<img src=\"%s\">\n<br>\n",$feedback_file);
  }
  if (isset($feedback_array['graph'][$element_array[$i]['Sessionid']])) {
    $workstring="  </DD>\n  <DD>Feedback graph from surveys:\n<br>\n";
    $printstring.=$workstring;
    $pdf->writeHTML($workstring, true, false, true, false, "");
    $workstring="";
    $graphstring=generateSvgString($element_array[$i]['Sessionid']);
    $printstring.=$graphstring;
    $pdf->ImageSVG("@".$graphstring,'','','','','','N','',1,true);
  }
  $workstring.="</DD></P>\n";
}
$workstring.="</DL>\n";
$printstring.=$workstring;
$pdf->writeHTML($workstring, true, false, true, false, "");

if ($print_p =="") {
  topofpagereport($title,$description,$additionalinfo);
  echo "$printstring\n</hr>\n";
  correct_footer();
  } elseif ($print_p =="Template") {
  echo $printstring;
  } else {
  $pdf->Output('Feedback-'.$conid.'.pdf', 'I');
 }
