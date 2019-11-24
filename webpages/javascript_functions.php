<?php
//	Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
function load_external_javascript() {
    ?>
    <script src="external/jquery1.7.2/jquery-1.7.2.min.js"></script>
    <script src="external/jqueryui1.8.16/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="external/bootstrap2.3.2/bootstrap.js" type="text/javascript"></script>
    <script src="external/choices9.0.0/choices.min.js"></script>
    <?php
}

function load_internal_javascript($title, $isReport = false) {
    ?>
    <script src="javascript/main.js"></script>
    <?php
    /**
     * These js files initialize themselves and therefore should be included only on the relevant pages.
     * See main.js
     *
     * Session History -- SessionHistory.js
     * Invite Participants -- InviteParticipants.js
     * (Staff) Assign Participants -- StaffAssignParticipants.js
     * Maintain Room Schedule -- MaintainRoomSched.js
     *
     * Other js files may be included in this switch statement, but aren't required
     */
    switch ($title) {
        case "Assign Participants":
            echo "<script src=\"javascript/StaffAssignParticipants.js\"></script>\n";
            break;
        case "Invite Participants":
            echo "<script src=\"javascript/InviteParticipants.js\"></script>\n";
            break;
        case "Session History":
            echo "<script src=\"javascript/SessionHistory.js\"></script>\n";
            break;
        case "Maintain Room Schedule":
            echo "<script src=\"javascript/MaintainRoomSched.js\"></script>\n";
            break;
        default:
            if ($isReport) {
                echo "<script src=\"external/dataTables1.10.16/jquery.dataTables.js\"></script>\n";
                echo "<script src=\"javascript/Reports.js\"></script>\n";
            }
    }
?>
<script src="javascript/AdminParticipants.js"></script>
<script src="javascript/editCreateSession.js"></script>
<script src="javascript/myProfile.js"></script>
<script src="javascript/SearchMySessions1.js"></script>
<script src="javascript/staffMaintainSchedule.js"></script>
<script src="javascript/partPanelInterests.js"></script>
<?php
}
?>


