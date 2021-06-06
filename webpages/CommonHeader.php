<?php
require_once('login_functions.php');
//	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function commonHeader($headerVersion, $isLoggedIn, $noUserRequired, $loginPageStatus, $headerErrorMessage = "", $bootstrap4 = false) {
    global $header_rendered;
    if ($isLoggedIn && ($loginPageStatus == 'Normal' || $loginPageStatus == 'Consent') && !may_I("Participant") && !may_I("Staff")) {
        $loginPageStatus = 'No_Permission';
    }
    $paramArray = array();
    $paramArray["header_version"] = $headerVersion;
    $paramArray["logged_in"] = $isLoggedIn;
    $paramArray["login_page_status"] = $loginPageStatus;
    $paramArray["CON_NAME"] = CON_NAME;
    $paramArray["badgename"] = isset($_SESSION['badgename']) ? $_SESSION['badgename'] : '';
    if (defined('CON_HEADER_IMG') && CON_HEADER_IMG !== "") {
        $paramArray["headerimg"] = CON_HEADER_IMG;
    }
    if (defined('CON_HEADER_IMG_ALT') && CON_HEADER_IMG_ALT !== "") {
        $paramArray["headerimgalt"] = CON_HEADER_IMG_ALT;
    }
    $paramArray["USER_ID_PROMPT"] = get_user_id_prompt();
    $paramArray["header_error_message"] = $headerErrorMessage;
    $paramArray["no_user_required"] = $noUserRequired;
    $paramArray["RESET_PASSWORD_SELF"] = RESET_PASSWORD_SELF;
    if ($bootstrap4) {
        RenderXSLT('GlobalHeader_BS4.xsl', $paramArray);
    } else {
        RenderXSLT('GlobalHeader.xsl', $paramArray);
    }
    $header_rendered = true;
}
