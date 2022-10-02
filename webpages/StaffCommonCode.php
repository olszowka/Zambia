<?php
//	Copyright (c) 2011-2022 Peter Olszowka. All rights reserved. See copyright document for more details.
    global $headerErrorMessage, $returnAjaxErrors, $title;
    require_once('CommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
    if (!(may_I("Staff"))) {
        $headerErrorMessage = "You are not authorized to access this page or your login session has expired.";
        if (isset($returnAjaxErrors) && $returnAjaxErrors) {
            RenderErrorAjax($headerErrorMessage);
        } else {
            staff_header($title);
        }
        exit();
        };
?>
