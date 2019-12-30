<?php
//	Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
function participant_header($title, $noUserRequired = false, $loginPageStatus = 'Normal') {
    // $noUserRequired is true if user not required to be logged in to access this page
    // $loginPageStatus is "Login", "Logout", "Normal", "No_Permission"
    //      login page should be "Login"
    //      logout page should be "Logout"
    //      logged in user who reached page for which he does not have permission is "No_Permission"
    //      all other pages should be "Normal"
    error_log("reached ParticipantHeader.php: 9.  Value of \$loginPageStatus is $loginPageStatus.");
    global $header_used;
    $header_used = HEADER_PARTICIPANT;
    page_header($title);
?>
<body>
    <div class="container-fluid">
<?php
    $is_logged_in = isLoggedIn();
    if ($is_logged_in && $loginPageStatus = 'Normal' && !may_I("Participant") && !may_I("Staff")) {
        $loginPageStatus = 'No_Permission';
    }
    $xml = new DomDocument("1.0", "UTF-8");
    $emptyDoc = $xml -> createElement("doc");
    $xml -> appendChild($emptyDoc);
    $xsl = new DomDocument;
    $xsl->load('xsl/GlobalHeader.xsl');
    $xslt = new XsltProcessor();
    $xslt->importStylesheet($xsl);
    $xslt->setParameter('', 'header_version', 'Participant');
    $xslt->setParameter('', 'logged_in', $is_logged_in);
    $xslt->setParameter('', 'login_page_status', $loginPageStatus);
    $xslt->setParameter('', 'no_user_required', $noUserRequired);
    $xslt->setParameter('', 'CON_NAME', CON_NAME);
    $xslt->setParameter('', 'badgename', isset($_SESSION['badgename']) ? $_SESSION['badgename'] : '');
    $xslt->setParameter('', 'USER_ID_PROMPT', USER_ID_PROMPT);
    $html = $xslt->transformToXML($xml);
    echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
    if ($is_logged_in && (may_I("Participant") || may_I("Staff"))) {
?>
        <nav id="participantNav" class="navbar navbar-inverse">
            <div class="navbar-inner">
                <div class="container" style="width: auto;">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<? echo $_SERVER['PATH_INFO'] ?>"><? echo $title ?></a>
                    <div class="nav-collapse">
                        <ul class="nav">
                            <li><a href="my_contact.php">Profile</a></li>
                            <?php makeMenuItem("Availability",may_I('my_availability'),"my_sched_constr.php",false); ?>
                            <?php makeMenuItem("Session Interests",may_I('my_panel_interests'),"PartPanelInterests.php",false); ?>
                            <!-- XXX this should have a may_I -->
                            <?php makeMenuItem("General Interests",1,"my_interests.php",false); ?>
                            <?php makeMenuItem("My Schedule",may_I('my_schedule'),"MySchedule.php",false); ?>
                            <?php makeMenuItem("Search Sessions",may_I('search_panels'),"my_sessions1.php",may_I('search_panels')); ?>
                            <?php makeMenuItem("Suggest a Session",may_I('BrainstormSubmit'),"BrainstormWelcome.php",may_I('BrainstormSubmit')); ?>
                            <li class="divider-vertical"></li>
                            <li><a href="welcome.php">Overview</a></li>
                            <li class="divider-vertical"></li>
                        </ul>
                            <?php if (may_I('Staff')) {
                                echo '<ul class="nav pull-right"><li class="divider-vertical"></li><li><a id="staffView" href="StaffPage.php">Staff View</a></li></ul>';
                            }?>
                    </div>
                </div>
            </div>
        </nav>
<?php } else {
        if (!$noUserRequired) {
            participant_footer();
            exit();
        }
    }
}
?>
