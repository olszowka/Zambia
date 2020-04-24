<?php
//	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. Test change for key
function commonHeader($headerVersion, $isLoggedIn, $noUserRequired, $loginPageStatus, $headerErrorMessage = "") {
    global $header_rendered;
    if ($isLoggedIn && $loginPageStatus == 'Normal' && !may_I("Participant") && !may_I("Staff")) {
        $loginPageStatus = 'No_Permission';
    }
    $paramArray = array();
    $paramArray["header_version"] = $headerVersion;
    $paramArray["logged_in"] = $isLoggedIn;
    $paramArray["login_page_status"] = $loginPageStatus;
    $paramArray["CON_NAME"] = CON_NAME;
    $paramArray["badgename"] = isset($_SESSION['badgename']) ? $_SESSION['badgename'] : '';
    $paramArray["USER_ID_PROMPT"] = USER_ID_PROMPT;
    $paramArray["header_error_message"] = $headerErrorMessage;
    $paramArray["no_user_required"] = $noUserRequired;
    RenderXSLT('GlobalHeader.xsl', $paramArray);
    $header_rendered = true;
}
