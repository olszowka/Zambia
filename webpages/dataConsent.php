<?php
// Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
//
// This file is required directly from ParticipantHeader.php or StaffHeader.php if consent is required and not given
global $message, $message_error, $message2, $participant_array, $title;
$title = "Data Retention Consent";
// Now that title is set, get common text
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
$xmlDoc = new DomDocument("1.0", "UTF-8");
$emptyDoc = $xmlDoc->createElement("doc");
$xmlDoc->appendChild($emptyDoc);
$xmlDoc = appendCustomTextArrayToXML($xmlDoc);
$participant_array = retrieveFullParticipant($_SESSION['badgeid']);
if ($message_error != "") {
    echo "<p class=\"alert alert-danger\">$message_error</p>\n";
}
if ($message != "") {
    echo "<p class=\"alert alert-success\">$message</p>\n";
}
$paramArray = array();
$paramArray["conName"] = CON_NAME;
$paramArray["firstName"] = $participant_array["firstname"];
$paramArray["lastName"] = $participant_array["lastname"];
RenderXSLT('dataConsent.xsl', $paramArray, $xmlDoc);
participant_footer();
