<?php
// Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
global $SessionSearchParameters, $message_error, $message, $title;
$title = "Staff - Search Previous Sessions";
require_once('StaffCommonCode.php');
require_once('StaffSearchPreviousSessions_FNC.php');
staff_header($title);
ProcessImportSessions();
SetSessionSearchParameterDefaults();
RenderSearchPreviousSessions();
staff_footer();
?>