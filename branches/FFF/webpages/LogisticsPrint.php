<?php
require_once('StaffCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$conid=$_SESSION['conid'];
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$Grid_Spacer=GRID_SPACER; // make it a variable so it can be substituted
$logo=CON_LOGO; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="LogisticsPrint.php";
$title="Logistics";
$description="<P>Logistics information for each of the rooms.</P>\n";
$additionalinfo="<P>List <A HREF=\"LogisticsPrint.php?order=room\">by room </A>\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=room&print_p=y\">print</A>)\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=room&csv=y\">CSV</A>)\n";
$additionalinfo.="or <A HREF=\"LogisticsPrint.php?order=time\">by time</A>\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=time&print_p=y\">print</A>)\n";
$additionalinfo.="(<A HREF=\"LogisticsPrint.php?order=time&csv=y\">CSV</A>).</P>\n";

/* This query returns the room names, start time, sessionid, title,
 services, features, and any other tech notes for printing */
$query = <<<EOD
SELECT
    concat('<A HREF="MaintainRoomSched.php?selroom=',R.roomid,'">',R.roomname,'</A>') AS Room,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a&nbsp;%l:%i&nbsp;%p') as 'Start Time', 
    concat('<A HREF=StaffAssignParticipants.php?selsess=',SCH.sessionid,'>',SCH.sessionid,'</A>') AS Sessionid,
    concat('<a href=EditSession.php?id=',SCH.sessionid,'>',S.title,'</a>') Title,
    Z.roomsetname as 'Room Set',
    if((X.servicelist!=''),X.servicelist,'') as 'Services',
    if((Y.featurelist!=''),Y.featurelist,'') as 'Features',
    if((servicenotes!=''),servicenotes,'') as 'Hotel and Tech Notes'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN  (SELECT
           S.sessionid, 
           GROUP_CONCAT(DISTINCT servicename SEPARATOR ', ') as 'servicelist' 
        FROM
            Sessions S, 
            SessionHasService SS, 
            $ReportDB.Services SE
        WHERE
          S.sessionid=SS.sessionid and
          SE.serviceid=SS.serviceid and
	  SE.conid=$conid
        GROUP BY
           S.sessionid) X USING (sessionid)
    LEFT JOIN (SELECT
           S.sessionid, 
           GROUP_CONCAT(DISTINCT featurename SEPARATOR ', ') as 'featurelist'
        FROM
            Sessions S, 
            SessionHasFeature SF, 
            Features F
        WHERE
          S.sessionid=SF.sessionid and
          F.featureid=SF.featureid
        GROUP BY
          S.sessionid) Y USING (sessionid)
    LEFT JOIN (SELECT
	  S.sessionid,
	  RS.roomsetname
        FROM
	    Sessions S,
	    $ReportDB.RoomSets RS
        WHERE
	  S.roomsetid=RS.roomsetid) Z USING (sessionid)
  WHERE
    R.display_order < 10
  ORDER BY

EOD;

if ($_GET["order"]=="time") {
  $query.="    starttime,\n    roomname";
 } else {
  $query.="    roomname,\n    starttime";
 }

// Retrieve query
list($rows,$header_array,$roomset_array)=queryreport($query,$link,$title,$description,0);

$checkfield="";
$newtableline=0;
for ($i=1; $i<=$rows; $i++) {
  if ($_GET["order"]=="time") {
    if ($roomset_array[$i]['Start Time'] != $checkfield) {
      $checkfield=$roomset_array[$i]['Start Time'];
      $breakon[$newtableline++]=$i;
    }
  } else {
    if ($roomset_array[$i]['Room'] != $checkfield) {
      $checkfield=$roomset_array[$i]['Room'];
      $breakon[$newtableline++]=$i;
    }
  }
 }
$breakon[$newtableline]=$i;
    

// Page Rendering
/* Check for the csv variable, to see if we should be dropping a table,
 instead of displaying one.  If so, feed a continuous table, otherwise
 split up the tables on "skip" spaces, to make them flow more naturally.
 Include the $additionalinfo regularly, so one doesn't have to scroll
 all the way back to the top, and it gives a nice visual break. */
if ($_GET["csv"]=="y") {
  topofpagecsv("Logistics_grid.csv");
  echo rendercsvreport(1,$rows,$header_array,$roomset_array);
 } elseif ($_GET["print_p"]=="y") {
  require_once('../../tcpdf/config/lang/eng.php');
  require_once('../../tcpdf/tcpdf.php');
  $pdf = new TCPDF('l', 'mm', 'letter', true, 'UTF-8', false);
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
  $pdf->SetFont('helvetica', '', 12, '', true);
  for ($i=0; $i<$newtableline; $i++) {
    $htmlstring=renderhtmlreport($breakon[$i],$breakon[$i+1]-1,$header_array,$roomset_array);
    $pdf->AddPage();
    $pdf->writeHTML($htmlstring, true, false, true, false, '');
  }
  $pdf->Output(CON_NAME.'-grid.pdf', 'I');
 } else {
  topofpagereport($title,$description,$additionalinfo);
  for ($i=0; $i<$newtableline; $i++) {
    echo renderhtmlreport($breakon[$i],$breakon[$i+1]-1,$header_array,$roomset_array);
  }
  correct_footer();
 }
