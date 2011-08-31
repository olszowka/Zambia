<?php
require_once('CommonCode.php');
if (may_I("Staff")) {
  require_once('StaffCommonCode.php');
 } else {
  require_once('PartCommonCode.php');
 }
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$GohBadgeList=GOH_BADGE_LIST;

// LOCALIZATIONS
$_SESSION['return_to_page']="manualGRIDS.php";

/* unpub controls the "Do Not Print" and "Staff Only" inclusion into
 the grid it needs to be set first, because otherwise we are checking
 on a negative.  Default exclude "Do Not Print" and "Staff Only"
 filled is the switch between semi-filled (color only) and filled
 (name in each block).  Default semi-filled nocolor allows for the
 lack of background color of the cells. Default color beginonly gives
 the short version of the grid, not broken by time demarcation,
 showing start times only, with no day-breaks progselect limits to the
 programming items/rooms only volselect limits to the volunteer
 items/rooms only regselect limits to the registration items/rooms
 only saleselect limits to the sales items/rooms only vendselect
 limits to the vendor items/rooms only watchselect limits to the watch
 items/rooms only logselect limits to the logistic items/rooms only
 eventselect limits to the events items/rooms only fasttrack limits to
 the Fast Track items/rooms only (the above several should exclude each
 other) goh limits to the goh involved programs only
*/
$unpub="n";
$unpub=$_GET['unpublished'];
$staffonly=$_GET['staffonly'];
$filled=$_GET['timefilled'];
$nocolor=$_GET['nocolor'];
$beginonly=$_GET['starttime'];
$progselect=$_GET['programming'];
$volselect=$_GET['volunteer'];
$regselect=$_GET['registration'];
$saleselect=$_GET['sales'];
$vendselect=$_GET['vending'];
$watchselect=$_GET['watch'];
$logselect=$_GET['logistics'];
$eventselect=$_GET['events'];
$fasttrackselect=$_GET['fasttrack'];
$goh=$_GET['goh'];

// If Participant, fix several of the variables, so there is only one grid displayed.
if ($_SESSION['role']=="Participant") {
  $unpub="n";
  $staffonly="n";
  $progselect="n";
  $volselect="n";
  $regselect="n";
  $saleselect="n";
  $vendselect="n";
  $watchselect="n";
  $logelect="n";
  $eventselect="n";
  $fastrtactselect=="n";
  $filled="n";
  $beginonly="n";
  $goh="n";
  $nocolor="n";
 }

// Title/header hacking so everything is switched, and easily readable.
// Defaults
$allprint="excludes";
$tallprint="";
$typeprint="Complete ";
$beginonlyprint="regular ";
$semifill=" (only)";
$tsemifill="Time Semi-filled ";
$gohprint="";
$tcolorprint="Color ";
$colorprint=", keyed by color";

// Mods
if ($unpub=="y") {
  $allprint="includes";
  $tallprint="Unabridged ";
 }
if ($staffonly=="y") {
  $unpub="y";
  $allprint="is only";
  $tallprint="Staff Only ";
 }
if ($progselect=="y") {
  $typeprint="Programming ";
 }
if ($volselect=="y") {
  $typeprint="Volunteer ";
 }
if ($regselect=="y") {
  $typeprint="Registration ";
 }
if ($saleselect=="y") {
  $typeprint="Sales ";
 }
if ($vendselect=="y") {
  $typeprint="Vending ";
 }
if ($watchselect=="y") {
  $typeprint="Watch ";
 }
if ($logselect=="y") {
  $typeprint="Logistics/Tech ";
 }
if ($eventselect=="y") {
  $typeprint="Events ";
 }
if ($fasttrackselect=="y") {
  $typeprint="Fast Track ";
 }
if ($filled=="y") {
  $semifill="";
  $tsemifill="Time Filled ";
 }
if ($beginonly=="y") {
  $filled="y";
  $beginonlyprint="";
  $semifill="";
  $tsemifill="";
 }
if ($goh=="y") {
  $gohprint="GoH ";
 }
if ($nocolor=="y") {
  $tcolorprint="";
  $colorprint="";
 }

// Back to the more standard piece.
$title=$tallprint.$gohprint.$typeprint.$tsemifill.$tcolorprint."Grid";
$description="<P>Display ".$gohprint.$typeprint."schedule with rooms on horizontal axis and ".$beginonlyprint."time on vertical".$colorprint.$semifill.". This $allprint items marked \"Do Not Print\" or \"Staff Only\".</P>\n";
$additionalinfo="<P>Click on the room name to edit the room's schedule;\n";
$additionalinfo.="the session id to edit the session's participants; or\n";
$additionalinfo.="the title to edit the session.</P>\n";
$additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php\">description</A>,\n";
$additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php\">bio</A>, the time to visit that section of";
$additionalinfo.=" the <A HREF=\"StaffSchedule.php\">schedule</A>, or the track name to see all the classes\n";
$additionalinfo.="by <A HREF=\"StaffTracks.php\">track</A>.  <A HREF=\"manualGRIDS.php\">Pick</A> another grid.</P>\n";
$Grid_Spacer=GRID_SPACER;

/* This query returns the room names for an array.  We might want to
 add the "unpub" and "staffonly" hacks here, to cull the unused rooms.
 There should be a better way to cull them, though.

 R.roomid in (SELECT DISTINCT SCH.roomid FROM Schedule SCH JOIN
 Sessions S USING (sessionid) where pubstatusid=2) and, unless I do
 this in a phenominally better way, we can probably drop the "R." from
 this query.
*/
$query ="SELECT R.roomname, R.roomid";
$query.=" FROM Rooms R";
$query.=" WHERE";
if ($progselect=="y") {$query.=" R.function like '%program%' AND";}
if ($volselect=="y") {$query.=" R.function like '%volunteer%' AND";}
if ($regselect=="y") {$query.=" R.function like '%registration%' AND";}
if ($saleselect=="y") {$query.=" R.function like '%sale%' AND";}
if ($vendselect=="y") {$query.=" R.function like '%vendor%' AND";}
if ($watchselect=="y") {$query.=" R.function like '%watch%' AND";}
if ($logselect=="y") {$query.=" R.function like '%program%' AND";}
if ($eventselect=="y") {$query.=" R.function like '%event%' AND";}
if ($fasttrackselect=="y") {$query.=" R.function like '%Fast Track%' AND";}
$query.=" R.roomid in (SELECT DISTINCT roomid FROM Schedule JOIN Sessions USING (sessionid)";
if ($goh=="y") {$query.=" JOIN ParticipantOnSession USING (sessionid) WHERE badgeid in $GohBadgeList AND";} else {$query.=" WHERE";}
if ($unpub=="y") {$query.=" pubstatusid > 0";} else {$query.=" pubstatusid=2";}
$query.=") ORDER BY R.display_order";

// Retrieve query
list($rooms,$unneeded_array_a,$header_array)=queryreport($query,$link,$title,$description,0);

// Set up the header cells
// Need to add the iCal link in here once it works
$header_cells="<TR><TH class=\"border2222\">&nbsp;&nbsp;Class&nbsp;&nbsp;Time&nbsp;&nbsp;</TH>";
for ($i=1; $i<=$rooms; $i++) {
  $header_cells.="<TH class=\"border2222\">";
  $header_cells.=sprintf("<A HREF=\"MaintainRoomSched.php?selroom=%s\"><B>%s</B></A>",$header_array[$i]["roomid"],$header_array[$i]["roomname"]);
  $header_cells.="</TH>";
 }
$header_cells.="</TR>";

/* This set of queries finds the appropriate presenters for a class,
 based on sessionid, and produces links for them.
 To get the volunteers use the following instead/in addition to the GROUP_CONCAT line below:
 WHERE POS.volunteer=0 AND POS.introducer=0 AND POS.aidedecamp=0 removed
 GROUP_CONCAT(IF((POS.volunteer=1 OR POS.introducer=1 OR POS.aidedecamp=1),concat(P.pubsname,", "),"") SEPARATOR "") as allpubsnames
*/

$query = <<<EOD
SELECT
      S.sessionid,
      GROUP_CONCAT(IF((POS.volunteer=0 AND POS.introducer=0 AND POS.aidedecamp=0),concat("<A HREF=\"StaffBios.php#",P.pubsname,"\">",P.pubsname,"</A>",if((POS.moderator=1),'(m), ',', ')),"") SEPARATOR "") as presentpubsnames,
      GROUP_CONCAT(IF((POS.volunteer=1),concat(P.pubsname,"(v), "),"") SEPARATOR "") as volpubsnames,
      GROUP_CONCAT(IF((POS.introducer=1),concat(P.pubsname,"(i), "),"") SEPARATOR "") as intpubsnames,
      GROUP_CONCAT(IF((POS.aidedecamp=1),concat(P.pubsname,"(a), "),"") SEPARATOR "") as aidpubsnames
    FROM
      Sessions S
    JOIN
      ParticipantOnSession POS USING (sessionid)
    JOIN
      Participants P USING (badgeid)
    GROUP BY
      sessionid
    ORDER BY
      sessionid;
EOD;

// Retrieve query
list($presenters,$unneeded_array_b,$presenters_tmp_array)=queryreport($query,$link,$title,$description,0);

for ($i=1; $i<=$presenters; $i++) {
  $presenters_array[$presenters_tmp_array[$i]['sessionid']]=$presenters_tmp_array[$i]['presentpubsnames'].$presenters_tmp_array[$i]['volpubsnames'].$presenters_tmp_array[$i]['intpubsnames'].$presenters_tmp_array[$i]['aidpubsnames'];
 } 

/* These queries finds the first and last second that is actually
 scheduled so we don't waste grid-space. */
$query="SELECT TIME_TO_SEC(starttime) as 'beginschedule' FROM Schedule ORDER BY starttime ASC LIMIT 0,1";
list($earliest,$unneeded_array_c,$grid_start_sec_array)=queryreport($query,$link,$title,$description,0);
$grid_start_sec=$grid_start_sec_array[1]['beginschedule'];

$query="SELECT (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) as 'endschedule' FROM Schedule SCH JOIN Sessions S USING (sessionid) ORDER BY endschedule DESC LIMIT 0,1";
list($latest,$unneeded_array_d,$grid_end_sec_array)=queryreport($query,$link,$title,$description,0);
$grid_end_sec=$grid_end_sec_array[1]['endschedule'];

/* This complex set of queries fills in the header_cells and then puts
 the times, associated with each room along the row seperated out by
 the determinants above, by stepping along either in time intervals or
 as a whole, again, chosen above. */

if ($beginonly=="y") {$grid_end_sec=$grid_start_sec;}
$printrowscount=0;
for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
  $printrowscount++;
  $printrows_array[$printrowscount]=$time;
  if ($beginonly=="y") {
    $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'blocktime'";
  } else {
    $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SEC_TO_TIME('$time')),'%a %l:%i %p') as 'blocktime'";
  }
  if ($filled=="y") {
    $filled_cull="roomid=%s";
  } else {
    $filled_cull="(roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime)))";
  }
  for ($i=1; $i<=$rooms; $i++) {
    $header_roomid=$header_array[$i]["roomid"];
    $header_roomname=$header_array[$i]["roomname"];
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,S.title,\"\") SEPARATOR '') as \"%s title\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,S.sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF($filled_cull,S.duration,\"\") SEPARATOR '') as \"%s duration\"",$header_roomid,$header_roomname);
    $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,T.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$header_roomid,$header_roomname);
  }
  $query.=" FROM Schedule SCH JOIN Sessions S USING (sessionid)";
  $query.=" JOIN Rooms R USING (roomid) JOIN Types T USING (typeid)";
  $query.=" WHERE";
  if ($unpub!="y") {$query.=" S.pubstatusid = 2 AND";}
  if ($staffonly=="y") {$query.=" S.pubstatusid != 2 AND";}
  if ($progselect=="y") {$query.=" R.function like '%program%' AND";}
  if ($volselect=="y") {$query.=" R.function like '%volunteer%' AND";}
  if ($regselect=="y") {$query.=" R.function like '%registration%' AND";}
  if ($saleselect=="y") {$query.=" R.function like '%sale%' AND";}
  if ($vendselect=="y") {$query.=" R.function like '%vendor%' AND";}
  if ($watchselect=="y") {$query.=" R.function like '%watch%' AND";}
  if ($logselect=="y") {$query.=" R.function like '%program%' AND";}
  if ($eventselect=="y") {$query.=" R.function like '%event%' AND";}
  if ($fasttrackselect=="y") {$query.=" R.function like '%Fast Track%' AND";}
  if ($goh=="y") {$query.=" S.sessionid in (SELECT DISTINCT sessionid from ParticipantOnSession WHERE badgeid IN $GohBadgeList) AND";}
  if ($beginonly=="y") {
    $query.=" SCH.sessionid = S.sessionid GROUP BY SCH.starttime ORDER BY SCH.starttime";
  } else {
    $query.=" TIME_TO_SEC(SCH.starttime) <= $time";
    $query.=" AND (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) >= ($time + $Grid_Spacer);";
  }
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

  if ($beginonly=="y") {
    for ($i=1; $i<=$rows; $i++) {
      $printrows_array[$i]=$i;
      $grid_array[$i]=mysql_fetch_array($result,MYSQL_BOTH);
      $k=$grid_array[$i]['blocktime'];
      $grid_array[$i]['blocktime']=sprintf("<A HREF=\"StaffSchedule.php#%s\"><B>%s</B></A>",$k,$k);
    }
  } else {
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
    if ($skiprow == 0) {$grid_array[$time]['blocktime'] = "Skip";}
    if ($refskiprow != 0) {
      $k=$grid_array[$time]['blocktime'];
      $grid_array[$time]['blocktime']=sprintf("<A HREF=\"StaffSchedule.php#%s\"><B>%s</B></A>",$k,$k);
    }
  }
 }

/* Printing body.  Uses the page-init from above adds informational
 line then creates the grid.  skipinit kills the rogue extrat /TABLE
 and skipaccum allows for only one new tabel per set of skips.  The
 extra ifs keep the parens out of the otherwise empty blocks.  We
 switch on htmlcellcolor, because, by design, that is the only thing
 written in a continuation block. */
topofpagereport($title,$description,$additionalinfo);
$skipinit=0;
$skipaccum=1;
foreach ($printrows_array as $i) {
  if ($skipaccum == 1) { 
    if ($skipinit != 0) {echo "</TABLE>\n";} else {$skipinit++;}
    echo "<TABLE class=\"border1111\">";
    //            echo "<TABLE BORDER=1>";
    echo $header_cells;
  }
  if ($grid_array[$i]['blocktime'] == "Skip") {
    $skipaccum++;
  } else {
    echo "<TR><TH class=\"border1111\">";
    echo $grid_array[$i]['blocktime'];
    echo "</TH>";
    for ($j=1; $j<=$rooms; $j++) {
      $header_roomname=$header_array[$j]['roomname'];
      $bgcolor=$grid_array[$i]["$header_roomname htmlcellcolor"]; //cell background color
      $cellclass=$grid_array[$i]["$header_roomname cellclass"]; //cell edge state
      if ($cellclass == "") {$cellclass="border1111";}
      $sessionid=$grid_array[$i]["$header_roomname sessionid"]; //sessionid
      $title=$grid_array[$i]["$header_roomname title"]; //title
      $duration = substr($grid_array[$i]["$header_roomname duration"],0,-3); // duration; drop ":00" representing seconds off the end
      $presenters = substr($presenters_array[$sessionid],0,-2); //presenters, with the final ", " cut off.
      if (substr($duration,0,1)=="0") {$duration = substr($duration,1,999);} // drop leading "0"
      if ($bgcolor!="") {
	if ($nocolor=="y") {
	  echo sprintf("<TD CLASS=\"%s\">",$cellclass);
	} else {
	  echo sprintf("<TD BGCOLOR=\"%s\" CLASS=\"%s\">",$bgcolor,$cellclass);
	}
	if (($sessionid!="") AND ($_SESSION['role']!='Participant')) {
	  echo sprintf("(<A HREF=\"StaffAssignParticipants.php?selsess=%s\">%s</A>) ",$sessionid,$sessionid);
	}
	if ($title!="") {
	  if ($_SESSION['role']=="Participant") {
	    echo sprintf("<A HREF=\"StaffDescriptions.php#%s\">%s</A>",$sessionid,$title);
	  } else {
	    echo sprintf("<A HREF=\"EditSession.php?id=%s\">%s</A>",$sessionid,$title);
	  }
	}
	if ($duration!="") {
	  echo sprintf(" (%s)",$duration);
	}
	if ($presenters!="") {
	  echo sprintf("<br>\n%s",$presenters);
	}
      }
      else
	{ echo "<TD class=\"border1111\">&nbsp;"; } 
      echo "</TD>";
    }
    echo "</TR>\n";
    $skipaccum=0;
  }
}
echo "</TABLE>";
if ($_SESSION['role']=="Participant") {
  participant_footer();
 } else {
  staff_footer();
 }

?>
