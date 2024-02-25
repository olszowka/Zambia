<?php
// Copyright (c) 2020-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
function commonHeader($headerVersion, $topSectionBehavior, $bootstrapVersion = 'bs2', $headerErrorMessage='') {
    /**
     * Top section behavior
     * LOGIN:
     *      Login form, no message
     * SESSION_EXPIRED:
     *      Login form, session expired message (error)
     * LOGOUT:
     *      Login form, logout success message (success)
     * PASSWORD_RESET_COMPLETE:
     *      Login form, password changed message (success)
     * NO_USER:
     *      No login form, just title and logo
     * NORMAL:
     *      No login form, welcome message with logout button
     */
    global $header_rendered;
    $paramArray = array();
    $paramArray["header_version"] = $headerVersion;
    $paramArray["top_section_behavior"] = $topSectionBehavior;
    $paramArray["CON_NAME"] = CON_NAME;
    $paramArray["badgename"] = isset($_SESSION['badgename']) ? $_SESSION['badgename'] : '';
    $paramArray["USER_ID_PROMPT"] = USER_ID_PROMPT;
    $paramArray["RESET_PASSWORD_SELF"] = RESET_PASSWORD_SELF;
    $paramArray["header_error_message"] = $headerErrorMessage;
    switch ($bootstrapVersion) {
        case 'bs5':
            RenderXSLT('GlobalHeader_BS5.xsl', $paramArray);
            break;
        case 'bs4':
            RenderXSLT('GlobalHeader_BS4.xsl', $paramArray);
            break;
        case 'bs2':
        default:
            RenderXSLT('GlobalHeader.xsl', $paramArray);
            break;
    }
    $header_rendered = true;
}
