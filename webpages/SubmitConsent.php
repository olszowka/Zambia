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
if ($participant_array = retrieveFullParticipant($badgeid)) {
    require('doLogin.php');
    exit();
} else {
    $message = $message2 . "<br>Failure to re-retrieve data for Participant.";
    RenderError($message);
    exit();
}
?>