<?php
//  Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
function load_external_javascript($isDataTables = false, $isRecaptcha = false, $bootstrapVersion = 'bs5', $isJqueryUI = false) {
    switch ($bootstrapVersion) {
        case 'bs5':
?>
            <script src="external/jquery3.7.1/jquery-3.7.1.min.js"></script>
<?php if ($isJqueryUI) { ?>
            <script src="external/jqueryui1.14.2/jquery-ui-1.14.2.min.js"></script>
<?php } ?>
            <script src="external/bootstrap5.3.8/bootstrap.bundle.min.js" type="text/javascript"></script>
<?php
            break;
        case 'bs4':
?>
        <script src="external/jquery3.7.1/jquery-3.7.1.min.js"></script>
<?php if ($isJqueryUI) { ?>
        <script src="external/jqueryui1.14.2/jquery-ui-1.14.2.min.js"></script>
<?php } ?>
        <script src="external/bootstrap4.6.2/bootstrap.bundle.min.js" type="text/javascript"></script>
<?php
            break;
    } ?>
    <script src="external/choices9.0.0/choices.min.js"></script>
<?php if ($isDataTables) { ?>
    <script src="external/dataTables2.3.8/dataTables.js"></script>
<?php }
    if ($isRecaptcha) { ?>
    <script async defer id="recaptcha-script" src="https://www.google.com/recaptcha/api.js"></script>
<?php }
}

/**
 * TinyMCE (GPL since v7) is loaded from a version-pinned CDN rather than distributed with Zambia.
 * An installation may define TINYMCE_URL in db_name.php to load it from elsewhere (e.g. a self-hosted copy).
 * The SRI hash covers only tinymce.min.js; plugins/skins it loads are pinned by the versioned URL path.
 */
function tinymce_script_tag() {
    if (defined('TINYMCE_URL') && TINYMCE_URL !== '') {
        return "<script src=\"" . htmlspecialchars(TINYMCE_URL) . "\"></script>\n";
    }
    return "<script src=\"https://cdn.jsdelivr.net/npm/tinymce@8.10.0/tinymce.min.js\" "
        . "integrity=\"sha384-3pPvcZXivf5neWCEl4OuvTIleT8Q1bA2hphqFEVWNXaxIWVBUJda+xujUu2WWWuz\" "
        . "crossorigin=\"anonymous\"></script>\n";
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
        case "Configure Permissions":
            echo "<link rel=\"stylesheet\" href=\"javascript/ConfigurePermissions/dist/bundle.css\" type=\"text/css\">\n";
            echo "<script type=\"module\" src=\"javascript/ConfigurePermissions/dist/bundle.js\"></script>\n";
            break;
        case "Grid Scheduler":
            echo "<link rel=\"stylesheet\" href=\"javascript/GridScheduler/dist/bundle.css\" type=\"text/css\">\n";
            echo "<script type=\"module\" src=\"javascript/GridScheduler/dist/bundle.js\"></script>\n";
            break;
        case "Edit Custom Text":
            echo tinymce_script_tag();
            echo "<script src=\"javascript/EditCustomText.js\"></script>\n";
            break;
        case "Edit Survey":
            echo "<script src=\"external/tabulator-6.5.3/js/tabulator.js\"></script>\n";
            echo "<script src=\"javascript/EditSurvey.js\"></script>\n";
            echo "<script src=\"javascript/RenderSurvey.js\"></script>\n";
            echo tinymce_script_tag();
            break;
        case "Participant Survey":
            echo "<script src=\"javascript/PartSurvey.js\"></script>\n";
            echo "<script src=\"javascript/RenderSurvey.js\"></script>\n";
            echo tinymce_script_tag();
        case "Preview Survey":
            echo "<script src=\"javascript/RenderSurvey.js\"></script>\n";
            echo tinymce_script_tag();
            break;
        case "Session Search Results":
            echo "<script src=\"javascript/PartSearchSessionsSubmit.js\"></script>\n";
            break;
        case "Administer Participants":
            echo tinymce_script_tag();
            echo "<script src=\"javascript/AdminParticipants.js\"></script>\n";
            break;
        case "Administer Photos":
            echo "<script src=\"external/croppie.2.6.5/croppie.min.js\"></script>\n";
            echo "<script src=\"javascript/AdminPhotos.js\"></script>\n";
            break;
        case "My Profile":
            echo tinymce_script_tag();
            echo "<script src=\"javascript/myProfile.js\"></script>";
            break;
        case "My Photo":
            echo "<script src=\"external/croppie.2.6.5/croppie.min.js\"></script>\n";
            echo "<script src=\"javascript/myPhoto.js\"></script>";
            break;
        case "Edit Session":
            echo tinymce_script_tag();
            echo "<script src=\"javascript/editCreateSession.js\"></script>\n";
        case "Create New Session":
            echo tinymce_script_tag();
            echo "<script src=\"javascript/editCreateSession.js\"></script>\n";
            break;
        case "Edit Configuration Tables":
            echo tinymce_script_tag();
            echo "<script src=\"external/tabulator-6.5.3/js/tabulator.js\"></script>\n";
            echo "<script src=\"javascript/EditConfigTables.js\"></script>\n";
            break;
        case "Import Reg User":
            echo "<script src=\"javascript/BalticonImportRegUser.js\"></script>\n";
            break;
        case "Participant View":
            echo "<script src=\"javascript/ParticipantView.js\"></script>\n";
            break;
        default:
            if ($isDataTables) {
                echo "<script src=\"javascript/Reports.js\"></script>\n";
            }
    }
?>
<script src="javascript/partPanelInterests.js"></script>
<?php
}
?>
