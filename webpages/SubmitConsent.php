<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
require('PartCommonCode.php');
$title = "Submit Consent";
$consent = getString('consent');
// If consent is not granted, log out the user
if ($consent == 0) {
   require_once('logout.php');
   exit();
}

// update consent
$query_param_arr = array($badgeid);
$message2 = "";

$query = "UPDATE Participants SET data_retention = 1 WHERE badgeid = ?";
if (!mysql_cmd_with_prepare($query, 's', $query_param_arr)) {
    $message = $query . "<br>Error updating database.  Database not updated.";
    RenderError($message);
    exit();
}
$message = "Database updated successfully.";
$message2 = "";
$_SESSION['data_consent'] = 1;
if (may_I('Staff')) {
    require('StaffPage.php');
} elseif (may_I('Participant')) {
    if (!$participant_array = retrieveFullParticipant($badgeid)) {
        $message_error = $message2 . "<br />Error retrieving data from DB.  No further execution possible.";
        RenderError($message_error);
    } else {
        require('renderWelcome.php');
    }
} elseif (may_I('public_login')) {
    require('renderBrainstormWelcome.php');
} else {
    unset($_SESSION['badgeid']);
    $message_error = "There is a problem with your $userIdPrompt's permission configuration:  It doesn't have ";
    $message_error .= "permission to access any welcome page.  Please contact Zambia staff.";
    RenderError($message_error);
}
exit();
?>