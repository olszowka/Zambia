<?php
require_once('PostingCommonCode.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$Grid_Spacer=GRID_SPACER; // make it a variable so it can be substituted
$logo=CON_LOGO; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// Deal with what is passed in.
if (!empty($_SERVER['QUERY_STRING'])) {
  $passon="?".$_SERVER['QUERY_STRING'];
  $passon_p=$passon."&print_p=y";
} else {
  $passon_p="?print_p=y";
}

if (isset($_GET['volunteer'])) {
  $pubstatus_check="'Volunteer'";
  $Grid_Spacer=3600;
} elseif (isset($_GET['registration'])) {
  $pubstatus_check="'Reg Staff'";
  $Grid_Spacer=3600;
} elseif (isset($_GET['sales'])) {
  $pubstatus_check="'Sales Staff'";
  $Grid_Spacer=3600;
} elseif (isset($_GET['vfull'])) {
  $pubstatus_check="'Volunteer','Reg Staff','Sales Staff'";
  $Grid_Spacer=3600;
} else {
  $pubstatus_check="'Public'";
}

// LOCALIZATIONS
$_SESSION['return_to_page']="Postgrid.php";
$title="Sessions Grid";
$description="<P>Grid of all sessions.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php$passon\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"Bios.php$passon\">bio</A>, the time to visit that section of\n";
$additionalinfo.="the <A HREF=\"Schedule.php$passon\">schedule</A>, or the track name to see all the classes\n";
$additionalinfo.="by <A HREF=\"Tracks.php$passon\">track</A>.  (<A HREF=\"Postgrid-wide.php$passon\">Switch indices</A>)</P>\n";
$additionalinfo.="<P>If you wish to have a copy printed, please download the <A HREF=Postgrid.php$passon_p>Rooms\n";
$additionalinfo.="x Times</A> or <A HREF=Postgrid-wide.php$passon_p>Times x Rooms</A> version.</P>\n";

/* This query returns the room names for an array, to be used as
 headers, and keys for other arrays.*/
$query = <<<EOD
SELECT
        roomname,
        roomid
    FROM
            Rooms
    WHERE
        roomid in
        (SELECT DISTINCT roomid FROM Schedule JOIN Sessions USING (sessionid) JOIN $ReportDB.PubStatuses USING (pubstatusid) WHERE pubstatusname in ($pubstatus_check))
    ORDER BY
    	  display_order;
EOD;

// Retrieve query
list($rooms,$unneeded_array_a,$header_array)=queryreport($query,$link,$title,$description,0);

/* This set of queries finds the appropriate presenters for a session element,
 based on sessionid, and produces links for them. */
$query = <<<EOD
SELECT
      sessionid,
      GROUP_CONCAT(concat("<A HREF=\"Bios.php$passon#",pubsname,"\">",pubsname,"</A>",if((moderator=1),'(m)','')) SEPARATOR ", ") as allpubsnames
    FROM
      Sessions
    JOIN ParticipantOnSession USING (sessionid)
    JOIN $ReportDB.Participants USING (badgeid)
    WHERE 
      volunteer=0 AND
      introducer=0 AND
      aidedecamp=0
    GROUP BY
      sessionid
    ORDER BY
      sessionid;
EOD;

// Retrieve query
list($presenters,$unneeded_array_b,$presenters_tmp_array)=queryreport($query,$link,$title,$description,0);
for ($i=1; $i<=$presenters; $i++) {
  $presenters_array[$presenters_tmp_array[$i]['sessionid']]=$presenters_tmp_array[$i]['allpubsnames'];
 } 

/* The below was a lovely idea, but the time differential was
   minimal, and not all the time was the whole con being
   represented so the below is simply commented out. */
/* This query finds the first second that is actually scheduled
 so we don't waste grid-space and time looping through nothing.
$query="SELECT TIME_TO_SEC(starttime) as 'beginschedule' FROM Schedule ORDER BY starttime ASC LIMIT 0,1";
if (($result=mysql_query($query,$link))===false) {
  $message="Error retrieving data from database.<BR>";
  $message.=$query;
  $message.="<BR>";
  $message.= mysql_error();
  RenderError($title,$message);
  exit ();
 }
if (0==($earliest=mysql_num_rows($result))) {
  $message="<P>This report retrieved no results matching the criteria.</P>\n";
  RenderError($title,$message);
  exit();
 }
$grid_start_sec=mysql_result($result,0);

/* This query finds the last second that is actually scheduled
 so we don't waste grid-space and time looping through nothing. 
$query="SELECT (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) as 'endschedule' FROM Schedule SCH JOIN Sessions S USING (sessionid) ORDER BY endschedule DESC LIMIT 0,1";
if (($result=mysql_query($query,$link))===false) {
  $message="Error retrieving data from database.<BR>";
  $message.=$query;
  $message.="<BR>";
  $message.= mysql_error();
  RenderError($title,$message);
  exit ();
 }
if (0==($latest=mysql_num_rows($result))) {
  $message="<P>This report retrieved no results matching the criteria.</P>\n";
  RenderError($title,$message);
  exit();
 }
$grid_end_sec=mysql_result($result,0);

*/
$grid_start_sec=0;
$grid_end_sec=CON_NUM_DAYS*86400;
/* This complex query set is generated by stepping along by the time interval,
 and, in each interval, setting up the title, sessionid, duration, and background
 color of each class/grid element. */
/* Probably should use queryreport to standardize gets.*/
$header_time=array("Room Name");
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
  $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SEC_TO_TIME('$time')),'%a&nbsp;%l:%i&nbsp;%p') as 'blocktime'";
  for ($i=1; $i<=$rooms; $i++) {
    $x=$header_array[$i]["roomid"];
    $y=$header_array[$i]["roomname"];
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.title,\"\") SEPARATOR '') as \"%s title\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.duration,\"\") SEPARATOR '') as \"%s duration\"",$x,$y);
    $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,T.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$x,$y);
  }
  $query.=" FROM Schedule SCH JOIN Sessions S USING (sessionid)";
  $query.=" JOIN Rooms R USING (roomid) JOIN $ReportDB.Types T USING (typeid) JOIN $ReportDB.PubStatuses PS USING (pubstatusid)";
  $query.=" WHERE PS.pubstatusname in ($pubstatus_check) AND TIME_TO_SEC(SCH.starttime) <= $time";
  $query.=" AND (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) >= ($time + $Grid_Spacer);";
  if (($result=mysql_query($query,$link))===false) {
    $message="Error retrieving data from database.<BR>";
    $message.=$query;
    $message.="<BR>";
    $message.= mysql_error();
    RenderError($title,$message);
    exit ();
  }
  if (0==($rows=mysql_num_rows($result))) {
    $message="<P>This report retrieved no results matching the criteria.</P>\n";
    RenderError($title,$message);
    exit();
  }

  /* It seems to me that the below to elements can and should be combined,
   somehow.  Otherwise we are walking the same set of loops more times than
   we need. */
  /* Still in the time-stepped loop, we create the elements of the array to
   be called forth, below, in terms of colour, border, and skipvalue. */
  $grid_array[$time]=mysql_fetch_array($result,MYSQL_BOTH);
  $skiprow=0;
  $refskiprow=0;
  for ($i=1; $i<=$rooms; $i++) {
    $j=$header_array[$i]['roomname'];
    if ($grid_array[$time]["$j htmlcellcolor"]!="") {
      $skiprow++;
      if ($grid_array[$time]["$j sessionid"]!="") {
	$grid_array[$time]["$j cellclass"]="border1101d";
	$refskiprow++;
      } else {
	$grid_array[$time]["$j cellclass"]="border0101d";
      }
    } else {
      $grid_array[$time]["$j cellclass"]="border1111";
    }
  }
  if ($skiprow == 0) {
    $grid_array[$time]['blocktime'] = "Skip";
  } else {
    if ($refskiprow != 0) {
      $k=$grid_array[$time]['blocktime'];
      $fk=str_replace("&nbsp;"," ",$k);
      $grid_array[$time]['blocktime']=sprintf("<A HREF=\"Schedule.php%s#%s\">%s</A>",$passon,$fk,$k);
    }
    array_push($header_time,$grid_array[$time]['blocktime']);
  }
 }

/* Assembling the body by creating the element_array, of all the
 information in each row, distinguished by $element_row. $breakon allows
 for one tabel per set of skips.  The extra ifs keep the parens out of
 the otherwise empty blocks.  We switch on htmlcellcolor, because, by
 design, that is the only thing written in a continuation block. */
/* This should also make generating the iCal that much easier, when
 that code is added */
$element_row=1;
$newtableline=1;
$breakon[$newtableline]=1;
for ($i=1; $i<=$rooms; $i++) { $header_rooms[$i]=$header_array[$i]['roomname']; }
array_unshift($header_rooms,"Class Time");
for ($i = $grid_start_sec; $i < $grid_end_sec; $i = ($i + $Grid_Spacer)) {
  if ($grid_array[$i]['blocktime'] == "Skip") {
    if ($breakon[$newtableline] != $element_row) { $breakon[++$newtableline] = $element_row; }
  } else {
    $element_array[$element_row]["Class Time"] = sprintf("<TD class=\"border1111\">%s</TD>\n",$grid_array[$i]['blocktime']);
    for ($j=1; $j<=$rooms; $j++) {
      $header_roomname=$header_array[$j]['roomname'];
      $element_col=$header_roomname;
      $bgcolor=$grid_array[$i]["$header_roomname htmlcellcolor"]; //cell background color
      $cellclass=$grid_array[$i]["$header_roomname cellclass"]; //cell edge state
      if ($cellclass == "") {$cellclass="border1111";}
      $sessionid=$grid_array[$i]["$header_roomname sessionid"]; //sessionid
      $title=$grid_array[$i]["$header_roomname title"]; //title
      $duration=substr($grid_array[$i]["$header_roomname duration"],0,-3); // duration; drop ":00" representing seconds off the end
      if (substr($duration,0,1)=="0") {$duration = substr($duration,1,999);} // drop leading "0"
      $presenters=$presenters_array[$sessionid]; //presenters
      if ($bgcolor!="") {
	$element_array[$element_row][$element_col] = sprintf("<TD BGCOLOR=\"%s\" CLASS=\"%s\">",$bgcolor,$cellclass);
	if ($title!="") {
	  $element_array[$element_row][$element_col].= sprintf("<A HREF=\"Descriptions.php%s#%s\">%s</A>",$passon,$sessionid,$title);
	}
	if ($duration!="") {
	  $element_array[$element_row][$element_col].= sprintf(" (%s)",$duration);
	}
	if ($presenters!="") {
	  $element_array[$element_row][$element_col].= sprintf("<br>\n%s",$presenters);
	}
      } else { $element_array[$element_row][$element_col].= "<TD class=\"border1111\">&nbsp;"; } 
      $element_array[$element_row][$element_col].= "</TD>\n";
    }
    $element_row++;
  }
 }
$breakon[++$newtableline] = $element_row;

// Page Rendering
/* Check for the csv variable, to see if we should be dropping a table,
 instead of displaying one.  If so, feed a continuous table, otherwise
 split up the tables on "skip" spaces, to make them flow more naturally.
 Include the $additionalinfo regularly, so one doesn't have to scroll
 all the way back to the top, and it gives a nice visual break. */
if ($_GET["csv"]=="y") {
  topofpagecsv("grid.csv");
  echo rendercsvreport(1,$element_row,$header_rooms,$element_array);
 } elseif ($_GET["print_p"]=="y") {
  require_once('../../tcpdf/config/lang/eng.php');
  require_once('../../tcpdf/tcpdf.php');
  $pdf = new TCPDF('p', 'mm', 'letter', true, 'UTF-8', false);
  $pdf->SetCreator('Zambia');
  $pdf->SetAuthor('Programming Team');
  $pdf->SetTitle('Grid');
  $pdf->SetSubject('Programming Grid');
  $pdf->SetKeywords('Zambia, Presenters, Volunteers, Programming, Grid');
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
  $pdf->SetFont('helvetica', '', 6, '', true);
  for ($i=1; $i<$newtableline-1; $i++) {
    $gridstring=rendergridreport($breakon[$i],$breakon[$i+1]-1,$header_rooms,$element_array);
    $pdf->AddPage();
    $pdf->writeHTML($gridstring, true, false, true, false, '');
  }
  $pdf->Output(CON_NAME.'-grid.pdf', 'I');
 } else {
  topofpagereport($title,$description,$additionalinfo);
  for ($i=1; $i<$newtableline-1; $i++) {
    echo rendergridreport($breakon[$i],$breakon[$i+1]-1,$header_rooms,$element_array);
    echo $additionalinfo;
  }
  posting_footer();
 }
