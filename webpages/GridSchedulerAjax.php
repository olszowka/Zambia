<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check for staff privileges
require_once('gridScheduler_functions.php');

function getSchedule() {
    echo json_encode(gridScheduler_buildData());
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
    default:
        RenderErrorAjax("Internal error.");
}
exit();
?>
