<?php
//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
function load_jquery() {
    ?>
    <script src="jquery/jquery-1.7.2.min.js"></script>
    <script src="jquery/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="javascript/bootstrap.js" type="text/javascript"></script>
    <script src="javascript/main.js"></script>
    <?php
}

function load_javascript($title, $isReport = false) {
    switch ($title) {
        case "Assign Participants":
            echo "<script src=\"javascript/StaffAssignParticipants.js\"></script>\n";
            break;
        case "Session History":
            echo "<script src=\"javascript/SessionHistory.js\"></script>\n";
            break;
        default:
            if ($isReport) {
                echo "<script src=\"jquery/jquery.dataTables.js\"></script>\n";
                echo "<script src=\"javascript/Reports.js\"></script>\n";
            }
    }
?>
<script src="javascript/AdminParticipants.js"></script>
<script src="javascript/editCreateSession.js"></script>
<script src="javascript/MaintainRoomSched.js"></script>
<script src="javascript/myProfile.js"></script>
<script src="javascript/SearchMySessions1.js"></script>
<script src="javascript/staffMaintainSchedule.js"></script>
<script src="javascript/partPanelInterests.js"></script>
<?php
}
?>


