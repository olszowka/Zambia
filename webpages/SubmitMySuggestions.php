<?php
// Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "My Suggestions";
require('PartCommonCode.php'); // sets $badgeid from $SESSION among other things
$paneltopics = if_null_default(getString("paneltopics"), '');
$otherideas = if_null_default(getString("otherideas"), '');
$suggestedguests = if_null_default(getString("suggestedguests"), '');
if (!may_I('my_suggestions_write')) {
    $message = "Currently, you do not have write access to this page.\n";
    $error = true;
} elseif ($message = validate_suggestions($paneltopics, $otherideas, $suggestedguests)) {
    $message .= "<br>Database not updated.\n";
    $error = true;
} else {
    $error = false;
    $query = <<<EOD
INSERT INTO ParticipantSuggestions (badgeid, paneltopics, otherideas, suggestedguests)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        badgeid = VALUES(badgeid), paneltopics=VALUES(paneltopics),
        otherideas = VALUES(otherideas), suggestedguests=VALUES(suggestedguests);
EOD;
    $queryParamsArr = array($badgeid, $paneltopics, $otherideas, $suggestedguests);
    mysqli_query_with_prepare_and_exit_on_error($query, 'ssss', $queryParamsArr);
    // Just assume it exited on error correctly
    $message = "Database updated successfully.";
}

$resultXML = ObjecttoXML('mysuggestions', array(array(
    'paneltopics' => $paneltopics,
    'otherideas' => $otherideas,
    'suggestedguests' => $suggestedguests,
)));
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
$resultXML = appendCustomTextArrayToXML($resultXML);

participant_header($title, false, 'Normal', 'bs5');
$paramArray = array();
$paramArray['readonly'] = may_I('my_suggestions_write') ? '0' : '1';
$paramArray['error'] = $error ? '1' : '0';
$paramArray['message'] = $message;
RenderXSLT('my_suggestions.xsl', $paramArray, $resultXML);
participant_footer();
?>
