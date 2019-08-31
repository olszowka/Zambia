<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
//This file should be requested from post on "add" form
require('PartCommonCode.php'); // initialize db; check login; set $badgeid
require('PartPanelInterests_FNC.php');
require('PartPanelInterests_Render.php');
global $session_interests, $session_interest_index, $title, $message;
$title = "Panel Interests";
$error = false;
if (!may_I('my_panel_interests')) {
    $message = "You do not currently have permission to view this page.<br />\n";
    RenderError($message);
    exit();
}
if (!isset($_POST["add"])) { //That should be "submit" button on "add" form.
    $message = "This page was reached from an unexpected place.<br />\n";
    RenderError($message);
    exit();
}
if (!isset($_POST["addsessionid"])) { //That should be "sessionid" box on "add" form.
    $message = "Sessionid value not found.<br />\n";
    RenderError($message);
    exit();
}
$addsessionid = getInt("addsessionid", 0);
if (!validate_add_session_interest($addsessionid, $badgeid, ParticipantAddSession)) {
    $error = true;
    $message_error = $message;
    $message = "";
} else {
    $query = "INSERT INTO ParticipantSessionInterest set badgeid='$badgeid', sessionid=$addsessionid";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $message = "Database updated successfully.";
    $message_error = "";
}
// $add
// Get the participant's interest data -- use global $session_interests
$session_interest_count = get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
// Get title, etc. of such data -- use global $session_interests
get_si_session_info_from_db($session_interest_count); // Will render its own errors
    render_session_interests($session_interest_count, $message, $message_error, false, false); // includes footer
?>
