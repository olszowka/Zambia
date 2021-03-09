<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Import Reg User";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
staff_header($title, $bootstrap4);
if (!isLoggedIn() || !may_I('Staff') || !may_I('balt_ImportUsers')) {
    staff_footer();
    exit();
}
if ($fbadgeid) {
    echo "<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
}
?>
<form id="importRegUserForm" class="form-row">
    <div id="resultBoxDIV" class="container-fluid"><span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span></div>
    <div id="searchPartsDIV" class="container-fluid">
        <div class="row mt-3">
            <div class="col-sm-12">
                <div class="dialog">Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete <?php echo USER_ID_PROMPT; ?>.
                </div>
            </div>
        </div>
        <div style="margin-top: 0.5em">
            <input type="text" id="searchImportINPUT" onkeypress = "if (event.keyCode === 13) return doSearchImportBUTN(); else false;" />
            <button type="button" class="btn btn-primary" data-loading-text="Searching..." id="searchImportBUTN">Search</button>
        </div>
        <div style="margin-top: 1em; height:250px; overflow:auto; border: 1px solid grey" id="searchResultsDIV">&nbsp;
        </div>
    </div>
    <div id="unsavedWarningModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Not Saved</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>You have unsaved changes for <span id='warnName'></span>, <?php echo USER_ID_PROMPT; ?>: <span id='warnNewBadgeID'></span>!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancelOpenSearchBUTN" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="overrideOpenSearchBUTN" class="btn btn-secondary" onclick="return loadNewParticipant();" >Discard changes</button>
                </div>
            </div>
        </div>
    </div>
    <div id="resultsDiv" class="container-fluid">
        <div class="row mt-3">
            <div class="col-sm-4">
                <div class="pb-1">
                    User Permission Roles:
                </div>
                <div>
                    <div class="tag-chk-container" id="role-container">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col col-auto">
                <button type="button" class="btn btn-primary" data-loading-text="Updating..." id="updateBUTN"
                    onclick="updateBUTTON();" disabled="disabled">Update
                </button>
            </div>
        </div>
    </div>
</form>
<?php
staff_footer();
?>
