<?php
//	Copyright (c) 2011-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_PARTICIPANT;

function participant_header($title, $noUserRequired = false, $pageHeaderFamily = 'Normal', $bootstrap4 = false) {
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
        $bootstrap4 = true;
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
                $bootstrap4 = false;
            }
            break;
        case 'PASSWORD_RESET_COMPLETE':
            $topSectionBehavior = 'PASSWORD_RESET_COMPLETE';
            break;
    }
    html_header($title, $bootstrap4);
    if ($bootstrap4) {
        echo "<body class=\"bs4\">\n";
    } else {
        echo "<body>\n";
    }
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
    commonHeader('Participant', $topSectionBehavior, $bootstrap4, $headerErrorMessage);
    // below: authenticated and authorized to see a menu
    if ($isLoggedIn && $pageHeaderFamily === 'Normal' &&
        (may_I("Participant") || may_I("Staff"))) {
    // check if survey is defined to set Survey Menu item in paramArray
        if (!isset($_SESSION['survey_exists'])) {
            $_SESSION['survey_exists'] = survey_programmed();
        }
        if ($bootstrap4) {
            $paramArray = array();
            $paramArray["title"] = $title;
            $paramArray["survey"] = $_SESSION['survey_exists'];
            $paramArray["PARTICIPANT_PHOTOS"] = PARTICIPANT_PHOTOS === TRUE ? 1 : 0;
            RenderXSLT('ParticipantMenu_BS4.xsl', $paramArray, GeneratePermissionSetXML());
        } else {
?>
        <nav id="participantNav" class="navbar navbar-inverse">
            <div class="navbar-inner">
                <div class="container" style="width: auto;">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php if (isset($_SERVER['PATH_INFO'])) echo $_SERVER['PATH_INFO'] ?>"><?php echo $title ?></a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            <li><a href="welcome.php">Overview</a></li>
                            <li><a href="my_contact.php">Profile</a></li>
                            <?php
                            makeMenuItem("Photo", (PARTICIPANT_PHOTOS === TRUE && may_I('photos')), "my_photo.php", false);
                            makeMenuItem("Survey", ($_SESSION['survey_exists'] && may_I('survey')), "PartSurvey.php", false);
                            makeMenuItem("Availability", may_I('my_availability'),"my_sched_constr.php",false);
                            makeMenuItem("General Interests", may_I('general_interests'),"my_interests.php",false);
                            makeMenuItem("Search Sessions", may_I('search_panels'),"PartSearchSessions.php", false);
                            makeMenuItem("Session Interests", may_I('my_panel_interests'),"PartPanelInterests.php",false);
                            makeMenuItem("My Schedule", may_I('my_schedule'),"MySchedule.php",false); ?>
                            <li class="divider-vertical"></li>
                            <?php makeMenuItem("Suggest a Session", may_I('BrainstormSubmit'),"BrainstormWelcome.php", false); ?>
                            <li class="divider-vertical"></li>
                        </ul>
                            <?php if (may_I('Staff')) {
                                echo '<ul class="nav pull-right"><li class="divider-vertical"></li><li><a id="StaffView" href="StaffPage.php">Staff View</a></li></ul>';
                            }?>
                    </div>
                </div>
            </div>
        </nav>
<?php       } // end of bootstrap 2
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
