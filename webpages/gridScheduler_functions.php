<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.

// Shared by GridSchedulerNew.php (initial page bootstrap) and GridSchedulerAjax.php (refresh actions) --
// the new React-based replacement for the Grid Scheduler page (see webpages/StaffMaintainSchedule.php /
// staffMaintainScheduleSubmit.php for the implementation being replaced).
//
// NOTE: gridScheduler_getDaySegments() below is a simplified, static stand-in for the legacy
// getScheduleTimesArray() (webpages/staffMaintainScheduleSubmit.php): it derives one segment per day
// straight from FIRST_DAY_START_TIME/OTHER_DAY_START_TIME/OTHER_DAY_STOP_TIME/LAST_DAY_STOP_TIME, and
// does not reproduce that function's dynamic trimming to the actual earliest/latest scheduled session,
// nor its inserted "overnight gap" rows. This is expected to be replaced by GRID_DAY_SEGMENTS-driven
// logic in a later phase of the grid scheduler rewrite.

function gridScheduler_timeStringToMinutes($timeStr) {
    sscanf($timeStr, "%d:%d", $hours, $minutes);
    return $hours * 60 + $minutes;
}

function gridScheduler_getDaySegments() {
    $segments = array();
    $firstDayStartMinutes = gridScheduler_timeStringToMinutes(FIRST_DAY_START_TIME);
    $otherDayStartMinutes = gridScheduler_timeStringToMinutes(OTHER_DAY_START_TIME);
    $otherDayStopMinutes = gridScheduler_timeStringToMinutes(OTHER_DAY_STOP_TIME);
    $lastDayStopMinutes = gridScheduler_timeStringToMinutes(LAST_DAY_STOP_TIME);
    for ($day = 1; $day <= CON_NUM_DAYS; $day++) {
        $dayOffsetMinutes = ($day - 1) * 24 * 60;
        $startMinutes = ($day == 1) ? $firstDayStartMinutes : $dayOffsetMinutes + $otherDayStartMinutes;
        $endMinutes = ($day == CON_NUM_DAYS) ? $dayOffsetMinutes + $lastDayStopMinutes : $dayOffsetMinutes + $otherDayStopMinutes;
        $segments[] = array(
            "day" => $day,
            "dayName" => longDayNameFromInt($day),
            "startMinutes" => $startMinutes,
            "endMinutes" => $endMinutes,
        );
    }
    return $segments;
}

// Returns the current rooms/schedule state as a plain array, ready for json_encode() -- used both for
// the initial page bootstrap and for ajax refreshes, so the two stay in sync automatically.
function gridScheduler_buildData() {
    $data = array();
    $data["conStartDateTime"] = str_replace(" ", "T", CON_START_DATIM);
    $data["conNumDays"] = CON_NUM_DAYS;
    $data["resolutionMinutes"] = 30; // TODO: replace with GRID_TIME_RESOLUTION_MINUTES in a later phase
    $data["trackTagUsage"] = TRACK_TAG_USAGE;
    $data["daySegments"] = gridScheduler_getDaySegments();

    $rooms = array();
    $query = "SELECT roomid, roomname, display_order FROM Rooms WHERE is_scheduled = 1 ORDER BY display_order;";
    $result = mysqli_query_with_error_handling($query, true, true);
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = array(
            "roomid" => intval($row["roomid"]),
            "roomname" => $row["roomname"],
            "display_order" => is_null($row["display_order"]) ? null : intval($row["display_order"]),
        );
    }
    mysqli_free_result($result);
    $data["rooms"] = $rooms;

    $schedule = array();
    $query = <<<EOD
SELECT
        SCH.scheduleid, SCH.roomid, SCH.starttime, S.sessionid, S.title,
        TIME_TO_SEC(S.duration) / 60 AS durationMinutes,
        TR.trackname, TY.typename, D.divisionname
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Tracks TR USING (trackid)
        JOIN Types TY USING (typeid)
        JOIN Divisions D USING (divisionid)
    ORDER BY
        SCH.roomid, SCH.starttime;
EOD;
    $result = mysqli_query_with_error_handling($query, true, true);
    while ($row = mysqli_fetch_assoc($result)) {
        $schedule[] = array(
            "scheduleid" => intval($row["scheduleid"]),
            "roomid" => intval($row["roomid"]),
            "sessionid" => intval($row["sessionid"]),
            "title" => $row["title"],
            "startMinutes" => gridScheduler_timeStringToMinutes($row["starttime"]),
            "durationMinutes" => intval($row["durationMinutes"]),
            "trackname" => $row["trackname"],
            "typename" => $row["typename"],
            "divisionname" => $row["divisionname"],
        );
    }
    mysqli_free_result($result);
    $data["schedule"] = $schedule;

    return $data;
}
?>
