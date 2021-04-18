<?php
// Copyright (c) 2005-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "My Suggestions";
require('PartCommonCode.php'); // sets $badgeid from $SESSION among other things
require_once('renderMySuggestions.php');
$paneltopics = getString("paneltopics");
$otherideas = getString("otherideas");
$suggestedguests = getString("suggestedguests");
if (!may_I('my_suggestions_write')) {
    $message = "Currently, you do not have write access to this page.\n";
    $error = true;
    renderMySuggestions($title, $error, $message, $paneltopics, $otherideas, $suggestedguests);
    exit();
}
if ($message = validate_suggestions($paneltopics, $otherideas, $suggestedguests)) {
    $message .= "<br>Database not updated.\n";
    $error = true;
    renderMySuggestions($title, $error, $message, $paneltopics, $otherideas, $suggestedguests);
    exit();
}
$message = "Database updated successfully.";
$error = false;
$paneltopicsE = mysqli_real_escape_string($linki, $paneltopics);
$otherideasE = mysqli_real_escape_string($linki, $otherideas);
$suggestedguestsE = mysqli_real_escape_string($linki, $suggestedguests);
$query = <<<EOD
INSERT INTO ParticipantSuggestions (badgeid, paneltopics, otherideas, suggestedguests)
    VALUES ("$badgeid", "$paneltopicsE", "$otherideasE", "$suggestedguestsE") 
    ON DUPLICATE KEY UPDATE 
        badgeid = VALUES(badgeid), paneltopics=VALUES(paneltopics),
        otherideas = VALUES(otherideas), suggestedguests=VALUES(suggestedguests);
EOD;
if (!mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}
renderMySuggestions($title, $error, $message, $paneltopics, $otherideas, $suggestedguests);
?>
