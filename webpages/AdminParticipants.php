<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Administer Participants";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
staff_header($title, $bootstrap4);
if ($fbadgeid)
	echo"<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
?>
<form class="form-row">
    <div id="searchPartsDIV" class="container-fluid">
        <div class="row mt-3">
            <div class="col-sm-12">
                <div class="dialog">Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete <?php echo USER_ID_PROMPT; ?>.
                </div>
            </div>
        </div>
        <div style="margin-top: 0.5em">
            <input type="text" id="searchPartsINPUT" onkeypress = "if (event.keyCode === 13) doSearchPartsBUTN();" />
            <button type="button" class="btn btn-primary" data-loading-text="Searching..." id="searchPartsBUTN">Search</button>
            <button type="button" class="btn btn-secondary" id="toggleSearchResultsBUTN"><span id="toggleText">Hide</span> Results</button>
        </div>
        <div style="margin-top: 1em; height:250px; overflow:auto; border: 1px solid grey" id="searchResultsDIV">&nbsp;
        </div>
    </div>
    <div class="modal hide" id="unsavedWarningDIV">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">x</button>
            <h3>Data Not Saved</h3>
        </div>
        <div class="modal-body">
            <p>You have unsaved changes for <span id='warnName'></span>!</p>
            <p class="hide" id='warnNewBadgeID'></p>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-primary" id="cancelOpenSearchBUTN" data-dismiss="modal">Cancel</a>
            <a href="#" class="btn" id="overrideOpenSearchBUTN" onclick="return loadNewParticipant();" data-dismiss="modal">Discard changes</a>
        </div>
    </div>
    <div id="resultBoxDIV" class="container-fluid"><span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span></div>
    <div id="resultsDiv" class="container-fluid">
        <div class="row mt-3">
            <div class="col-sm-1">
                <div class="">
                    <label for="badgeid" class="mb-1"><?php echo USER_ID_PROMPT; ?>:</label>
                </div>
                <div>
                    <input class="disabled" id="badgeid" type="text" readonly="readonly" size="8" />
                </div>
            </div>
<?php if (USE_REG_SYSTEM === TRUE) {?>
            <div class="col-sm-3">
                <div class="">
                    <label for="lname_fname" class="mb-1">Last name, first name:</label>
                </div>
                <div>
                    <input class="disabled" id="lname_fname" type="text" readonly="readonly" size="35" />
                </div>
            </div>
            <?php } else { ?>
            <div class="col-sm-3">
                <div class="">
                    <label for="lastname" class="mb-1">Last name:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="lastname" type="text" size="35" maxlength="40" onchange="textChange('lastname');" onkeyup="textChange('lastname');" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="firstname" class="mb-1">First name:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="firstname" type="text" size="35" maxlength="35" onchange="textChange('firstname');" onkeyup="textChange('firstname');"/>
                </div>
            </div>
            <?php }; ?>
            <div class="col-sm-3">
                <div class="">
                    <label for="bname" class="mb-1">Badge name:</label>
                </div>
                <div>
                    <input class="userFormINPTXT <?php if (USE_REG_SYSTEM === TRUE) { ?>disabled <?php }; ?>"
                        id="bname" type="text"
                        <?php if (USE_REG_SYSTEM === TRUE) { ?>readonly="readonly" <?php } else { ?> onchange="textChange('bname');" onkeyup="textChange('bname');" <?php }; ?>
                        size="35" maxlength="50" />
                </div>
            </div>
<?php if (USE_REG_SYSTEM === FALSE) { ?> 
        </div>
        <div class="row mt-3">
            <div class="col-sm-3 offset-sm-1">
                <div class="">
                    <label for="phone" class="mb-1">Phone number:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="phone" type="text" size="35" maxlength="100" onchange="textChange('phone');" onkeyup="textChange('phone');" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="email" class="mb-1">Email address:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="email" type="text" size="35" maxlength="100" onchange="textChange('email');" onkeyup="textChange('email');"/>
                </div>
            </div>
            <?php }; ?>
            <div class="col-sm-3">
                <div class="">
                    <label for="pname" class="mb-1">Name for publications:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="pname" type="text" readonly="readonly" size="35" maxlength="50" onchange="textChange('pname');" onkeyup="textChange('pname');" />
                </div>
            </div>
        </div>
<?php if (USE_REG_SYSTEM === FALSE) { ?>
        <div class="row mt-3">
            <div class="col-sm-3 offset-sm-1">
                <div class="">
                    <label for="postaddress1" class="mb-1">Postal Address:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="postaddress1" type="text" size="35" maxlength="100" onchange="textChange('postaddress1');" onkeyup="textChange('postaddress1');"/>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="postaddress2" class="mb-1">Postal Address Line 2:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="postaddress2" type="text" size="35" maxlength="100" onchange="textChange('postaddress2');" onkeyup="textChange('postaddress2');" />
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-sm-2 offset-sm-1">
                <div class="">
                    <label for="postcity" class="mb-1">City:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="postcity" type="text" size="25" maxlength="50" onchange="textChange('postcity');" onkeyup="textChange('postcity');" />
                </div>
            </div>
            <div class="col-sm-1">
                <div class="">
                    <label for="poststate" class="mb-1">State:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="poststate" type="text" size="8" maxlength="25" onchange="textChange('poststate');" onkeyup="textChange('poststate');" />
                </div>
            </div>
            <div class="col-sm-1">
                <div class="">
                    <label for="postzip" class="mb-1">Zip Code:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="postzip" type="text" size="10" maxlength="10" onchange="textChange('postzip');" onkeyup="textChange('postzip');" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="postcountry" class="mb-1">Country:</label>
                </div>
                <div>
                    <input class="userFormINPTXT" id="postcountry" type="text" size="25" maxlength="25" onchange="textChange('postcountry');" onkeyup="textChange('postcountry');"/>
                </div>
            </div>
        </div>
<?php }; ?>
        <div class="row mt-3">
            <div class="container-sm p-2 my-2 border border-dark">
                <div class="control-group">
                    <label for="interested" class="control-label">Participant is interested and available to participate
                        in <?php echo CON_NAME; ?> programming:</label>
                    <div class="controls">
                        <select id="interested" class="yesno" disabled="disabled" onchange="anyChange();">
                            <option value="0" selected="selected">&nbsp</option>
                            <option value="1">Yes</option>
                            <option value="2">No</option>
                        </select>
                    </div>
                    <p class="help-block">Changing this to <i>No</i> will remove the participant from all sessions.</p>
                </div>
            </div>
        </div>
<?php if (may_I("ResetUserPassword")) { ?>
        <div class="row">
            <div class="col-sm-3">
                <div class="">
                    <label for="password">Change Participant's Password:</label>
                </div>
                <div>
                    <input type="password" size="30" maxlength="40" id="password" readonly="readonly" onchange="anyChange();"
                        onkeyup="anyChange();"/>
                </div>
                <span id="passwordsDontMatch" style="color: red;">Passwords don't match.</span>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="cpassword">Confirm New Password:</label>
                </div>
                <div>
                    <input type="password" size="30" maxlength="40" id="cpassword" readonly="readonly" onchange="anyChange();"
                        onkeyup="anyChange();"/>
                </div>
            </div>
        </div>
<?php }; ?>
        <div class="row mt-3">
            <div class="col-sm-6">
                <div class="">
                    <label for="bio" class="">Participant biography:</label>
                </div>
                <div>
                    <textarea id="bio" rows="4" cols="80" readonly="readonly" data-maxlength="<?php echo MAX_BIO_LEN?>" onchange="textChange('bio');" onkeyup="textChange('bio');"></textarea>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="">
                    <label for="staffnotes" class="">Staff notes re. participant:</label>
                </div>
                <div>
                    <textarea id="staffnotes" rows="6" cols="80" readonly="readonly"
                        onchange="textChange('snotes');" onkeyup="textChange('snotes');"></textarea>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary" data-loading-text="Updating..." id="updateBUTN"
            onclick="updateBUTTON();" disabled="disabled">Update
        </button>
    </div>
</form>
<?php
staff_footer();
?>