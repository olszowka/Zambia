<?php
// $Header$
//	Copyright (c) 2011-2017 The Zambia Group. All rights reserved. See copyright document for more details.
require_once('PartCommonCode.php');
// 2017-Jan-23 PBO: unlock participant function not working because biolocked by field not in schema
//unlock_participant('');            // unlock any records locked by this user
session_destroy();                 // Destroy session data
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
require ('login.php');
//$title="Logged Out";
//participant_header($title);
//participant_footer();
?>

