<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Staff Overview";
require_once('StaffCommonCode.php');
staff_header($title, 'bs5');

if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
$xmlDoc = new DomDocument("1.0", "UTF-8");
$emptyDoc = $xmlDoc->createElement("doc");
$xmlDoc->appendChild($emptyDoc);
$xmlDoc = appendCustomTextArrayToXML($xmlDoc);
$paramArray = array();
$paramArray["conNumDays"] = CON_NUM_DAYS;
$paramArray["conName"] = CON_NAME;
$paramArray["conStartDate"] = getConStartDate();
$paramArray["conEndDate"] = getConEndDate();
RenderXSLT('StaffOverview.xsl', $paramArray, $xmlDoc);
staff_footer();
