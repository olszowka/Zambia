<?php
//	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.

require_once('StaffCommonCode.php');
require_once('surveyFilterBuild.php');

function invite_participant() {
    global $linki;

    $partbadgeid = getString("selpart");
    if ($partbadgeid !== NULL)
        $partbadgeid = mysqli_real_escape_string($linki, $partbadgeid);
    else
        $partbadgeid = '';
    $sessionid = getInt("selsess", 0);

    if (($partbadgeid == '') || ($sessionid == 0)) {
        $message = "<p class=\"alert alert-error\">Database not updated. Select a participant and a session.</p>";
        $alerttype = "warning";
    } else {
        $query = "INSERT INTO ParticipantSessionInterest SET badgeid='$partbadgeid', ";
        $query .= "sessionid=$sessionid;";
        $result = mysqli_query($linki, $query);
        if ($result) {
            $message =  "<p>Database successfully updated.</p>";
        } elseif (mysqli_errno($linki) == 1062) {
            $message =  "<p>Database not updated. That participant was already invited to that session.</p>";
            $alerttype = "warning";
        } else {
            $message = $query . "<p>Database not updated.</p>";
            $alerttype = "danger";
        }
    }
    $json_return = array();
    $json_return["message"] = $message;
    $json_return["alerttype"] = $alerttype;
    echo json_encode($json_return) . "\n";
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "" || !isLoggedIn() || !may_I("Staff")) {
    exit();
}

switch ($ajax_request_action) {
    case "invite":
        invite_participant();
        break;
    default:
        exit();
}

?>