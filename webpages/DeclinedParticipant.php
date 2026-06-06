<?php
// Created by Peter Olszowka on 2022-10-01;
// Copyright (c) 2022-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$title = "Declined to Invite";
require_once('PartCommonCode.php');
if (!populateCustomTextArray()) { // title changed above, reload custom text with the proper page title
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
participant_header($title, false, 'No_Menu', 'bs5');
$xmlDoc = new DomDocument("1.0", "UTF-8");
$emptyDoc = $xmlDoc->createElement("doc");
$xmlDoc->appendChild($emptyDoc);
$xmlDoc = appendCustomTextArrayToXML($xmlDoc);
$paramArray["conName"] = CON_NAME;
$paramArray["programEmail"] = PROGRAM_EMAIL;
RenderXSLT('DeclinedParticipant.xsl', $paramArray, $xmlDoc);
echo "</div><!-- end whole page div --></body></html>"; // in lieu of participant footer
