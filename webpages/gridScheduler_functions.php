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
    return $schedule;
}

// Returns the current rooms/schedule state as a plain array, ready for json_encode() -- used both for
// the initial page bootstrap and for ajax refreshes, so the two stay in sync automatically.
function gridScheduler_buildData() {
    $data = array();
    $data["conStartDateTime"] = str_replace(" ", "T", CON_START_DATIM);
    $data["conNumDays"] = CON_NUM_DAYS;
    // Grid-line resolution: controls the empty/partially-empty schedule's row lines (derived from
    // STANDARD_BLOCK_LENGTH today; a later phase replaces this with proper GRID_*-config derivation).
    // This is one of three distinct, similarly-named-but-different resolutions in the grid scheduler --
    // see the "Time resolutions" section of the rewrite plan. Do not conflate with snap resolution (a
    // page-level Snap Mode control in the React app) or display resolution (fixed at
    // 1 minute, a non-configurable constant on the client -- see DISPLAY_RESOLUTION_MINUTES in
    // react-apps/GridScheduler/src/components/Grid.tsx).
    $data["gridLineResolutionMinutes"] = 30; // TODO: replace with GRID_TIME_RESOLUTION_MINUTES in a later phase
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

    $data["schedule"] = gridScheduler_fetchSchedule();

    // Lookup lists for the Sessions tab's search filters.
    $data["tracks"] = gridScheduler_fetchLookupList("Tracks", "trackid", "trackname");
    $data["tags"] = gridScheduler_fetchLookupList("Tags", "tagid", "tagname");
    $data["types"] = gridScheduler_fetchLookupList("Types", "typeid", "typename");
    $data["divisions"] = gridScheduler_fetchLookupList("Divisions", "divisionid", "divisionname");

    return $data;
}

// Ports webpages/staffMaintainScheduleSubmit.php's retrieveSessions() to JSON output with bound
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

    $query = "SELECT S.sessionid, S.title, TR.trackname, TY.typename, D.divisionname,"
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
            "divisionname" => $row["divisionname"],
            "durationMinutes" => intval($row["durationMinutes"]),
        );
    }
    mysqli_free_result($result);
    return $sessions;
}

// Ports webpages/staffMaintainScheduleSubmit.php's retrieveSessionInfo() to JSON output with bound
// parameters -- the legacy version reads $_POST["sessionid"] straight into the query with no int-cast at
// all, a real (if login-gated) SQL injection hole this rewrite does not carry forward. Returns null if no
// such session exists.
function gridScheduler_getSessionInfo($sessionId) {
    $conStartDatim = CON_START_DATIM;
    $query = "SELECT S.sessionid, S.title, S.progguiddesc, S.notesforprog, TR.trackname, TY.typename, D.divisionname,"
        . " TIME_FORMAT(S.duration, '%H:%i') AS duration, SCH.roomid, R.roomname,"
        . " DATE_FORMAT(ADDTIME(?, SCH.starttime), '%a %l:%i %p') AS starttime,"
        . " DATE_FORMAT(ADDTIME(?, ADDTIME(SCH.starttime, S.duration)), '%a %l:%i %p') AS endtime"
        . " FROM Sessions S JOIN Tracks TR USING (trackid) JOIN Types TY USING (typeid) JOIN Divisions D USING (divisionid)"
        . " LEFT JOIN Schedule SCH USING (sessionid) LEFT JOIN Rooms R USING (roomid)"
        . " WHERE S.sessionid = ?;";
    $result = mysqli_query_with_prepare_and_exit_on_error($query, "ssi", array($conStartDatim, $conStartDatim, $sessionId));
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
