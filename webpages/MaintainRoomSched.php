<?php
// Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $daymap, $message_error, $link, $title;
$bigarray = array();
define("newroomslots", 5); // number of rows at bottom of page for new schedule entries
$title = "Maintain Room Schedule";
require_once('db_functions.php');
require_once('data_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');

staff_header($title);
$topsectiononly = true; // no room selected -- flag indicates to display only the top section of the page
$conflict = false; // initialize
if (isset($_POST["numrows"])) {
    $ignore_conflicts = (isset($_POST['override'])) ? true : false;
    if (!SubmitMaintainRoom($ignore_conflicts))
        $conflict = true;
}

if (isset($_POST["selroom"]) && $_POST["selroom"] != "0") { // room was selected by this form
    $selroomid = $_POST["selroom"];
    $topsectiononly = false;
    //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
} elseif (isset($_GET["selroom"])) { // room was select by external page such as a report
    $selroomid = $_GET["selroom"];
    $topsectiononly = false;
} else {
    $selroomid = 0; // room was not yet selected.
    unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
}

if ($conflict != true) {
    $queryArray["rooms"] = "SELECT roomid, roomname, function, is_scheduled FROM Rooms ORDER BY display_order";
    if (($resultXML = mysql_query_XML($queryArray)) === false) {
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
        echo "<A HREF=\"" . $_SESSION['return_to_page'] . "\">Return to report</A>";
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
    for ($i = 1; $i <= newroomslots; $i++) {
        unset($_POST["day$i"]);
        unset($_POST["hour$i"]);
        unset($_POST["min$i"]);
        unset($_POST["ampm$i"]);
        unset($_POST["sess$i"]);
    }
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
SELECT
        roomid, roomname, opentime1, closetime1, opentime2, closetime2, opentime3, closetime3,
        function, floor, height, dimensions, area, notes
    FROM
        Rooms
    WHERE
        roomid = $selroomid;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);
echo "<h2>$selroomid - " . htmlspecialchars($row["roomname"]) . "</h2>";
echo "<h4 class=\"label\">Open Times</h4>\n";
echo "<div class=\"border1111 lrpad lrmargin\"><p class=\"lrmargin\">";
if ($row["opentime1"] != "") {
    echo time_description($row["opentime1"]) . " through " . time_description($row["closetime1"]) . "<br />\n";
}
if ($row["opentime2"] != "") {
    echo time_description($row["opentime2"]) . " through " . time_description($row["closetime2"]) . "<br />\n";
}
if ($row["opentime3"] != "") {
    echo time_description($row["opentime3"]) . " through " . time_description($row["closetime3"]) . "<br />\n";
}
echo "</div>\n";
echo "<h4 class=\"label\">Characteristics</H4>\n";
echo "   <table class=\"table table-condensed compressed\">\n";
echo "      <tr>\n";
echo "         <th class=\"lrpad border1111\">Function</th>\n";
echo "         <th class=\"lrpad border1111\">Floor</th>\n";
echo "         <th class=\"lrpad border1111\">Dimensions</th>\n";
echo "         <th class=\"lrpad border1111\">Area</th>\n";
echo "         <th class=\"lrpad border1111\">Height</th>\n";
echo "         </tr>\n";
echo "      <tr>\n";
echo "         <td class=\"lrpad border1111\">".htmlspecialchars($row["function"])."</td>\n";
echo "         <td class=\"lrpad border1111\">".htmlspecialchars($row["floor"])."</td>\n";
echo "         <td class=\"lrpad border1111\">".htmlspecialchars($row["dimensions"])."</td>\n";
echo "         <td class=\"lrpad border1111\">".htmlspecialchars($row["area"])."</td>\n";
echo "         <td class=\"lrpad border1111\">".htmlspecialchars($row["height"])."</td>\n";
echo "         </tr>\n";
if ($row["notes"] != "") {
    echo "        <tr>\n";
    echo "          <td colspan=5 class=\"alert alert-info\">" . htmlspecialchars($row["notes"]) . "</td>\n";
    echo "        </tr>\n";
}
echo "      </table>\n";
echo "<h4 class=\"label\">Room Sets</h4>\n";
$query = <<<EOD
SELECT
        RS.roomsetname, RHS.capacity
    FROM
             RoomSets RS
        JOIN RoomHasSet RHS USING (roomsetid)
    WHERE
        RHS.roomid = $selroomid;
EOD;
if (!$result=mysqli_query_exit_on_error($query)) {
    exit(); //should have exited already
}
$i = 1;
while ($bigarray[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $i++;
}
mysqli_free_result($result);
$numrows = $i;
echo "   <table class=\"table table-condensed compressed\">\n";
echo "      <tr>\n";
echo "         <th class=\"lrpad border1111\">Room Set</th>\n";
echo "         <th class=\"lrpad border1111\">Capacity</th>\n";
echo "         </tr>\n";
for ($i = 1; $i <= $numrows; $i++) {
    echo "   <tr>\n";
    echo "      <td class=\"vatop lrpad border1111\">" . $bigarray[$i]["roomsetname"] . "</td>\n";
    echo "      <td class=\"vatop lrpad border1111\">" . $bigarray[$i]["capacity"] . "</td>\n";
    echo "      </tr>\n";
}
echo "      </table>\n";
$query = <<<EOD
SELECT
        SCH.scheduleid, SCH.starttime, S.duration, SCH.sessionid, T.trackname, S.title, RS.roomsetname 
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Tracks T USING (trackid)
        JOIN RoomSets RS USING (roomsetid)
    WHERE
        SCH.roomid = $selroomid
    ORDER BY
        SCH.starttime;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
$i = 1;
while ($bigarray[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $i++;
}
mysqli_free_result($result);
$numrows = --$i;

echo "<hr />\n";
echo "<h4 class=\"label\">Current Room Schedule</H4>\n";
echo "<table class=\"table table-condensed compressed\">\n";
echo "   <tr>\n";
echo "      <th>Delete</th>\n";
echo "      <th>Start Time</th>\n";
echo "      <th>Duration</th>\n";
echo "      <th>Track</th>\n";
echo "      <th>Session ID</th>\n";
echo "      <th>Title</th>\n";
echo "      <th>Room Set</th>\n";
echo "      </tr>\n";
for ($i = 1; $i <= $numrows; $i++) {
    echo "   <tr>\n";
    echo "      <td class=\"border0010\"><input type=\"checkbox\" class=\"checkbox adjust\" name=\"del$i\" value=\"1\"></td>\n";
    echo "<input type=\"hidden\" name=\"row$i\" value=\"" . $bigarray[$i]["scheduleid"] . "\">";
    echo "<input type=\"hidden\" name=\"rowsession$i\" value=\"{$bigarray[$i]["sessionid"]}\"></td>\n";
    echo "      <td class=\"vatop lrpad border0010\">" . time_description($bigarray[$i]["starttime"]) . "</td>\n";
    echo "      <td class=\"vatop lrpad border0010\">" . $bigarray[$i]["duration"] . "</td>\n";
    echo "      <td class=\"vatop lrpad border0010\">" . $bigarray[$i]["trackname"] . "</td>\n";
    echo "      <td class=\"vatop lrpad border0010\"> <a href=EditSession.php?id=" . $bigarray[$i]["sessionid"] . ">" . $bigarray[$i]["sessionid"] . "</td>\n";
    echo "      <td class=\"vatop lrpad border0010\">" . $bigarray[$i]["title"] . "</td>\n";
    echo "      <td class=\"vatop lrpad border0010\">" . $bigarray[$i]["roomsetname"] . "</td>\n";
    echo "      </tr>\n";
}
echo "   </table>\n";
echo "<h4 class=\"label\">Add To Room Schedule</H4>\n";
echo "<table class=\"table table-condensed compressed\">\n";
$query = <<<EOD
SELECT
        S.sessionid, T.trackname, S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
    WHERE
            SS.may_be_scheduled = 1
        AND NOT EXISTS ( SELECT *
            FROM
                Schedule
            WHERE
                sessionid = S.sessionid
          )
    ORDER BY
        T.trackname, S.sessionid
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
$i = 1;
while ($bigarray[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $i++;
}
mysqli_free_result($result);
$numsessions = --$i;
for ($i = 1; $i <= newroomslots; $i++) {
    echo "   <tr>\n";
    echo "      <td>";
    // ****DAY****
    if (CON_NUM_DAYS>1) {
        echo "<select class=\"span2\" name=day$i><option value=0 ";
        if ((!isset($_POST["day$i"])) or $_POST["day$i"]==0)
            echo "selected";
        echo ">Day&nbsp;</option>";
        for ($j=1; $j<=CON_NUM_DAYS; $j++) {
            $x=$daymap["long"]["$j"];
            echo"         <option value=$j ";
            if (isset($_POST["day$i"]) && $_POST["day$i"]==$j)
                echo "selected";
            echo ">$x</option>\n";
            }
        echo "</Select>&nbsp;\n";
        }
	// ****HOUR****
    echo "          <select class=\"span1 myspan1\" name=\"hour$i\"><option value=\"-1\" ";
    if (!isset($_POST["hour$i"]))
        $_POST["hour$i"]=-1;
    if ($_POST["hour$i"]==-1)
        echo "selected";
    echo ">Hour&nbsp;</option><option value=0 ";
	if ($_POST["hour$i"]==0)
	    echo "selected";
	echo ">12</option>";
    for ($j=1;$j<=11;$j++) {
        echo "<option value=$j ";
        if ($_POST["hour$i"]==$j)
            echo "selected";
        echo ">$j</option>";
        }
    echo "</select>\n";
	// ****MIN****
    echo "          <select class=\"span1 myspan1\" name=\"min$i\"><option value=\"-1\" ";
	if (!isset($_POST["min$i"]))
	    $_POST["min$i"]=-1;
    if ($_POST["min$i"]==-1)
        echo "selected";
	echo">Min&nbsp;</option>";
    for ($j=0;$j<=55;$j+=5) {
        echo "<option value=$j ";
        if ($_POST["min$i"]==$j)
            echo "selected";
		echo ">".($j<10?"0":"").$j."</option>";
        }
    echo "</select>\n";
	// ****AM/PM****
    echo "          <Select class=\"span1 myspan1\" name=\"ampm$i\"><option value=0 ";
    if ((!isset($_POST["ampm$i"])) or $_POST["ampm$i"]==0)
        echo "selected";
    echo ">AM&nbsp;</option><option value=1 ";
    if (isset($_POST["ampm$i"]) && $_POST["ampm$i"]==1)
        echo "selected";
	echo ">PM</option>";
    echo "</select>\n";
    echo "          </td>";
    // ****Session****
    echo "      <td><Select class=\"span8\" name=\"sess$i\"><option value=\"unset\" ";
	if ((!isset($_POST["sess$i"])) or $_POST["sess$i"]=="unset")
	    echo "selected";
    echo ">Select Session</option>\n";
    for ($j=1;$j<=$numsessions;$j++) {
        echo "          <option value=\"".$bigarray[$j]["sessionid"]."\" ";
        if (isset($_POST["sess$i"]) && $_POST["sess$i"]==$bigarray[$j]["sessionid"])
            echo "selected";
		echo ">{$bigarray[$j]['trackname']} - {$bigarray[$j]['sessionid']} - {$bigarray[$j]['title']}</option>\n";
        }
    echo "</select>\n";
    echo "          </td>\n";
    echo "       </tr>\n";
    }
echo "</table>";
echo "<input type=\"hidden\" name=\"selroom\" value=\"$selroomid\">\n";
echo "<input type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<div class=\"SubmitDiv\"><button type=\"submit\" name=\"update\" class=\"btn btn-primary\">Update</button></div>\n";
echo "</form>\n";
staff_footer();
?>
