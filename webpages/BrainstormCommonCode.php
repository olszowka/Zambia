<?php
//  Copyright (c) 2011-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('CommonCode.php');
    require_once('error_functions.php');
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    global $badgeid;
    $badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
    if (!(may_I("BrainstormSubmit") || may_I("BS_sear_sess") || may_I("public_login"))) {
        $message = "You are not authorized to access this page.";
        require('StaffPage.php');
        exit();
    };
?>
