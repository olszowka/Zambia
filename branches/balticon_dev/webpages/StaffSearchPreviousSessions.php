<?php
global $SessionSearchParameters, $message_error,$message;
$title="Staff - Search Previous Sessions";
require_once('StaffCommonCode.php');
require_once('StaffSearchPreviousSessions_FNC.php');
staff_header($title);
SetSessionSearchParameterDefaults();
$message="";
$message_error="";
RenderSearchPreviousSessions();
staff_footer();
?>
