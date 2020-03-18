<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('CommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
    if (!(may_I("Staff"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>
