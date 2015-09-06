<?php
require_once('PartCommonCode.php');
unlock_participant('');            // unlock any records locked by this user
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
session_destroy();                 // Destroy session data

$title="Logged Out";
participant_header($title);

?>

<?php participant_footer() ?>
