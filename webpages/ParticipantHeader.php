<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_PARTICIPANT;

function participant_header($title, $noUserRequired = false, $pageHeaderFamily = 'Normal', $bootstrapVersion = 'bs5') {
    // $noUserRequired is true if user not required to be logged in to access this page
    // $pageHeaderFamily is "Login", "Logout", "No_Menu", "PASSWORD_RESET_COMPLETE", "Consent", "Normal"
    //      "Login":
    //          don't show menu; show login form in header
    //          login page and password reset pages
    //      "Logout":
    //          don't show menu; show logout confirmation in header
    //          logout page only
    //      "No_Menu":
    //          don't show menu; show welcome if user required and populated; show page
    //          declined participant pages
    //      "PASSWORD_RESET_COMPLETE":
    //          don't show menu; show login form in header (just like login, but with password change message)
    //      override page to gather user data retention consent is "Consent"
    //          don't show menu; show dataConsent page; show normal header (with welcome)
    //      all other pages should be "Normal"
    //          show menu; show page; show normal header (with welcome)
    global $headerErrorMessage;
    $displayDataConsentPage = false;
    $isLoggedIn = isLoggedIn();
    if ($isLoggedIn && REQUIRE_CONSENT && (empty($_SESSION['data_consent']) || $_SESSION['data_consent'] !== 1)) {
        $title = "Data Retention Consent";
        $pageHeaderFamily = 'No_Menu';
        $bootstrapVersion = 'bs5'; // dataConsent.xsl markup is bs5-specific
        $displayDataConsentPage = true;
    }
    switch ($pageHeaderFamily) {
        case 'Login':
            $topSectionBehavior = 'LOGIN';
            break;
        case 'Logout':
            $topSectionBehavior = 'LOGOUT';
            break;
        case 'Normal':
        case 'No_Menu':
            if ($isLoggedIn) {
                $topSectionBehavior = 'NORMAL';
            } elseif ($noUserRequired) {
                $topSectionBehavior = 'NO_USER';
            } else {
                $topSectionBehavior = 'SESSION_EXPIRED';
            }
            break;
        case 'PASSWORD_RESET_COMPLETE':
            $topSectionBehavior = 'PASSWORD_RESET_COMPLETE';
            break;
    }
    html_header($title, $bootstrapVersion);
    echo "<body>\n";
    echo "<div class=\"container-fluid\">\n";
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
    commonHeader('Participant', $topSectionBehavior, $bootstrapVersion, $headerErrorMessage);
    // below: authenticated and authorized to see a menu
    if ($isLoggedIn && $pageHeaderFamily === 'Normal' &&
        (may_I("Participant") || may_I("Staff"))) {
    // check if survey is defined to set Survey Menu item in paramArray
        if (!isset($_SESSION['survey_exists'])) {
            $_SESSION['survey_exists'] = survey_programmed();
        }
        $paramArray = array();
        $paramArray["title"] = $title;
        $paramArray["survey"] = $_SESSION['survey_exists'];
        $paramArray["PARTICIPANT_PHOTOS"] = PARTICIPANT_PHOTOS === TRUE ? 1 : 0;
        $filename = $bootstrapVersion == 'bs4' ? 'ParticipantMenu_BS4.xsl' : 'ParticipantMenu_BS5.xsl';
        RenderXSLT($filename, $paramArray, GeneratePermissionSetXML());
    } else { // couldn't show menu
        if ($displayDataConsentPage) {
            require('dataConsent.php');
            exit();
        } elseif (!$noUserRequired && !$isLoggedIn) { // not authenticated and authorized to see a menu
            participant_footer();
            exit();
        }
    }
}
?>
