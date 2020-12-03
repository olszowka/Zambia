<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function load_external_javascript($isDataTables = false, $isRecaptcha = false, $bootstrap4 = false) {
    if ($bootstrap4) { ?>
    <script src="external/jquery3.5.1/jquery-3.5.1.min.js"></script>
    <script src="external/bootstrap4.5.0/bootstrap.bundle.min.js" type="text/javascript"></script>
<?php } else { ?>
    <script src="external/jquery1.7.2/jquery-1.7.2.min.js"></script>
    <script src="external/jqueryui1.8.16/jquery-ui-1.8.16.custom.min.js"></script>
    <script src="external/bootstrap2.3.2/bootstrap.js" type="text/javascript"></script>
<?php } ?>
    <script src="external/choices9.0.0/choices.min.js"></script>
<?php if ($isDataTables) { ?>
    <script src="external/dataTables1.10.16/jquery.dataTables.js"></script>
<?php }
    if ($isRecaptcha) { ?>
    <script async defer id="recaptcha-script" src="https://www.google.com/recaptcha/api.js"></script>
<?php }
}

function load_internal_javascript($title, $isDataTables = false) {
    ?>
    <script src="javascript/main.js"></script>
    <?php
    /**
     * These js files initialize themselves and therefore should be included only on the relevant pages.
     * See main.js
     *
     * (Staff) Assign Participants -- StaffAssignParticipants.js
     * Forgot Password -- ForgotPassword.js
     * Invite Participants -- InviteParticipants.js
     * Maintain Room Schedule -- MaintainRoomSched.js
     * Reset Password -- ForgotPasswordResetForm.js
     * Session History -- SessionHistory.js
     *
     * Other js files may be included in this switch statement, but aren't required
     */
    switch ($title) {
        case "Assign Participants":
            echo "<script src=\"javascript/StaffAssignParticipants.js\"></script>\n";
            break;
        case "Forgot Password":
            echo "<script src=\"javascript/ForgotPassword.js\"></script>\n";
            break;
        case "Invite Participants":
            echo "<script src=\"javascript/InviteParticipants.js\"></script>\n";
            break;
        case "Maintain Room Schedule":
            echo "<script src=\"javascript/MaintainRoomSched.js\"></script>\n";
            break;
        case "Reset Password":
            echo "<script src=\"javascript/ForgotPasswordResetForm.js\"></script>\n";
            break;
        case "Session History":
            echo "<script src=\"javascript/SessionHistory.js\"></script>\n";
            break;
        case "Administer Phases":
            echo "<script src=\"javascript/AdminPhases.js\"></script>\n";
            break;
        case "Edit Custom Text":
            echo "<script src=\"external/tinymce_5.4.2/js/tinymce/tinymce.min.js\"></script>\n";
            echo "<script src=\"javascript/EditCustomText.js\"></script>\n";
            break;
        case "Session Search Results":
            echo "<script src=\"javascript/PartSearchSessionsSubmit.js\"></script>\n";
            break;
        default:
            if ($isDataTables) {
                echo "<script src=\"javascript/Reports.js\"></script>\n";
            }
    }
?>
<script src="javascript/AdminParticipants.js"></script>
<script src="javascript/editCreateSession.js"></script>
<script src="javascript/myProfile.js"></script>
<script src="javascript/staffMaintainSchedule.js"></script>
<script src="javascript/partPanelInterests.js"></script>
<?php
}
?>