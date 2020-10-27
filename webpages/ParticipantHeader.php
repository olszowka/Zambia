<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_PARTICIPANT;

function participant_header($title, $noUserRequired = false, $loginPageStatus = 'Normal', $bootstrap4 = false) {
    // $noUserRequired is true if user not required to be logged in to access this page
    // $loginPageStatus is "Login", "Logout", "Normal", "No_Permission", "Password_Reset"
    //      login page should be "Login"
    //      logout page should be "Logout"
    //      logged in user who reached page for which he does not have permission is "No_Permission"
    //      all other pages should be "Normal"
    global $headerErrorMessage;
    html_header($title, $bootstrap4);
    
if ($bootstrap4) { ?>
<body class="bs4">
<?php } else { ?>
<body>
<?php } ?>
    <div class="container-fluid">
<?php
    $isLoggedIn = isLoggedIn();
    commonHeader('Participant', $isLoggedIn, $noUserRequired, $loginPageStatus, $headerErrorMessage, $bootstrap4);
    // below: authenticated and authorized to see a menu
    if ($isLoggedIn && $loginPageStatus != 'Login' && 
        (may_I("Participant") || may_I("Staff"))) {
        if ($bootstrap4) {
            $paramArray = array();
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
                    <?php if ($loginPageStatus <> 'Consent') { ?>
                    <div class="nav-collapse">
                        <ul class="nav">
                            <li><a href="welcome.php">Overview</a></li>
                            <li><a href="my_contact.php">Profile</a></li>
                            <?php makeMenuItem("Availability", may_I('my_availability'),"my_sched_constr.php",false); ?>
                            <?php makeMenuItem("General Interests",1,"my_interests.php",false); ?>
                            <?php makeMenuItem("Search Sessions", may_I('search_panels'),"PartSearchSessions.php", false); ?>
                            <?php makeMenuItem("Session Interests", may_I('my_panel_interests'),"PartPanelInterests.php",false); ?>
                            <?php makeMenuItem("My Schedule", may_I('my_schedule'),"MySchedule.php",false); ?>
                            <li class="divider-vertical"></li>
                            <?php makeMenuItem("Suggest a Session", may_I('BrainstormSubmit'),"BrainstormWelcome.php", false); ?>
                            <li class="divider-vertical"></li>
                        </ul>
                            <?php if (may_I('Staff')) {
                                echo '<ul class="nav pull-right"><li class="divider-vertical"></li><li><a id="StaffView" href="StaffPage.php">Staff View</a></li></ul>';
                            }?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </nav>
<?php       }
    } else { // not authenticated and authorized to see a menu
        if (!$noUserRequired) {
            participant_footer();
            exit();
        }
    }
}
?>
