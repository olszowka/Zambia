<?php
//	Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $pageBootstrapVersion;
$pageBootstrapVersion = 'bs5';
require_once('PartCommonCode.php');
session_destroy();                 // Destroy session data
$_SESSION = array();               // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
participant_header("Logout", false, 'Logout', 'bs5');
participant_footer();
?>

