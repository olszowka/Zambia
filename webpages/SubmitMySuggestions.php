<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "My Suggestions";
require('PartCommonCode.php'); // sets $badgeid from $SESSION among other things
require_once('renderMySuggestions.php');
$paneltopics = stripslashes($_POST["paneltopics"]);
$otherideas = stripslashes($_POST["$otherideas"]);
$suggestedguests = stripslashes($_POST["$suggestedguests"]);
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
$paneltopicsE = mysqli_real_escape_string($paneltopics, $linki);
$otherideasE = mysqli_real_escape_string($otherideas, $linki);
$suggestedguestsE = mysqli_real_escape_string($suggestedguests, $linki);
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
