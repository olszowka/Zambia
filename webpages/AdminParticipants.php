<?php
//  Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Administer Participants";
require_once('StaffCommonCode.php');
require('ParticipantTags_FNC.php');
$fbadgeid = getString("badgeid");
staff_header($title, 'bs5');
if (!isLoggedIn() || !may_I('Staff')) {
    staff_footer();
    exit();
}
if ($fbadgeid) {
    echo "<script type=\"text/javascript\">fbadgeid = '$fbadgeid';</script>\n";
}
?>
<form id="adminParticipantsForm"">
    <div id="resultBoxDIV" class="container-fluid">
        <span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span>
    </div>
    <div id="searchPartsDIV" class="container-xxl border-bottom border-dark pb-3">
        <div class="d-none d-lg-block">
            <div class="row mt-3">
                <div class="col-lg-11 offset-xl-1 col-xl-9 offset-xxl-3 col-xxl-9">
                    <label for="searchPartsINPUT_lg" class="dialog">
                        Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete <?php echo USER_ID_PROMPT; ?>.
                    </label>
                </div>
                <div class="col-lg-14 col-xl-13 col-xxl-14">
                    <div class="pt-4 text-center">Participant Tags</div>
                </div>
            </div>
            <div class="row mt-3 row-gap-3">
                <div class="col-lg-9 offset-xl-1 col-xl-8 offset-xxl-3 col-xxl-8">
                    <input type="text" id="searchPartsINPUT_lg" class="form-control searchPartsINPUT" />
                </div>
                <div class="offset-lg-1 col-lg-16 offset-xl-1 col-xl-13 col-xxl-14">
                    <div class="row">
                        <div class="col checkbox-list-container tag-search-container">
                            <?php
                            echo fetch_participant_tags(true);
                            ?>
                        </div>
                        <div class="col ms-4 me-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tagmatchRadio" id="tagMatchAny_lg" value="tagmatchany" checked>
                                <label class="form-check-label" for="tagMatchAny_lg">
                                    Match any selected
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tagmatchRadio" id="tagMatchAll_lg" value="tagmatchall">
                                <label class="form-check-label" for="tagMatchAll_lg">
                                    Match all selected
                                </label>
                            </div>
                            <div class="form-check disabled">
                                <input class="form-check-input" type="radio" name="tagmatchRadio" id="tagMatchNotAll_lg" value="tagmatchnotall">
                                <label class="form-check-label" for="tagMatchNotAll_lg">
                                    Exclude any selected
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-xl-13 col-xxl-10">
                    <input type="hidden" id="searchPhotoApproval" value=""/>
                    <div class="d-flex flex-wrap column-gap-2 row-gap-3" role="group" aria-label="search actions">
                        <button type="button" class="btn btn-primary searchPartsBUTN" data-loading-text="Searching..." >Search</button>
                        <button type="button" class="btn btn-secondary toggleSearchResultsBUTN">Hide Results</button>
                        <button type="button" class="btn btn-secondary prevSearchResultBUTN" style="display: none;" disabled onclick="prevParticipant();">Previous</button>
                        <button type="button" class="btn btn-secondary nextSearchResultBUTN" style="display: none;" disabled onclick="nextParticipant();">Next</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-block d-lg-none">
            <div class="row mt-3">
                <div class="col">
                    <div>
                        <label for="searchPartsINPUT_sm" class="dialog">
                            Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete <?php echo USER_ID_PROMPT; ?>.
                        </label>
                    </div>
                    <div class="mt-2">
                        <input type="text" id="searchPartsINPUT_sm" class="form-control searchPartsINPUT" />
                    </div>
                </div>
                <div class="col">
                    <div class="text-center">Participant Tags</div>
                    <div class="mt-2 d-flex justify-content-center">
                        <div class="checkbox-list-container tag-search-container">
                            <?php
                            echo fetch_participant_tags(true);
                            ?>
                        </div>
                    </div>
                    <div class="mt-2 d-flex justify-content-center">
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tagmatchRadio" id="tagMatchAny_sm" value="tagmatchany" checked>
                                <label class="form-check-label" for="tagMatchAny_sm">
                                    Match any selected
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tagmatchRadio" id="tagMatchAll_sm" value="tagmatchall">
                                <label class="form-check-label" for="tagMatchAll_sm">
                                    Match all selected
                                </label>
                            </div>
                            <div class="form-check disabled">
                                <input class="form-check-input" type="radio" name="tagmatchRadio" id="tagMatchNotAll_sm" value="tagmatchnotall">
                                <label class="form-check-label" for="tagMatchNotAll_sm">
                                    Exclude any selected
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col d-flex flex-wrap align-content-center row-gap-3 column-gap-2">
                    <button type="button" class="btn btn-primary searchPartsBUTN" data-loading-text="Searching..." >Search</button>
                    <button type="button" class="btn btn-secondary toggleSearchResultsBUTN">Hide Results</button>
                    <button type="button" class="btn btn-secondary prevSearchResultBUTN" style="display: none;" disabled onclick="prevParticipant();">Previous</button>
                    <button type="button" class="btn btn-secondary nextSearchResultBUTN" style="display: none;" disabled onclick="nextParticipant();">Next</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container-xxl">
        <div class="mt-3 border border-dark" style="height:250px; overflow:auto;" id="searchResultsDIV">&nbsp;
        </div>
    </div>
    <div id="unsavedWarningModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Not Saved</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>You have unsaved changes for <span id='warnName'></span>, <?php echo USER_ID_PROMPT; ?>: <span id='warnNewBadgeID'></span>!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancelOpenSearchBUTN" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="overrideOpenSearchBUTN" class="btn btn-secondary" onclick="return loadNewParticipant();" >Discard changes</button>
                </div>
            </div>
        </div>
    </div>
    <div id="resultsDiv" class="container-xxl">
        <div class="row mt-3">
            <div class="col-sm-7 col-lg-3">
                <div>
                    <label for="badgeid" class="mb-1"><?php echo USER_ID_PROMPT; ?>:</label>
                </div>
                <div>
                    <input class="col-text-input disabled" id="badgeid" type="text" readonly="readonly" />
                </div>
            </div>
<?php
if (USE_REG_SYSTEM === TRUE && UPDATE_REG_SYSTEM === FALSE) {
?>
            <div class="col-sm-10 col-lg-8">
                <div>
                    <label for="lname_fname" class="mb-1">Last name, first name:</label>
                </div>
                <div>
                    <input class="col-text-input disabled" id="lname_fname" type="text" readonly="readonly" style="max-width:20rem;" />
                </div>
            </div>
<?php
} else {
?>
            <div class="col-sm-10 col-lg-8">
                <div>
                    <label for="lastname" class="mb-1">Last name:</label>
                </div>
                <div>
                    <input class="form-control" id="lastname" type="text" maxlength="40" />
                </div>
            </div>
            <div class="col-sm-10 col-lg-8">
                <div>
                    <label for="firstname" class="mb-1">First name:</label>
                </div>
                <div>
                    <input class="form-control" id="firstname" type="text" maxlength="35" />
                </div>
            </div>
<?php
}
?>
            <div class="col-sm-9 col-lg-8">
                <div>
                    <label for="badgename" class="mb-1">Badge name:</label>
                </div>
                <div>
<?php
if (USE_REG_SYSTEM === TRUE && UPDATE_REG_SYSTEM === FALSE) {
?>
                    <input type="text" id="badgename" class="form-control disabled" readonly="readonly" maxlength="50" />
<?php
} else {
?>
                    <input type="text" id="badgename" class="form-control" maxlength="50" />
<?php
}
?>
                </div>
            </div>
<?php
if (USE_REG_SYSTEM === TRUE) {
?>
            <div class="d-none d-lg-block col-sm-6 offset-lg-2 col-lg-7 offset-xl-0 col-xl-6">
                <div>
                    <label for="regtype1" class="mb-1">Registration Status</label>
                </div>
                <div>
                    <input class="form-control disabled regtype" id="regtype1" type="text" readonly="readonly" />
                </div>
            </div>
<?php
}
?>
        </div>
        <div class="row mt-3">
<?php
if (USE_REG_SYSTEM === TRUE) {
?>
            <div class="d-block d-lg-none col-sm-9 offset-lg-2 col-lg-7 offset-xl-0 col-xl-6">
                <div>
                    <label for="regtype2" class="mb-1">Registration Status</label>
                </div>
                <div>
                    <input class="form-control disabled regtype" id="regtype2" type="text" readonly="readonly" />
                </div>
            </div>
<?php
}
?>
            <div class="col-sm-9 offset-lg-3 col-lg-8">
                <div>
                    <label for="email" class="mb-1">Email address:</label>
                </div>
                <div>
<?php
if (USE_REG_SYSTEM === TRUE) {
?>
                    <input class="form-control disabled" id="email" type="text" maxlength="100" readonly = "readonly" data-readonly = "true" />
<?php
} else {
?>
                    <input class="form-control " id="email" type="text" maxlength="100" />
<?php
}
?>
                </div>
            </div>
            <div class="col-sm-9 col-lg-8">
                <div>
                    <label for="pubsname" class="mb-1">Name for publications:</label>
                </div>
                <div>
                    <input class="form-control" id="pubsname" type="text" readonly="readonly" maxlength="50" />
                </div>
            </div>
<?php
if (ENABLE_NAME_FOR_SORTING) {
?>
            <div class="col-sm-9 col-lg-10 col-xl-8">
                <div>
                    <label for="name_for_sorting" class="mb-1">Name for sorting in publications:</label>
                </div>
                <div>
                    <input class="form-control" id="name_for_sorting" type="text" readonly="readonly" maxlength="50" />
                </div>
            </div>
<?php
} else {
?>
            <input type="hidden" id="name_for_sorting" />
<?php
}
?>
            <div class="d-none d-lg-block col-lg-7 col-xl-6">
                <div>
                    <label for="phone1" class="mb-1">Phone number:</label>
                </div>
                <div>
                    <input class="form-control phone-inp" id="phone1" type="text" maxlength="25" />
                </div>
            </div>
        </div>
<?php
if (USE_REG_SYSTEM === FALSE || UPDATE_REG_SYSTEM === TRUE) {
?>
        <div class="row mt-3">
            <div class="d-block d-lg-none col-sm-8">
                <div>
                    <label for="phone2" class="mb-1">Phone number:</label>
                </div>
                <div>
                    <input class="form-control phone-inp" id="phone2" type="text" maxlength="25" />
                </div>
            </div>
            <div class="col-sm-14 offset-lg-3 col-lg-8">
                <div>
                    <label for="postaddress1" class="mb-1">Postal Address:</label>
                </div>
                <div>
                    <input class="form-control" id="postaddress1" type="text" maxlength="100" />
                </div>
            </div>
            <div class="col-sm-14 col-lg-8">
                <div>
                    <label for="postaddress2" class="mb-1">Postal Address Line 2:</label>
                </div>
                <div>
                    <input class="form-control" id="postaddress2" type="text" maxlength="100" />
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-8 offset-lg-3 col-lg-5">
                <div>
                    <label for="postcity" class="mb-1">City:</label>
                </div>
                <div>
                    <input class="form-control" id="postcity" type="text" maxlength="50" />
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div>
                    <label for="poststate" class="mb-1">State:</label>
                </div>
                <div>
                    <input class="col-text-input mycontrol" id="poststate" type="text" maxlength="25" />
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div>
                    <label for="postzip" class="mb-1">Zip Code:</label>
                </div>
                <div>
                    <input class="form-control" id="postzip" type="text" maxlength="10" style="max-width:7rem;" />
                </div>
            </div>
            <div class="col-sm-9">
                <div>
                    <label for="postcountry" class="mb-1">Country:</label>
                </div>
                <div>
                    <input class="form-control" id="postcountry" type="text" maxlength="25" />
                </div>
            </div>
        </div>
<?php
}
?>
        <div class="row mt-3">
            <div class="col-sm-36 offset-lg-3 col-lg-27 col-xl-25 col-xxl-21">
                <div class="control-group border border-dark pt-2 px-2">
                    <label for="interested" class="control-label">Participant is interested and available to participate
                        in <?php echo CON_NAME; ?> programming:</label>
                    <div class="controls py-1">
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
        <div class="row mt-2">
            <div class="col-sm-13 offset-lg-3 col-lg-10 col-xl-8">
                <div>
                    <label for="password" class="mb-1">Change Participant's Password:</label>
                </div>
                <div>
                    <input type="password" maxlength="40" id="password" readonly="readonly" />
                </div>
                <span id="passwordsDontMatch" style="color: red;">Passwords don't match.</span>
            </div>
            <div class="col-sm-10">
                <div>
                    <label for="cpassword" class="mb-1">Confirm New Password:</label>
                </div>
                <div>
                    <input type="password" maxlength="40" id="cpassword" readonly="readonly" />
                </div>
            </div>
        </div>
<?php
}
?>
        <div class="row mt-3">
            <div class="col-sm-18">
                
<?php
if (HTML_BIO === TRUE) {
?>
              <div>
                <label for="htmlbio" class="mb-1">Participant biography:</label>
              </div>
              <div class="newforminput">
                <textarea id="htmlbio" rows="4" cols="80" class="mycontrol" readonly="readonly" data-max-length="<?php echo MAX_BIO_LEN?>"></textarea>
              </div>
<?php
} else {
?>
              <div>
                <label for="bio" class="mb-1">Participant biography:</label>
              </div>
              <div>
                <textarea id="bio" class="mycontrol" rows="4" cols="80" readonly="readonly"  data-max-length="<?php echo MAX_BIO_LEN?>"></textarea>
              </div>
            
<?php
}
?>
            </div>
            <div class="col-sm-18">
                <div class="newformlabel">
                    <label for="staffnotes" class="mb-1">Staff notes re. participant:</label>
                </div>
                <div class="newforminput">
                    <textarea id="staffnotes" rows="6" cols="80" readonly="readonly" class="form-control mycontrol"></textarea>
                </div>
<?php
if (HTML_BIO === TRUE) {
?>
                <div class="newformlabel">
                    <label for="bio" class="newformlabel mb-1">Text biography:</label>
                </div>
                <div class="newforminput">
                    <textarea id="bio" rows="8" cols="80" class="userFormTXT readonly mycontrol form-control" readonly="readonly" data-max-length="<?php echo MAX_BIO_LEN?>"></textarea>
                </div>
<?php
}
?>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-12 col-lg-8">
                <div class="pb-1">
                    User Permission Roles:
                </div>
                <div>
                    <div class="checkbox-list-container" id="role-container">
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="pb-1">
                    Participant Tags:
                </div>
                <div>
                    <div class="checkbox-list-container" id="tag-container">
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
            <div class="col col-auto" id="showsurveydiv" style="display: none;">
                <button type="button" class="btn btn-info" id="showsurveyBTN" disabled="disabled" onclick="showSurveyBUTTON();">Show Survey Responses
                </button>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-36">
                <div class="pb-1">
                    Participant's Schedule:
                </div>
                <div id="schedule-container">
                </div>
            </div>
        </div>
    </div>
</form>
<?php
staff_footer();
?>
