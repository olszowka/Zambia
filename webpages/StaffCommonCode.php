<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('CommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    $badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
    if (!(may_I("Staff"))) {
        global $headerErrorMessage, $returnAjaxErrors;
        $headerErrorMessage = "You are not authorized to access this page or your login session has expired.";
        if (isset($returnAjaxErrors) && $returnAjaxErrors) {
            RenderErrorAjax($headerErrorMessage);
        } else {
            require ('login.php');
        }
        exit();
        };
?>
