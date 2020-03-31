<?php
// Copyright (c) 2015-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// 2020-03-28 PBO: This is code from an incomplete or broken commit which does not function--it is never called by anything
require_once('StaffCommonCode.php');
// Start here.  Should be AJAX requests only
if (!$ajax_request_action=$_POST["ajax_request_action"])
	exit();
switch ($ajax_request_action) {
	case "autosave":
		autosave();
		break;
	default:
		exit();
	}
?>
