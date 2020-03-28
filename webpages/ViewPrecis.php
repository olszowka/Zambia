<?php
// Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('StaffCommonCode.php');
require_once('retrieve.php');
$title = "Precis";
$showlinks = getInt("showlinks", 0);
$_SESSION['return_to_page'] = "ViewPrecis.php?showlinks=$showlinks";
$showlinks = ($showlinks === 1);
$sessionSearchArray = array();
$sessionSearchArray['statusidList'] = get_idlist_from_db("SessionStatuses", "statusid", "statusname",
    "'Brainstorm','Edit Me','Vetted'");
if ($result = retrieveSessions($sessionSearchArray)) {
    staff_header($title);
    RenderPrecis($result, $showlinks);
    staff_footer();
    exit();
}
?>
