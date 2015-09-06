<?php
define ("newroomslots",5); // number of rows at bottom of page for new schedule entries
$title="Maintain Room Schedule";
require_once('db_functions.php');
require_once('data_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');
global $daymap;

staff_header($title);
$topsectiononly=true; // no room selected -- flag indicates to display only the top section of the page

if (isset($_POST["numrows"])) {
	$ignore_conflicts=(isset($_POST['override']))?true:false;
	if(!SubmitMaintainRoom($ignore_conflicts)) $conflict=true;
    }

if (isset($_POST["selroom"]) && $_POST["selroom"]!="0") { // room was selected by this form
        $selroomid=$_POST["selroom"];
        $topsectiononly=false;
        //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }
    elseif (isset($_GET["selroom"])) { // room was select by external page such as a report
        $selroomid=$_GET["selroom"];
        $topsectiononly=false;
        }
    else {
        $selroomid=0; // room was not yet selected.
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }

if ($conflict!=true) {
		$queryArray["rooms"]="SELECT roomid, roomname, function, is_scheduled FROM Rooms ORDER BY display_order";
		if (($resultXML=mysql_query_XML($queryArray))===false) {
		    RenderErrorAjax($message_error); //header has already been sent, so can just send error message and stop.
	        exit();
	        }
		//echo($resultXML->saveXML()); //for debugging only
		$xsl = new DomDocument;
		$xsl->load('xsl/MaintainRoomSched_roomSelect.xsl');
		$xslt = new XsltProcessor();
		$xslt->importStylesheet($xsl);
		$html = $xslt->transformToXML($resultXML);
?>
<form class="form-inline" name="selroomform" method="POST" action="MaintainRoomSched.php">
	<div>
	 <label for="selroom">Select Room:</label>
<?php echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i")); ?>
  	<button type="submit" name="submit" class="btn btn-primary">Fetch Room</button></div>
<?php
		if (isset($_SESSION['return_to_page'])) {
		    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report</A>";
		    }
?>
	<div class="">
  	<input type="checkbox" class="checkbox adjust" id="showUnschedRmsCHK" name="showUnschedRmsCHK" value="1" <?php if (isset($_POST["showUnschedRmsCHK"])) echo "checked=\"checked\""?> />
  	<label class="checkbox inline" for="showUnschedRmsCHK">Include unscheduled rooms</label>
	</div>
	<div class="padded text-info">For any session where you are rescheduling, please read the Notes for Programming Committee.</div>
	</form>
	<hr>
<?php
		// unset all stuff from posts so input fields get reset to blank
		for ($i=1;$i<=newroomslots;$i++) {
			unset($_POST["day$i"]);
			unset($_POST["hour$i"]);
			unset($_POST["min$i"]);
			unset($_POST["ampm$i"]);
			unset($_POST["sess$i"]);
			}
		}
	else {
		//
		}
if ($topsectiononly) {
    staff_footer();
    exit();
    }
?>
<form name="rmschdform" method="POST" action="MaintainRoomSched.php">
<input type="hidden" name="showUnschedRmsCHK" value="1" <?php if (isset($_POST["showUnschedRmsCHK"])) echo "checked=\"checked\""?> />
<?php
if ($conflict==true) {
	echo "<button type=\"submit\" name=\"override\" class=\"btn btn-danger\">Save Anyway!</button>\n";
	echo "<br><hr>\n";
	}
$query = <<<EOD
SELECT roomid, roomname, opentime1, closetime1, opentime2, closetime2, opentime3, closetime3,
function, floor, height, dimensions, area, notes FROM Rooms WHERE roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"alert alert-error\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<h2>$selroomid - ".htmlspecialchars(mysql_result($result,0,"roomname"))."</h2>";
//echo "|".mysql_result($result,0,"opentime1")."|<BR>\n";
echo "<h4 class=\"label\">Open Times</h4>\n";
echo "<div class=\"border1111 lrpad lrmargin\"><P class=\"lrmargin\">";
if (mysql_result($result,0,"opentime1")!="") {
    echo time_description(mysql_result($result,0,"opentime1"))." through ".time_description(mysql_result($result,0,"closetime1"))."<BR>\n";
    }
if (mysql_result($result,0,"opentime2")!="") {
    echo time_description(mysql_result($result,0,"opentime2"))." through ".time_description(mysql_result($result,0,"closetime2"))."<BR>\n";
    }
if (mysql_result($result,0,"opentime3")!="") {
    echo time_description(mysql_result($result,0,"opentime3"))." through ".time_description(mysql_result($result,0,"closetime3"))."<BR>\n";
    }
echo "</div>\n";
echo "<H4 class=\"label\">Characteristics</H4>\n";
echo "   <TABLE class=\"table table-condensed compressed\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Function</TH>\n";
echo "         <TH class=\"lrpad border1111\">Floor</TH>\n";
echo "         <TH class=\"lrpad border1111\">Dimensions</TH>\n";
echo "         <TH class=\"lrpad border1111\">Area</TH>\n";
echo "         <TH class=\"lrpad border1111\">Height</TH>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"function"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"floor"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"dimensions"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"area"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"height"))."</TD>\n";
echo "         </TR>\n";
if (mysql_result($result,0,"notes")!="") {
echo "        <TR>\n";
echo "          <TD colspan=5 class=\"alert alert-info\">".htmlspecialchars(mysql_result($result,0,"notes"))."</TD>\n";
echo "        </TR>\n";
}
echo "      </TABLE>\n";
echo "<H4 class=\"label\">Room Sets</H4>\n";
$query = <<<EOD
SELECT RS.roomsetname, RHS.capacity FROM RoomSets RS, RoomHasSet RHS WHERE
RS.roomsetid=RHS.roomsetid AND RHS.roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"alert alert-error\">".$message."\n";
    staff_footer();
    exit();
    }
$i=1;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i++;
    }
$numrows=$i;
echo "   <TABLE class=\"table table-condensed compressed\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Room Set</TH>\n";
echo "         <TH class=\"lrpad border1111\">Capacity</TH>\n";
echo "         </TR>\n";
for ($i=1;$i<=$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"vatop lrpad border1111\">".$bigarray[$i]["roomsetname"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border1111\">".$bigarray[$i]["capacity"]."</TD>\n";
    echo "      </TR>\n";
    }
echo "      </TABLE>\n";
$query = <<<EOD
SELECT SC.scheduleid, SC.starttime, S.duration, SC.sessionid, T.trackname, S.title, ST.roomsetname 
FROM Schedule SC, Sessions S, Tracks T, RoomSets ST WHERE
SC.sessionid = S.sessionid AND S.trackid = T.trackid AND S.roomsetid=ST.roomsetid
AND SC.roomid=$selroomid ORDER BY SC.starttime
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$i=1;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i++;
    }
$numrows=--$i;

echo "<HR>\n";
echo "<H4 class=\"label\">Current Room Schedule</H4>\n";
echo "<TABLE class=\"table table-condensed compressed\">\n";
echo "   <TR>\n";
echo "      <TH>Delete</TH>\n";
echo "      <TH>Start Time</TH>\n";
echo "      <TH>Duration</TH>\n";
echo "      <TH>Track</TH>\n";
echo "      <TH>Session ID</TH>\n";
echo "      <TH>Title</TH>\n";
echo "      <TH>Room Set</TH>\n";
echo "      </TR>\n";
for ($i=1;$i<=$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"border0010\"><INPUT type=\"checkbox\" class=\"checkbox adjust\" name=\"del$i\" value=\"1\"></TD>\n";
    echo "<INPUT type=\"hidden\" name=\"row$i\" value=\"".$bigarray[$i]["scheduleid"]."\">";
    echo "<INPUT type=\"hidden\" name=\"rowsession$i\" value=\"{$bigarray[$i]["sessionid"]}\"></TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".time_description($bigarray[$i]["starttime"])."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["duration"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["trackname"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\"> <a href=EditSession.php?id=".$bigarray[$i]["sessionid"].">".$bigarray[$i]["sessionid"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["title"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["roomsetname"]."</TD>\n";
    echo "      </TR>\n";
    }
echo "   </TABLE>\n";
echo "<H4 class=\"label\">Add To Room Schedule</H4>\n";
echo "<TABLE class=\"table table-condensed compressed\">\n";
$query = <<<EOD
SELECT
        S.sessionid, T.trackname, S.title, SCH.roomid
    FROM
        Sessions S JOIN
        Tracks T USING (trackid) JOIN
        SessionStatuses SS USING (statusid) LEFT JOIN
        Schedule SCH USING (sessionid)
    WHERE
        SS.may_be_scheduled=1
    HAVING
        SCH.roomid is null
    ORDER BY
        T.trackname, S.sessionid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"alert alert-error\">".$message."\n";
    staff_footer();
    exit();
    }
$i=1;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i++;
    }
$numsessions=--$i;
for ($i=1;$i<=newroomslots;$i++) {
    echo "   <TR>\n";
    echo "      <TD>";
    // ****DAY****
    if (CON_NUM_DAYS>1) {
        echo "<Select class=\"span2\" name=day$i><Option value=0 ";
        if ((!isset($_POST["day$i"])) or $_POST["day$i"]==0) echo "selected";
        echo ">Day&nbsp;</Option>";
        for ($j=1; $j<=CON_NUM_DAYS; $j++) {
            $x=$daymap["long"]["$j"];
            echo"         <OPTION value=$j ";
            if ($_POST["day$i"]==$j) echo "selected";
            echo ">$x</OPTION>\n";
            }
        echo "</Select>&nbsp;\n";
        }
	// ****HOUR****
    echo "          <Select class=\"span1 myspan1\" name=\"hour$i\"><Option value=\"-1\" ";
    if (!isset($_POST["hour$i"])) $_POST["hour$i"]=-1;
    if ($_POST["hour$i"]==-1) echo "selected";
    echo ">Hour&nbsp;</Option><Option value=0 ";
	if ($_POST["hour$i"]==0) echo "selected";
	echo ">12</Option>";
    for ($j=1;$j<=11;$j++) {
        echo "<Option value=$j ";
        if ($_POST["hour$i"]==$j) echo "selected";
        echo ">$j</Option>";
        }
    echo "</select>\n";
	// ****MIN****
    echo "          <Select class=\"span1 myspan1\" name=\"min$i\"><Option value=\"-1\" ";
	if (!isset($_POST["min$i"])) $_POST["min$i"]=-1;
    if ($_POST["min$i"]==-1) echo "selected";
	echo">Min&nbsp;</Option>";
    for ($j=0;$j<=55;$j+=5) {
        echo "<Option value=$j ";
        if ($_POST["min$i"]==$j) echo "selected";
		echo ">".($j<10?"0":"").$j."</Option>";
        }
    echo "</select>\n";
	// ****AM/PM****
    echo "          <Select class=\"span1 myspan1\" name=\"ampm$i\"><Option value=0 ";
    if ((!isset($_POST["ampm$i"])) or $_POST["ampm$i"]==0) echo "selected";
    echo ">AM&nbsp;</Option><Option value=1 ";
    if ($_POST["ampm$i"]==1) echo "selected";
	echo ">PM</Option>";
    echo "</select>\n";
    echo "          </TD>";
    // ****Session****
    echo "      <TD><Select class=\"span8\" name=\"sess$i\"><Option value=\"unset\" ";
	if ((!isset($_POST["sess$i"])) or $_POST["sess$i"]=="unset") echo "selected";
    echo ">Select Session</Option>\n";
    for ($j=1;$j<=$numsessions;$j++) {
        echo "          <Option value=\"".$bigarray[$j]["sessionid"]."\" ";
        if ($_POST["sess$i"]==$bigarray[$j]["sessionid"]) echo "selected";
		echo ">{$bigarray[$j]['trackname']} - {$bigarray[$j]['sessionid']} - {$bigarray[$j]['title']}</option>\n";
        }
    echo "</select>\n";
    echo "          </TD>\n";
    echo "       </TR>\n";
    }
echo "</TABLE>";
echo "<INPUT type=\"hidden\" name=\"selroom\" value=\"$selroomid\">\n";
echo "<INPUT type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"btn btn-primary\">Update</BUTTON></DIV>\n";
echo "</FORM>\n";
staff_footer();
?>


