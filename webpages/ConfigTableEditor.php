<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03
global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Edit Configuration Tables";
require_once('StaffCommonCode.php');

$paramArray = array();
staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
// Start of display portion

	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];

	$xmlDoc = GeneratePermissionSetXML();
} else {
    $paramArray["UpdateMessage"] = "Administrator role required";
	$xmlDoc = null;
}
	// following line for debugging only
echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xmlDoc->saveXML(), "i"));
RenderXSLT('ConfigTableEditor.xsl', $paramArray, $xmlDoc);

staff_footer();
?>