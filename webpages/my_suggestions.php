<?php
// Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "My Suggestions";
require('PartCommonCode.php'); // set $badgeid from session

$querySQLArr = array();
$queryParamTypesArr = array();
$queryParamsArr = array();

$querySQLArr['mysuggestions'] = <<<EOD
SELECT
        paneltopics, otherideas, suggestedguests
    FROM
        ParticipantSuggestions
    WHERE
        badgeid = ?;
EOD;
$queryParamTypesArr['mysuggestions'] = 's';
$queryParamsArr['mysuggestions'] = array($badgeid);

if (!$resultXML = mysql_prepare_query_XML($querySQLArr, $queryParamTypesArr, $queryParamsArr)) {
    exit(); // Should have exited already
}
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
$resultXML = appendCustomTextArrayToXML($resultXML);

participant_header($title, false, 'Normal', 'bs5');
$paramArray = array();
$paramArray['readonly'] = may_I('my_suggestions_write') ? '0' : '1';
RenderXSLT('my_suggestions.xsl', $paramArray, $resultXML);
participant_footer();
?>
