<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Administer Participants";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
staff_header($title, $bootstrap4);
if ($fbadgeid) {
    echo "<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
}
?>
<form id="adminParticipantsForm" class="form-row">
    <div id="resultBoxDIV" class="container-fluid"><span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span></div>
    <div id="searchPartsDIV" class="container-fluid">
        <div class="row mt-3">
            <div class="col-sm-12">
                <div class="dialog">Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete <?php echo USER_ID_PROMPT; ?>.
                </div>
            </div>
        </div>
        <div style="margin-top: 0.5em">
            <input type="text" id="searchPartsINPUT" onkeypress = "if (event.keyCode === 13) doSearchPartsBUTN();" />
            <input type="hidden" id="searchPhotoApproval" value=""/>
            <div class="btn-group" role="group" aria-label="search actions">
                <button type="button" class="btn btn-primary" data-loading-text="Searching..." id="searchPartsBUTN" style="margin-right:10px;">Search</button>
                <button type="button" class="btn btn-secondary" id="prevSearchResultBUTN" style="display: none; margin-right:10px;" disabled onclick="prevParticipant();">Previous</button>
                <button type="button" class="btn btn-secondary" id="nextSearchResultBUTN" style="display: none; margin-right:10px;" disabled onclick="nextParticipant();">Next</button>
                <button type="button" class="btn btn-secondary" id="toggleSearchResultsBUTN"><span id="toggleText">Hide</span> Results</button>
            </div>
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
            <div class="col-sm-2 col-xl-1">
                <div class="">
                    <label for="badgeid" class="mb-1"><?php echo USER_ID_PROMPT; ?>:</label>
                </div>
                <div>
                    <input class="col-text-input disabled" id="badgeid" type="text" readonly="readonly" />
                </div>
            </div>
<?php
if (USE_REG_SYSTEM === TRUE) {
?>
            <div class="col-sm-3">
                <div class="">
                    <label for="lname_fname" class="mb-1">Last name, first name:</label>
                </div>
                <div>
                    <input class="col-text-input disabled" id="lname_fname" type="text" readonly="readonly" style="max-width:20rem;" />
                </div>
            </div>
<?php
} else {
?>
            <div class="col-sm-3">
                <div class="">
                    <label for="lastname" class="mb-1">Last name:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="lastname" type="text" maxlength="40" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="firstname" class="mb-1">First name:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="firstname" type="text" maxlength="35" />
                </div>
            </div>
<?php
};
?>
            <div class="col-sm-3">
                <div class="">
                    <label for="badgename" class="mb-1">Badge name:</label>
                </div>
                <div>
<?php if (USE_REG_SYSTEM === TRUE) { 
?>
                    <input type="text" id="badgename" class="col-text-input disabled" readonly="readonly" maxlength="50" />
<?php
} else {
?>
                    <input type="text" id="badgename" class="col-text-input mycontrol" maxlength="50" />
<?php
}
?>
                </div>
            </div>
<?php
if (USE_REG_SYSTEM === FALSE) {
?> 
        </div>
        <div class="row mt-3">
            <div class="col-sm-3 offset-sm-2 offset-xl-1">
                <div class="">
                    <label for="phone" class="mb-1">Phone number:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="phone" type="text" maxlength="100" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="email" class="mb-1">Email address:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="email" type="text" maxlength="100" />
                </div>
            </div>
<?php
};
?>
            <div class="col-sm-3">
                <div class="">
                    <label for="pubsname" class="mb-1">Name for publications:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="pubsname" type="text" readonly="readonly" maxlength="50" />
                </div>
            </div>
        </div>
<?php
if (USE_REG_SYSTEM === FALSE) {
?>
        <div class="row mt-3">
            <div class="col-sm-3 offset-sm-2 offset-xl-1">
                <div class="">
                    <label for="postaddress1" class="mb-1">Postal Address:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="postaddress1" type="text" maxlength="100" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="postaddress2" class="mb-1">Postal Address Line 2:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="postaddress2" type="text" maxlength="100" />
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-2 offset-sm-2 offset-xl-1">
                <div class="">
                    <label for="postcity" class="mb-1">City:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="postcity" type="text" maxlength="50" />
                </div>
            </div>
            <div class="col-sm-1">
                <div class="">
                    <label for="poststate" class="mb-1">State:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="poststate" type="text" maxlength="25" />
                </div>
            </div>
            <div class="col-sm-2">
                <div class="">
                    <label for="postzip" class="mb-1">Zip Code:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="postzip" type="text" maxlength="10" style="max-width:7rem;" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="postcountry" class="mb-1">Country:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="postcountry" type="text" maxlength="25" />
                </div>
            </div>
        </div>
<?php
};
?>
        <div class="row mt-3">
            <div class="container-sm p-2 my-2 border border-dark">
                <div class="control-group">
                    <label for="interested" class="control-label">Participant is interested and available to participate
                        in <?php echo CON_NAME; ?> programming:</label>
                    <div class="controls">
                        <select id="interested" class="yesno mycontrol" disabled="disabled">
                            <option value="0" selected="selected">&nbsp</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                    </div>
                    <p class="help-block">Changing this to <i>No</i> will remove the participant from all sessions.</p>
                </div>
            </div>
        </div>
<?php
if (may_I("ResetUserPassword")) {
?>
        <div class="row">
            <div class="col-sm-3">
                <div class="">
                    <label for="password">Change Participant's Password:</label>
                </div>
                <div>
                    <input type="password" maxlength="40" id="password" readonly="readonly" class="mycontrol" />
                </div>
                <span id="passwordsDontMatch" style="color: red;">Passwords don't match.</span>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="cpassword">Confirm New Password:</label>
                </div>
                <div>
                    <input type="password" maxlength="40" id="cpassword" readonly="readonly" class="mycontrol" />
                </div>
            </div>
        </div>
<?php
};
?>
        <div class="row mt-3">
            <div class="col-sm-6">
                <div>
                    <label for="bio" class="">Participant biography:</label>
                </div>
                <div>
                    <textarea id="bio" class="mycontrol" rows="4" cols="80" readonly="readonly" data-maxlength="<?php echo MAX_BIO_LEN?>"></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="">
                    <label for="staffnotes" class="">Staff notes re. participant:</label>
                </div>
                <div>
                    <textarea id="staffnotes" rows="6" cols="80" readonly="readonly" class="mycontrol"></textarea>
                </div>
            </div>
        </div>
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
            <button type="button" class="btn btn-primary" data-loading-text="Updating..." id="updateBUTN"
                onclick="updateBUTTON();" disabled="disabled">Update
            </button>
        </div>
    </div>
</form>
<?php
staff_footer();
?>
