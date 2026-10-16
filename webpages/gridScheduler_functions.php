<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.

// Shared by StaffMaintainSchedule.php (initial page bootstrap) and GridSchedulerAjax.php (refresh actions) --
// the server side of the React-based Grid Scheduler (react-apps/GridScheduler). It replaced the legacy
// server-rendered implementation (StaffMaintainSchedule_FNC.php / staffMaintainScheduleSubmit.php, since deleted).

// Grid configuration problems found in db_name.php -- StaffMaintainSchedule.php catches this and shows the message in
// place of the grid, so a typo in a GRID_* constant is reported plainly rather than producing a broken grid.
class GridSchedulerConfigException extends Exception {
}

function gridScheduler_timeStringToMinutes($timeStr) {
    sscanf($timeStr, "%d:%d", $hours, $minutes);
    return $hours * 60 + $minutes;
}

// Strict version of gridScheduler_timeStringToMinutes() for admin-entered GRID_* values: "H:MM" or "HH:MM", hours may
// exceed 23 (e.g. "25:00" for 1:00 AM the next morning). Returns null if malformed.
function gridScheduler_parseConfigTime($timeStr) {
    if (!is_string($timeStr) || !preg_match('/^(\d{1,2}):([0-5]\d)$/', $timeStr, $matches)) {
        return null;
    }
    return intval($matches[1]) * 60 + intval($matches[2]);
}

// Grid-line resolution: the room columns' empty-grid row lines, and the rows the Grid snap mode snaps to. This is one
// of three distinct, similarly-named resolutions in the grid scheduler -- see the "Time resolutions" section of the
// rewrite plan. Do not conflate with snap resolution (the page-level Snap selector in the React app) or display
// resolution (fixed at 1 minute, a non-configurable constant on the client -- see DISPLAY_RESOLUTION_MINUTES in
// react-apps/GridScheduler/src/components/Grid.tsx).
// GRID_SPACING_MINUTES is optional. When it isn't defined, STANDARD_BLOCK_LENGTH ("1:30" -> 90) is used, which
// matches the legacy grid's empty slots. STANDARD_BLOCK_LENGTH itself stays as is, since other features
// (reports/conflictbacktoback.php) depend on it.
function gridScheduler_getGridLineResolutionMinutes() {
    if (defined("GRID_SPACING_MINUTES")) {
        $resolution = GRID_SPACING_MINUTES;
        if (is_string($resolution) && ctype_digit($resolution)) {
            $resolution = intval($resolution);
        }
        if (!is_int($resolution) || $resolution < 5 || $resolution > 24 * 60) {
            throw new GridSchedulerConfigException("GRID_SPACING_MINUTES must be a whole number of minutes from 5 to 1440.");
        }
        return $resolution;
    }
    $resolution = defined("STANDARD_BLOCK_LENGTH") ? gridScheduler_parseConfigTime(STANDARD_BLOCK_LENGTH) : null;
    return empty($resolution) ? 30 : $resolution;
}

// DEFAULT_DURATION (the duration a new session starts with, in DURATION_IN_MINUTES's format) in minutes, or null if
// it's missing or doesn't parse. Grid Simplification only applies to sessions of exactly this duration -- see
// simplifiedEndMinutes() in react-apps/GridScheduler/src/timeGrid.ts.
function gridScheduler_getDefaultDurationMinutes() {
    if (!defined("DEFAULT_DURATION")) {
        return null;
    }
    $duration = parse_duration_for_db(DEFAULT_DURATION);
    if ($duration === false) {
        return null;
    }
    sscanf($duration, "%d:%d", $hours, $minutes);
    return $hours * 60 + $minutes;
}

// The grid's displayed time ranges, in minutes since CON_START_DATIM, in order and never overlapping. Anything
// outside them (overnight, a lunch break) isn't drawn at all. The client widens a segment if a session is scheduled
// outside every segment, so nothing is ever drawn at the wrong time -- see coverScheduledSessions() in timeGrid.ts.
// GRID_DAY_SEGMENTS is optional. When it isn't defined, one segment per day is derived from the legacy
// FIRST_DAY_START_TIME / OTHER_DAY_START_TIME / OTHER_DAY_STOP_TIME / LAST_DAY_STOP_TIME constants, so existing installs
// keep working unedited.
function gridScheduler_getDaySegments() {
    $segments = defined("GRID_DAY_SEGMENTS")
        ? gridScheduler_daySegmentsFromConfig(GRID_DAY_SEGMENTS)
        : gridScheduler_daySegmentsFromLegacyConstants();
    foreach ($segments as &$segment) {
        $segment["dayName"] = longDayNameFromInt($segment["day"]);
    }
    unset($segment);
    return $segments;
}

function gridScheduler_daySegmentsFromConfig($config) {
    $usage = 'GRID_DAY_SEGMENTS must be a list of segments like ["day" => 1, "start" => "17:30", "end" => "25:00"].';
    if (!is_array($config) || count($config) === 0) {
        throw new GridSchedulerConfigException($usage);
    }
    $segments = array();
    foreach (array_values($config) as $index => $entry) {
        $position = $index + 1;
        if (!is_array($entry) || !isset($entry["day"], $entry["start"], $entry["end"])) {
            throw new GridSchedulerConfigException("GRID_DAY_SEGMENTS entry $position is malformed. $usage");
        }
        $day = $entry["day"];
        if (!is_int($day) || $day < 1 || $day > CON_NUM_DAYS) {
            throw new GridSchedulerConfigException("GRID_DAY_SEGMENTS entry $position: \"day\" must be a whole number from 1 to CON_NUM_DAYS (" . CON_NUM_DAYS . ").");
        }
        $start = gridScheduler_parseConfigTime($entry["start"]);
        $end = gridScheduler_parseConfigTime($entry["end"]);
        if (is_null($start) || is_null($end)) {
            throw new GridSchedulerConfigException("GRID_DAY_SEGMENTS entry $position: \"start\" and \"end\" must be times like \"9:00\" or \"25:00\".");
        }
        if ($end <= $start) {
            throw new GridSchedulerConfigException("GRID_DAY_SEGMENTS entry $position: \"end\" must be later than \"start\". Use a time past 24:00 to end after midnight.");
        }
        $dayOffsetMinutes = ($day - 1) * 24 * 60;
        $segments[] = array(
            "day" => $day,
            "startMinutes" => $dayOffsetMinutes + $start,
            "endMinutes" => $dayOffsetMinutes + $end,
        );
        if ($index > 0 && $segments[$index]["startMinutes"] < $segments[$index - 1]["endMinutes"]) {
            throw new GridSchedulerConfigException("GRID_DAY_SEGMENTS entry $position starts before entry " . ($position - 1) . " ends. List segments in order, without overlaps.");
        }
    }
    return $segments;
}

// Unlike the legacy grid (getScheduleTimesArray() in the deleted staffMaintainScheduleSubmit.php), this doesn't trim
// or extend days based on what's scheduled; the client's coverScheduledSessions() handles sessions outside these
// times instead. If a day's stop time runs past the next day's start, the earlier day is cut short there so the
// segments don't overlap. A day whose stop time isn't after its start time (e.g. CON_NUM_DAYS 1 with
// LAST_DAY_STOP_TIME "16:00" before FIRST_DAY_START_TIME "17:30") is a configuration error, rather than a day
// silently left off the grid.
function gridScheduler_daySegmentsFromLegacyConstants() {
    $segments = array();
    $firstDayStartMinutes = gridScheduler_timeStringToMinutes(FIRST_DAY_START_TIME);
    $otherDayStartMinutes = gridScheduler_timeStringToMinutes(OTHER_DAY_START_TIME);
    $otherDayStopMinutes = gridScheduler_timeStringToMinutes(OTHER_DAY_STOP_TIME);
    $lastDayStopMinutes = gridScheduler_timeStringToMinutes(LAST_DAY_STOP_TIME);
    for ($day = 1; $day <= CON_NUM_DAYS; $day++) {
        $dayOffsetMinutes = ($day - 1) * 24 * 60;
        $startName = ($day == 1) ? "FIRST_DAY_START_TIME" : "OTHER_DAY_START_TIME";
        $stopName = ($day == CON_NUM_DAYS) ? "LAST_DAY_STOP_TIME" : "OTHER_DAY_STOP_TIME";
        $startMinutes = $dayOffsetMinutes + (($day == 1) ? $firstDayStartMinutes : $otherDayStartMinutes);
        $endMinutes = $dayOffsetMinutes + (($day == CON_NUM_DAYS) ? $lastDayStopMinutes : $otherDayStopMinutes);
        if ($endMinutes <= $startMinutes) {
            throw new GridSchedulerConfigException("Day $day of the grid has no time to show: $stopName (\"" . constant($stopName)
                . "\") must be later than $startName (\"" . constant($startName) . "\"). Use a time past 24:00 to stop after midnight.");
        }
        if ($day < CON_NUM_DAYS) {
            $nextDayStartMinutes = $dayOffsetMinutes + 24 * 60 + $otherDayStartMinutes;
            if ($nextDayStartMinutes <= $startMinutes) {
                throw new GridSchedulerConfigException("Day $day of the grid has no time to show: $startName (\"" . constant($startName)
                    . "\") is at or after the next day's OTHER_DAY_START_TIME (\"" . OTHER_DAY_START_TIME . "\").");
            }
            $endMinutes = min($endMinutes, $nextDayStartMinutes);
        }
        $segments[] = array("day" => $day, "startMinutes" => $startMinutes, "endMinutes" => $endMinutes);
    }
    return $segments;
}

// Which fields a session box shows (on the grid, in the "sessions to be scheduled" list, and while dragging), as a
// list of lines, each a list of field names. The info icon always starts the first line. Every line is a single line
// of text, clipped if too long; the box clips any lines that don't fit its height.
const GRID_SESSION_BOX_FIELD_NAMES = array("title", "sessionid", "type", "track", "tags", "division", "duration");

// GRID_SESSION_BOX_FIELDS is optional. When it isn't defined, boxes show title / session id and type / track or tags,
// whichever TRACK_TAG_USAGE makes primary. When it is defined, it's followed as is, regardless of TRACK_TAG_USAGE.
function gridScheduler_getSessionBoxFields() {
    if (!defined("GRID_SESSION_BOX_FIELDS")) {
        $tagPrimary = TRACK_TAG_USAGE === "TAG_ONLY" || TRACK_TAG_USAGE === "TAG_OVER_TRACK";
        return array(array("title"), array("sessionid", "type"), array($tagPrimary ? "tags" : "track"));
    }
    $usage = 'GRID_SESSION_BOX_FIELDS must be a list of lines, each a list of field names, like [["title"], ["sessionid", "type"], ["tags"]]. '
        . 'Available fields: ' . implode(", ", GRID_SESSION_BOX_FIELD_NAMES) . '.';
    if (!is_array(GRID_SESSION_BOX_FIELDS) || count(GRID_SESSION_BOX_FIELDS) === 0) {
        throw new GridSchedulerConfigException($usage);
    }
    $lines = array();
    foreach (GRID_SESSION_BOX_FIELDS as $line) {
        if (is_string($line)) {
            $line = array($line);
        }
        if (!is_array($line) || count($line) === 0) {
            throw new GridSchedulerConfigException($usage);
        }
        foreach ($line as $field) {
            if (!in_array($field, GRID_SESSION_BOX_FIELD_NAMES, true)) {
                throw new GridSchedulerConfigException("GRID_SESSION_BOX_FIELDS: unknown field \"" . (is_string($field) ? $field : gettype($field)) . "\". $usage");
            }
        }
        $lines[] = array_values($line);
    }
    return $lines;
}

// Tag names for the given sessions (all scheduled sessions if $sessionIds is null), in Tags.display_order, keyed by
// sessionid. Sessions without tags are omitted.
function gridScheduler_fetchTagNames($sessionIds) {
    $tagNames = array();
    if (is_null($sessionIds)) {
        $query = "SELECT SHT.sessionid, TA.tagname FROM SessionHasTag SHT JOIN Tags TA USING (tagid)"
            . " WHERE EXISTS (SELECT * FROM Schedule SCH WHERE SCH.sessionid = SHT.sessionid)"
            . " ORDER BY TA.display_order, TA.tagname;";
        $result = mysqli_query_with_error_handling($query, true, true);
    } elseif (count($sessionIds) === 0) {
        return $tagNames;
    } else {
        $placeholders = implode(",", array_fill(0, count($sessionIds), "?"));
        $query = "SELECT SHT.sessionid, TA.tagname FROM SessionHasTag SHT JOIN Tags TA USING (tagid)"
            . " WHERE SHT.sessionid IN ($placeholders) ORDER BY TA.display_order, TA.tagname;";
        $result = mysqli_query_with_prepare_and_exit_on_error($query, str_repeat("i", count($sessionIds)), $sessionIds);
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $tagNames[intval($row["sessionid"])][] = $row["tagname"];
    }
    mysqli_free_result($result);
    return $tagNames;
}

// Shared by the Sessions tab's filter dropdowns (tracks/types/divisions single-select, tags multi-select)
// -- mirrors populate_select_from_table()/populate_multiselect_from_table() (db_functions.php), but as
// JSON instead of <option> HTML.
function gridScheduler_fetchLookupList($table, $idColumn, $nameColumn) {
    $list = array();
    $query = "SELECT $idColumn AS id, $nameColumn AS name FROM $table ORDER BY display_order;";
    $result = mysqli_query_with_error_handling($query, true, true);
    while ($row = mysqli_fetch_assoc($result)) {
        $list[] = array("id" => intval($row["id"]), "name" => $row["name"]);
    }
    mysqli_free_result($result);
    return $list;
}

// The full current schedule, in the same shape as the bootstrap payload's "schedule" array -- also returned by
// editSchedule after every write, so the client always ends up showing exactly what's in the database (including
// other staff members' concurrent edits) rather than its own optimistic guess.
function gridScheduler_fetchSchedule() {
    $schedule = array();
    $query = <<<EOD
SELECT
        SCH.scheduleid, SCH.roomid, SCH.starttime, S.sessionid, S.title,
        TIME_TO_SEC(S.duration) / 60 AS durationMinutes,
        TR.trackname, TY.typename, TY.allow_grid_simplification, D.divisionname
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
    $tagNames = gridScheduler_fetchTagNames(null);
    while ($row = mysqli_fetch_assoc($result)) {
        $sessionId = intval($row["sessionid"]);
        $schedule[] = array(
            "scheduleid" => intval($row["scheduleid"]),
            "roomid" => intval($row["roomid"]),
            "sessionid" => $sessionId,
            "title" => $row["title"],
            "startMinutes" => gridScheduler_timeStringToMinutes($row["starttime"]),
            "durationMinutes" => intval($row["durationMinutes"]),
            "trackname" => $row["trackname"],
            "typename" => $row["typename"],
            "typeGridSimplification" => $row["allow_grid_simplification"] == 1,
            "divisionname" => $row["divisionname"],
            "tagnames" => isset($tagNames[$sessionId]) ? $tagNames[$sessionId] : array(),
        );
    }
    mysqli_free_result($result);
    return $schedule;
}

// Returns the page's bootstrap payload as a plain array, ready for json_encode(). Throws GridSchedulerConfigException
// if a GRID_* constant in db_name.php is invalid.
function gridScheduler_buildData() {
    $data = array();
    $data["badgeid"] = $_SESSION['badgeid'];
    $data["conStartDateTime"] = str_replace(" ", "T", CON_START_DATIM);
    $data["conNumDays"] = CON_NUM_DAYS;
    $data["gridLineResolutionMinutes"] = gridScheduler_getGridLineResolutionMinutes();
    $data["trackTagUsage"] = TRACK_TAG_USAGE;
    $data["daySegments"] = gridScheduler_getDaySegments();
    $data["sessionBoxFields"] = gridScheduler_getSessionBoxFields();
    $data["defaultDurationMinutes"] = gridScheduler_getDefaultDurationMinutes();

    $rooms = array();
    $query = "SELECT roomid, roomname, display_order, allow_grid_simplification FROM Rooms WHERE is_scheduled = 1 ORDER BY display_order;";
    $result = mysqli_query_with_error_handling($query, true, true);
    while ($row = mysqli_fetch_assoc($result)) {
        $rooms[] = array(
            "roomid" => intval($row["roomid"]),
            "roomname" => $row["roomname"],
            "display_order" => is_null($row["display_order"]) ? null : intval($row["display_order"]),
            "gridSimplification" => $row["allow_grid_simplification"] == 1,
        );
    }
    mysqli_free_result($result);
    $data["rooms"] = $rooms;

    $data["schedule"] = gridScheduler_fetchSchedule();

    // Lookup lists for the Sessions tab's search filters.
    $data["tracks"] = gridScheduler_fetchLookupList("Tracks", "trackid", "trackname");
    $data["tags"] = gridScheduler_fetchLookupList("Tags", "tagid", "tagname");
    $data["types"] = gridScheduler_fetchLookupList("Types", "typeid", "typename");
    $data["divisions"] = gridScheduler_fetchLookupList("Divisions", "divisionid", "divisionname");

    return $data;
}

// Ports the legacy staffMaintainScheduleSubmit.php's retrieveSessions() to JSON output with bound
// parameters instead of directly-interpolated SQL (the legacy version int-casts ids via
// getInt()/getArrayOfInts() but still string-builds the query by hand). Every filter is optional; only
// unscheduled sessions in a schedulable status are ever considered.
function gridScheduler_searchSessions() {
    $currSessionIdArray = getArrayOfInts("currSessionIdArray", array());
    $trackId = getInt("trackId", 0);
    $tagIds = getArrayOfInts("tagIds", array());
    $typeId = getInt("typeId", 0);
    $divisionId = getInt("divisionId", 0);
    $sessionId = getInt("sessionId", null);
    $title = mb_strtolower(getString("title"));
    $tagmatch = getString("tagmatch");
    $personsAssigned = getInt("personsAssigned", 0);

    $where = "S.statusid IN (2,3,7) AND NOT EXISTS (SELECT * FROM Schedule SCH WHERE S.sessionid = SCH.sessionid)";
    $types = "";
    $params = array();

    if ($trackId !== 0) {
        $where .= " AND S.trackid = ?";
        $types .= "i";
        $params[] = $trackId;
    }
    if ($typeId !== 0) {
        $where .= " AND S.typeid = ?";
        $types .= "i";
        $params[] = $typeId;
    }
    if ($divisionId !== 0) {
        $where .= " AND S.divisionid = ?";
        $types .= "i";
        $params[] = $divisionId;
    }
    if (!is_null($sessionId)) {
        $where .= " AND S.sessionid = ?";
        $types .= "i";
        $params[] = $sessionId;
    }
    if ($title !== "") {
        $where .= " AND LOWER(S.title) LIKE ?";
        $types .= "s";
        $params[] = "%$title%";
    }
    if (count($currSessionIdArray) > 0) {
        $placeholders = implode(",", array_fill(0, count($currSessionIdArray), "?"));
        $where .= " AND S.sessionid NOT IN ($placeholders)";
        $types .= str_repeat("i", count($currSessionIdArray));
        foreach ($currSessionIdArray as $id) {
            $params[] = $id;
        }
    }
    if (count($tagIds) > 0) {
        if ($tagmatch === "all") {
            foreach ($tagIds as $tag) {
                $where .= " AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S.sessionid AND tagid = ?)";
                $types .= "i";
                $params[] = $tag;
            }
        } else {
            $placeholders = implode(",", array_fill(0, count($tagIds), "?"));
            $where .= " AND EXISTS (SELECT * FROM SessionHasTag WHERE sessionid = S.sessionid AND tagid IN ($placeholders))";
            $types .= str_repeat("i", count($tagIds));
            foreach ($tagIds as $tag) {
                $params[] = $tag;
            }
        }
    }
    if ($personsAssigned === 1) {
        $where .= " AND EXISTS (SELECT * FROM ParticipantOnSession WHERE sessionid = S.sessionid)";
    }

    $query = "SELECT S.sessionid, S.title, TR.trackname, TY.typename, TY.allow_grid_simplification, D.divisionname,"
        . " TIME_TO_SEC(S.duration) / 60 AS durationMinutes"
        . " FROM Sessions S JOIN Tracks TR USING (trackid) JOIN Types TY USING (typeid) JOIN Divisions D USING (divisionid)"
        . " WHERE $where ORDER BY TR.trackname, S.sessionid;";

    if (count($params) > 0) {
        $result = mysqli_query_with_prepare_and_exit_on_error($query, $types, $params);
    } else {
        $result = mysqli_query_with_error_handling($query, true, true);
    }

    $sessions = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $sessions[] = array(
            "sessionid" => intval($row["sessionid"]),
            "title" => $row["title"],
            "trackname" => $row["trackname"],
            "typename" => $row["typename"],
            "typeGridSimplification" => $row["allow_grid_simplification"] == 1,
            "divisionname" => $row["divisionname"],
            "durationMinutes" => intval($row["durationMinutes"]),
        );
    }
    mysqli_free_result($result);
    $tagNames = gridScheduler_fetchTagNames(array_column($sessions, "sessionid"));
    foreach ($sessions as &$session) {
        $session["tagnames"] = isset($tagNames[$session["sessionid"]]) ? $tagNames[$session["sessionid"]] : array();
    }
    unset($session);
    return $sessions;
}

// Ports the legacy staffMaintainScheduleSubmit.php's retrieveSessionInfo() to JSON output with bound
// parameters -- the legacy version reads $_POST["sessionid"] straight into the query with no int-cast at
// all, a real (if login-gated) SQL injection hole this rewrite does not carry forward. Returns null if no
// such session exists.
function gridScheduler_getSessionInfo($sessionId) {
    $conStartDatim = CON_START_DATIM;
    // The weekday adds nothing for a one-day con.
    $timeFormat = (CON_NUM_DAYS > 1) ? "%a %l:%i %p" : "%l:%i %p";
    $query = "SELECT S.sessionid, S.title, S.progguiddesc, S.notesforprog, TR.trackname, TY.typename, D.divisionname,"
        . " TIME_FORMAT(S.duration, '%H:%i') AS duration, SCH.roomid, R.roomname,"
        . " DATE_FORMAT(ADDTIME(?, SCH.starttime), ?) AS starttime,"
        . " DATE_FORMAT(ADDTIME(?, ADDTIME(SCH.starttime, S.duration)), ?) AS endtime"
        . " FROM Sessions S JOIN Tracks TR USING (trackid) JOIN Types TY USING (typeid) JOIN Divisions D USING (divisionid)"
        . " LEFT JOIN Schedule SCH USING (sessionid) LEFT JOIN Rooms R USING (roomid)"
        . " WHERE S.sessionid = ?;";
    $result = mysqli_query_with_prepare_and_exit_on_error($query, "ssssi",
        array($conStartDatim, $timeFormat, $conStartDatim, $timeFormat, $sessionId));
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if (!$row) {
        return null;
    }

    $tagQuery = "SELECT TA.tagname FROM SessionHasTag SHT JOIN Tags TA USING (tagid) WHERE SHT.sessionid = ? ORDER BY TA.tagname;";
    $tagResult = mysqli_query_with_prepare_and_exit_on_error($tagQuery, "i", array($sessionId));
    $tagNames = array();
    while ($tagRow = mysqli_fetch_assoc($tagResult)) {
        $tagNames[] = $tagRow["tagname"];
    }
    mysqli_free_result($tagResult);

    $participantQuery = "SELECT POS.moderator, CD.badgename, P.badgeid,"
        . " COALESCE(P.pubsname, CONCAT(CD.firstname, ' ', CD.lastname)) AS participantname"
        . " FROM ParticipantOnSession POS JOIN Participants P USING (badgeid) JOIN CongoDump CD USING (badgeid)"
        . " WHERE POS.sessionid = ?;";
    $participantResult = mysqli_query_with_prepare_and_exit_on_error($participantQuery, "i", array($sessionId));
    $participants = array();
    while ($participantRow = mysqli_fetch_assoc($participantResult)) {
        $participants[] = array(
            "moderator" => boolval($participantRow["moderator"]),
            "badgename" => $participantRow["badgename"],
            "badgeid" => $participantRow["badgeid"],
            "participantname" => $participantRow["participantname"],
        );
    }
    mysqli_free_result($participantResult);

    return array(
        "sessionid" => intval($row["sessionid"]),
        "title" => $row["title"],
        "progguiddesc" => $row["progguiddesc"],
        "notesforprog" => $row["notesforprog"],
        "trackname" => $row["trackname"],
        "typename" => $row["typename"],
        "divisionname" => $row["divisionname"],
        "duration" => $row["duration"],
        "tagNames" => $tagNames,
        "scheduled" => !is_null($row["roomid"]),
        "roomname" => $row["roomname"],
        "starttime" => $row["starttime"],
        "endtime" => $row["endtime"],
        "participants" => $participants,
    );
}

// Inverse of gridScheduler_timeStringToMinutes(): Schedule.starttime is a TIME measured from the start of the con,
// so hours can legitimately exceed 23 (e.g. "49:30:00" for 1:30 AM on day 3).
function gridScheduler_minutesToTimeString($minutes) {
    return sprintf("%d:%02d:00", intdiv($minutes, 60), $minutes % 60);
}

// Minute-precision replacement for timeDescFromUnits() (data_functions.php), which only understands 30-minute
// units -- used for SessionEditHistory.editdescription.
function gridScheduler_timeDescFromMinutes($minutes) {
    global $con_start_php_timestamp;
    $dateTime = clone $con_start_php_timestamp;
    $dateTime->modify("+$minutes minutes");
    return $dateTime->format("D g:i A");
}

// Executes one INSERT/UPDATE/DELETE as a prepared statement, throwing on failure so the caller's transaction can
// be rolled back. (mysql_cmd_with_prepare() in db_functions.php isn't usable here: on failure it renders a full
// HTML error page rather than an ajax error, and it can't take part in a caller-managed transaction.)
function gridScheduler_execOrThrow($query, $types, $params) {
    global $mysqli;
    $statement = $mysqli->prepare($query);
    if (!$statement) {
        throw new ErrorException("DB prepare statement failed.");
    }
    if (!$statement->bind_param($types, ...$params) || !$statement->execute()) {
        $statement->close();
        throw new ErrorException("DB execute statement failed.");
    }
    $statement->close();
}

function gridScheduler_fetchOneRow($query, $types, $params) {
    $result = mysqli_query_with_prepare_and_error_handling($query, $types, $params, true, true);
    $row = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    return $row ?: null;
}

// Validates the edits array sent by the client for one drag-and-drop gesture. Each edit is one of:
//   ['action' => 'insert',     'sessionid' => n, 'roomid' => n, 'startMinutes' => n]
//   ['action' => 'reschedule', 'sessionid' => n, 'scheduleid' => n, 'roomid' => n, 'startMinutes' => n]
//   ['action' => 'delete',     'sessionid' => n, 'scheduleid' => n]
// Returns the normalized (int-cast) edits, or null if anything is malformed.
function gridScheduler_normalizeEdits($edits) {
    if (!is_array($edits) || count($edits) === 0) {
        return null;
    }
    $normalized = array();
    $sessionIdsSeen = array();
    foreach ($edits as $edit) {
        if (!is_array($edit) || !isset($edit["action"]) || !in_array($edit["action"], array("insert", "reschedule", "delete"), true)) {
            return null;
        }
        $action = $edit["action"];
        $required = array("sessionid");
        if ($action !== "insert") {
            $required[] = "scheduleid";
        }
        if ($action !== "delete") {
            $required[] = "roomid";
            $required[] = "startMinutes";
        }
        $normalizedEdit = array("action" => $action);
        foreach ($required as $field) {
            $value = isset($edit[$field]) ? filter_var($edit[$field], FILTER_VALIDATE_INT) : false;
            if ($value === false || $value < 0) {
                return null;
            }
            $normalizedEdit[$field] = $value;
        }
        // One gesture never touches the same session twice; check_room_sched_conflicts() also keys by sessionid.
        if (isset($sessionIdsSeen[$normalizedEdit["sessionid"]])) {
            return null;
        }
        $sessionIdsSeen[$normalizedEdit["sessionid"]] = true;
        $normalized[] = $normalizedEdit;
    }
    return $normalized;
}

// Confirms the client's view of every session/room an edit touches still matches the database -- another staff
// member may have changed the schedule since this page last refreshed. Returns an explanatory message if not,
// or null if everything is still consistent.
function gridScheduler_findStaleEdit($edits) {
    foreach ($edits as $edit) {
        if ($edit["action"] === "insert") {
            $row = gridScheduler_fetchOneRow("SELECT COUNT(*) AS n FROM Schedule WHERE sessionid = ?;", "i", array($edit["sessionid"]));
            if (intval($row["n"]) > 0) {
                return "Session {$edit["sessionid"]} has already been scheduled by someone else.";
            }
            $row = gridScheduler_fetchOneRow("SELECT sessionid FROM Sessions WHERE sessionid = ?;", "i", array($edit["sessionid"]));
            if (is_null($row)) {
                return "Session {$edit["sessionid"]} no longer exists.";
            }
        } else {
            $row = gridScheduler_fetchOneRow("SELECT sessionid FROM Schedule WHERE scheduleid = ?;", "i", array($edit["scheduleid"]));
            if (is_null($row) || intval($row["sessionid"]) !== $edit["sessionid"]) {
                return "Session {$edit["sessionid"]} was changed by someone else since this page was loaded.";
            }
        }
        if ($edit["action"] !== "delete") {
            $row = gridScheduler_fetchOneRow("SELECT roomid FROM Rooms WHERE roomid = ? AND is_scheduled = 1;", "i", array($edit["roomid"]));
            if (is_null($row)) {
                return "Room {$edit["roomid"]} is not available for scheduling.";
            }
        }
    }
    return null;
}

// Applies one drag-and-drop gesture's worth of schedule edits (e.g. a single reschedule, or a swap's two
// reschedules) atomically. Ports the write half of staffMaintainScheduleSubmit.php's editSchedule(), with two
// deliberate differences:
//  - Participant conflicts (check_room_sched_conflicts(), SubmitMaintainRoom.php, reused as-is) are checked
//    *before* writing: if any are found and $ignoreConflicts is false, nothing is written and the conflicts are
//    returned so staff can confirm. The legacy version always wrote first and only reported conflicts afterward.
//  - Every statement is a prepared statement, and all writes happen in one transaction.
// A reschedule updates the existing Schedule row in place (the legacy version deleted and re-inserted it);
// nothing references scheduleid, and keeping it stable lets the client track the row across the edit.
//
// Returns ['status' => 'applied' | 'conflicts' | 'stale', 'conflictsHtml' => string, 'message' => string,
// 'schedule' => fresh schedule]. The fresh schedule is always included so the client can resynchronize.
function gridScheduler_editSchedule($edits, $ignoreConflicts) {
    global $message, $mysqli;

    $staleMessage = gridScheduler_findStaleEdit($edits);
    if (!is_null($staleMessage)) {
        return array("status" => "stale", "conflictsHtml" => "", "message" => $staleMessage,
            "schedule" => gridScheduler_fetchSchedule());
    }

    $addToScheduleArray = array(); // sessionid => startMinutes, for the conflict checker
    $deleteScheduleIds = array(); // scheduleids whose current slot should be disregarded by the conflict checker
    foreach ($edits as $edit) {
        if ($edit["action"] !== "insert") {
            $deleteScheduleIds[] = $edit["scheduleid"];
        }
        if ($edit["action"] !== "delete") {
            $addToScheduleArray[$edit["sessionid"]] = $edit["startMinutes"];
        }
    }
    // check_room_sched_conflicts() only resets the global $message on some paths; clear it so a stale value can't
    // leak through when it returns early.
    $message = "";
    $noConflicts = check_room_sched_conflicts($deleteScheduleIds, $addToScheduleArray);
    $conflictsHtml = $noConflicts ? "" : $message;
    if (!$noConflicts && !$ignoreConflicts) {
        return array("status" => "conflicts", "conflictsHtml" => $conflictsHtml, "message" => "",
            "schedule" => gridScheduler_fetchSchedule());
    }

    $name = "";
    $email = "";
    get_name_and_email($name, $email);
    $badgeid = $_SESSION['badgeid'];
    $roomNames = array();
    foreach (gridScheduler_fetchLookupList("Rooms", "roomid", "roomname") as $room) {
        $roomNames[$room["id"]] = $room["name"];
    }

    // SessionEditHistory's primary key is (sessionid, timestamp) with one-second resolution, so two quick
    // consecutive edits to the same session would otherwise collide and fail the whole transaction -- keep the
    // most recent edit's row instead.
    $historyQuery = <<<EOD
INSERT INTO SessionEditHistory (sessionid, badgeid, name, email_address, sessioneditcode, statusid, editdescription)
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE sessioneditcode = VALUES(sessioneditcode), statusid = VALUES(statusid),
        editdescription = VALUES(editdescription);
EOD;
    $mysqli->begin_transaction();
    try {
        foreach ($edits as $edit) {
            if ($edit["action"] === "delete") {
                gridScheduler_execOrThrow("DELETE FROM Schedule WHERE scheduleid = ?;", "i", array($edit["scheduleid"]));
                // status 2 is "vetted"
                gridScheduler_execOrThrow("UPDATE Sessions SET statusid = 2 WHERE sessionid = ?;", "i", array($edit["sessionid"]));
                // session edit code 5 is "Remove from schedule"
                gridScheduler_execOrThrow($historyQuery, "isssiis",
                    array($edit["sessionid"], $badgeid, $name, $email, 5, 2, null));
                continue;
            }
            $startTime = gridScheduler_minutesToTimeString($edit["startMinutes"]);
            if ($edit["action"] === "insert") {
                gridScheduler_execOrThrow("INSERT INTO Schedule (sessionid, roomid, starttime) VALUES (?, ?, ?);", "iis",
                    array($edit["sessionid"], $edit["roomid"], $startTime));
                $editCode = 4; // "Add to schedule"
            } else {
                gridScheduler_execOrThrow("UPDATE Schedule SET roomid = ?, starttime = ? WHERE scheduleid = ?;", "isi",
                    array($edit["roomid"], $startTime, $edit["scheduleid"]));
                $editCode = 7; // "Rescheduled"
            }
            // status 3 is "scheduled"
            gridScheduler_execOrThrow("UPDATE Sessions SET statusid = 3 WHERE sessionid = ?;", "i", array($edit["sessionid"]));
            $description = gridScheduler_timeDescFromMinutes($edit["startMinutes"]) . " in " . $roomNames[$edit["roomid"]];
            gridScheduler_execOrThrow($historyQuery, "isssiis",
                array($edit["sessionid"], $badgeid, $name, $email, $editCode, 3, $description));
        }
        $mysqli->commit();
    } catch (Exception $e) {
        $errorMessage = log_mysqli_error_new("editSchedule", $e->getMessage()); // before rollback() clears $mysqli->error
        $mysqli->rollback();
        RenderErrorAjax($errorMessage);
        exit();
    }

    return array("status" => "applied", "conflictsHtml" => $conflictsHtml, "message" => "",
        "schedule" => gridScheduler_fetchSchedule());
}
?>
