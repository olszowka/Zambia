<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check for staff privileges
require_once('gridScheduler_functions.php');
require_once('SubmitMaintainRoom.php'); // check_room_sched_conflicts()

// Just the current schedule (not the rest of the bootstrap payload, whose rooms/lookups don't change while the
// page is open) -- the client refreshes with this whenever the set of displayed rooms changes.
function getSchedule() {
    echo json_encode(array("schedule" => gridScheduler_fetchSchedule()));
    exit();
}

function searchSessions() {
    echo json_encode(array("sessions" => gridScheduler_searchSessions()));
    exit();
}

function getSessionInfo() {
    $sessionId = getInt("sessionid", null);
    if (is_null($sessionId)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $info = gridScheduler_getSessionInfo($sessionId);
    if (is_null($info)) {
        RenderErrorAjax("Session not found.");
        exit();
    }
    echo json_encode($info);
    exit();
}

// "edits" is a JSON-encoded array (one drag-and-drop gesture's worth -- see gridScheduler_normalizeEdits()) rather
// than PHP's nested name[i][field] form encoding, since each edit has a different shape.
function editSchedule() {
    // Read directly rather than via getString(), which runs stripslashes() and would corrupt any JSON escapes.
    $edits = gridScheduler_normalizeEdits(json_decode(isset($_POST["edits"]) ? $_POST["edits"] : "", true));
    if (is_null($edits)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    echo json_encode(gridScheduler_editSchedule($edits, getInt("ignoreConflicts", 0) === 1));
    exit();
}

if (!$ajax_request_action = $_POST["ajax_request_action"])
    exit();
switch ($ajax_request_action) {
    case "getSchedule":
        getSchedule();
        break;
    case "searchSessions":
        searchSessions();
        break;
    case "getSessionInfo":
        getSessionInfo();
        break;
    case "editSchedule":
        editSchedule();
        break;
    default:
        RenderErrorAjax("Internal error.");
}
exit();
?>
