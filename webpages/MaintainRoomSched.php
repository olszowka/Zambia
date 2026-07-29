<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
$title = "Maintain Room Schedule";
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');

staff_header($title, 'bs5');
$topsectiononly = true; // no room selected -- flag indicates to display only the top section of the page
$conflict = false; // initialize
if (isset($_POST["numrows"])) {
    $ignore_conflicts = (isset($_POST['override'])) ? true : false;
    if (!SubmitMaintainRoom($ignore_conflicts))
        $conflict = true;
}

if (isset($_POST["selroom"]) && $_POST["selroom"] != "0") { // room was selected by this form
    $selroomid = (int)$_POST["selroom"];
    $topsectiononly = false;
    //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
} elseif (isset($_GET["selroom"])) { // room was select by external page such as a report
    $selroomid = (int)$_GET["selroom"];
    $topsectiononly = false;
} else {
    $selroomid = 0; // room was not yet selected.
    unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
}

if ($conflict != true) {
    $queryArray = array();
    $queryArray["rooms"] = "SELECT roomid, roomname, `function`, is_scheduled FROM Rooms ORDER BY display_order";
    if (($resultXML = mysql_query_XML($queryArray)) === false) {
        RenderErrorAjax($message_error); //header has already been sent, so can just send error message and stop.
        exit();
    }
    $paramArray = array();
    $paramArray['showUnschedRmsCHK'] = isset($_POST["showUnschedRmsCHK"]) ? '1' : '0';
    $paramArray['returnToPage'] = isset($_SESSION['return_to_page']) ? $_SESSION['return_to_page'] : '';
    RenderXSLT('MaintainRoomSched_roomSelect.xsl', $paramArray, $resultXML);
    // unset all stuff from posts so input fields get reset to blank
    for ($i = 1; $i <= NEW_ROOM_SLOTS; $i++) {
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

$queryArray = array();
$queryArray["roomInfo"] = <<<EOD
SELECT
        roomid, roomname, `function`, floor, height, dimensions, area, notes
    FROM
        Rooms
    WHERE
        roomid = $selroomid;
EOD;
$queryArray["roomSets"] = <<<EOD
SELECT
        RS.roomsetname, RHS.capacity
    FROM
             RoomSets RS
        JOIN RoomHasSet RHS USING (roomsetid)
    WHERE
        RHS.roomid = $selroomid;
EOD;
$queryArray["availableSessions"] = <<<EOD
SELECT
        S.sessionid, T.trackname, S.title, TY.typename
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN Types TY USING (typeid)
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
if (($resultXML = mysql_query_XML($queryArray)) === false) {
    RenderErrorAjax($message_error);
    exit();
}

// Built separately (rather than as part of $queryArray above) so that the day/time/duration fields can be
// edited: on a conflict, nothing was written to the database, so the row order/count here is unchanged from
// what produced the form the user just submitted, and we can safely redisplay their just-typed edits (keyed by
// row number) instead of the (unchanged) database values.
$query = <<<EOD
SELECT
        SCH.scheduleid, SCH.starttime, SCH.sessionid, S.duration, T.trackname, S.title, TY.typename,
        GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
    FROM
                  Schedule SCH
             JOIN Sessions S USING (sessionid)
             JOIN Tracks T USING (trackid)
             JOIN Types TY USING (typeid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
        LEFT JOIN Tags TA USING (tagid)
    WHERE
        SCH.roomid = $selroomid
    GROUP BY
        SCH.scheduleid
    ORDER BY
        SCH.starttime;
EOD;
if (!$result = mysqli_query_with_error_handling($query, true)) {
    exit(); // should have exited already
}
$currentSchedule = array();
$rownum = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $rownum++;
    list($day, $editampm, $edittime) = starttime_to_edit_fields($row['starttime']);
    $editduration = format_duration_for_edit($row['duration']);

    if ($conflict) {
        if (CON_NUM_DAYS > 1 && isset($_POST["editday$rownum"])) {
            $day = (int)$_POST["editday$rownum"];
        }
        if (isset($_POST["edittime$rownum"])) {
            $edittime = $_POST["edittime$rownum"];
        }
        if (isset($_POST["editampm$rownum"])) {
            $editampm = (int)$_POST["editampm$rownum"];
        }
        if (isset($_POST["editduration$rownum"])) {
            $editduration = $_POST["editduration$rownum"];
        }
    }

    $sched = new stdClass();
    $sched->scheduleid = $row['scheduleid'];
    $sched->sessionid = $row['sessionid'];
    $sched->title = $row['title'];
    $sched->trackname = $row['trackname'];
    $sched->typename = $row['typename'];
    $sched->taglist = $row['taglist'];
    $sched->editday = $day;
    $sched->edittime = $edittime;
    $sched->editampm = $editampm;
    $sched->editduration = $editduration;
    $currentSchedule[] = $sched;
}
mysqli_free_result($result);
$resultXML = ObjecttoXML('currentSchedule', $currentSchedule, $resultXML);

$days = array();
for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
    $day = new stdClass();
    $day->value = $j;
    $day->name = longDayNameFromInt($j);
    $days[] = $day;
}
$resultXML = ObjecttoXML('days', $days, $resultXML);

$hours = array();
for ($j = 0; $j <= 11; $j++) {
    $hour = new stdClass();
    $hour->value = $j;
    $hour->display = ($j == 0) ? '12' : $j;
    $hours[] = $hour;
}
$resultXML = ObjecttoXML('hours', $hours, $resultXML);

$minutes = array();
for ($j = 0; $j <= 55; $j += 5) {
    $minute = new stdClass();
    $minute->value = $j;
    $minute->display = ($j < 10 ? "0$j" : $j);
    $minutes[] = $minute;
}
$resultXML = ObjecttoXML('minutes', $minutes, $resultXML);

$newRoomSlots = array();
for ($i = 1; $i <= NEW_ROOM_SLOTS; $i++) {
    $slot = new stdClass();
    $slot->slot = $i;
    $slot->day = isset($_POST["day$i"]) ? (int)$_POST["day$i"] : 0;
    $slot->hour = isset($_POST["hour$i"]) ? (int)$_POST["hour$i"] : -1;
    $slot->min = isset($_POST["min$i"]) ? (int)$_POST["min$i"] : -1;
    $slot->ampm = isset($_POST["ampm$i"]) ? (int)$_POST["ampm$i"] : 0;
    $slot->sess = isset($_POST["sess$i"]) ? $_POST["sess$i"] : "unset";
    $newRoomSlots[] = $slot;
}
$resultXML = ObjecttoXML('newRoomSlots', $newRoomSlots, $resultXML);

$paramArray = array();
$paramArray['conflict'] = $conflict ? '1' : '0';
$paramArray['trackTagUsage'] = TRACK_TAG_USAGE;
$paramArray['conNumDays'] = CON_NUM_DAYS;
RenderXSLT('MaintainRoomSched.xsl', $paramArray, $resultXML);
staff_footer();
exit();
?>
