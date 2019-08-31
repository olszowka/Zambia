<?php
//	Created 2017-Jan-23 by Peter Olszowka
//	Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('BrainstormCommonCode.php');
session_destroy();                 // Destroy session data
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
require ('login.php');
?>

