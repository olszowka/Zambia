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

if (!$ajax_request_action = $_POST["ajax_request_action"])
    exit();
switch ($ajax_request_action) {
    case "getSchedule":
        getSchedule();
        break;
    default:
        RenderErrorAjax("Internal error.");
}
exit();
?>
