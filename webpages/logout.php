<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('PartCommonCode.php');
session_destroy();                 // Destroy session data
$_SESSION = array();               // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
participant_header("Logout", false, 'Logout', true);
participant_footer();
?>

