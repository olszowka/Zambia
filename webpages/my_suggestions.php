<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "My Suggestions";
require('PartCommonCode.php'); // set $badgeid from session
require_once('renderMySuggestions.php');
$query = <<<EOB
SELECT
        paneltopics, otherideas, suggestedguests
    FROM
        ParticipantSuggestions
    WHERE
        badgeid = "$badgeid";
EOB;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // should have exited already
}
$rows = mysqli_num_rows($result);
if ($rows > 1) {
    $message = $query . "<br>Multiple rows returned from database where one expected. Unable to continue.";
    RenderError($message);
    exit();
}
if ($rows == 0) {
    $paneltopics = "";
    $otherideas = "";
    $suggestedguests = "";
} else {
    list($paneltopics, $otherideas, $suggestedguests) = mysqli_fetch_array($result, MYSQLI_NUM);
}
$error = false;
$message = "";
renderMySuggestions($title, $error, $message, $paneltopics, $otherideas, $suggestedguests);
?>
