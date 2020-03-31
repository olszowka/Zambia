<?php
//	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function renderStaffMenu($title)
{
?>
<nav id="staffNav" class="navbar navbar-inverse">
    <div class="navbar-inner">
        <div class="container" style="width: auto;">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <?php echo "<a class=\"brand\" href=\"" . $_SERVER['REQUEST_URI'] . "\">$title</a>\n"; ?>
            <div class="nav-collapse">
                <ul class="nav">
                    <li class="dropdown">
                        <a href="#sessions" class="dropdown-toggle" data-toggle="dropdown">Sessions<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="StaffSearchSessions.php">Search Sessions</a></li>
                            <li><a href="CreateSession.php">Create New Session</a></li>
                            <li><a href="ViewSessionCountReport.php">View Session Counts</a></li>
                            <li><a href="ViewAllSessions.php">View All Sessions</a></li>
                            <li><a href="ViewPrecis.php?showlinks=0">View Precis</a></li>
                            <li><a href="ViewPrecis.php?showlinks=1">View Precis with Links</a></li>
                            <li><a href="StaffSearchPreviousSessions.php">Import Sessions</a></li>
                            <li><a href="SessionHistory.php">Session History</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#participants" class="dropdown-toggle" data-toggle="dropdown">Participants<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="AdminParticipants.php">Administer</a></li>
                            <li><a href="InviteParticipants.php">Invite to a Session</a></li>
                            <li><a href="StaffAssignParticipants.php">Assign to a Session</a></li>
                            <?php makeMenuItem("Send email", may_I('SendEmail'), "StaffSendEmailCompose.php", false); ?>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reports<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <?php
                            $prevErrorLevel = error_reporting();
                            $tempErrorLevel = $prevErrorLevel && ~E_WARNING;
                            error_reporting($tempErrorLevel);
                            if (!include 'ReportMenuInclude.php') {
                                echo "<li><div class='menu-error-entry'>Report menus not built!</div></li>\n";
                            } else { ?>
                                <li class='divider'></li>
                                <li><a href='staffReportsInCategory.php'>All Reports</a></li>
                            <?php }
                            error_reporting($prevErrorLevel);
                            ?>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#scheduling" class="dropdown-toggle" data-toggle="dropdown">Scheduling<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="MaintainRoomSched.php">Maintain Room Schedule</a></li>
                            <li><a href="StaffMaintainSchedule.php">Grid Scheduler</a></li>
                        </ul>
                    </li>
                    <li class="divider-vertical"></li>
                    <li><a href="StaffPage.php">Overview</a></li>
                    <?php makeMenuItem("Suggest a Session", may_I('BrainstormSubmit'), "BrainstormWelcome.php", may_I('BrainstormSubmit')); ?>
                    <li class="divider-vertical"></li>
                    <li>
                        <form method=POST action="ShowSessions.php" class="navbar-search pull-left">
                            <input type="text" name="searchtitle" class="search-query"
                                   placeholder="Search for sessions by title">
                            <input type="hidden" value="ANY" name="track">
                            <input type="hidden" value="ANY" name="status">
                            <input type="hidden" value="ANY" name="type">
                            <input type="hidden" value="" name="sessionid">
                            <input type="hidden" value="ANY" name="divisionid">
                        </form>
                    </li>
                    <?php if (may_I('ConfigureReports')) { ?>
                        <li class="dropdown">
                            <a href="#admin" class="dropdown-toggle" data-toggle="dropdown">Admin<b
                                        class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="BuildReportMenus.php">Build Report Menus</a></li>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
                <ul class="nav pull-right">
                    <li class="divider-vertical"></li>
                    <li><a id="ParticipantView" href="welcome.php">Participant View</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
        </div>
    </nav>
    <?php
}

?>

