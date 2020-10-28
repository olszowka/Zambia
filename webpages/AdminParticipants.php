<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Administer Participants";
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
staff_header($title);
if ($fbadgeid)
	echo"<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
?>
<div id="searchPartsDIV" class="vert-sep-above">
    <div class="dialog">Enter all or part of first name, last name, badge name, <span style="font-weight:bold">or</span> published name.  If you enter numbers, it will be interpreted as a complete badgeid.
  	</div>
  	<div style="margin-top: 0.5em">
  		<input type="text" id="searchPartsINPUT" onkeypress = "if (event.keyCode == 13) doSearchPartsBUTN();" />
  		<button type="button" class="btn btn-primary" data-loading-text="Searching..." id="searchPartsBUTN">Search</button>
  		<button type="button" class="btn" id="toggleSearchResultsBUTN"><span id="toggleText">Hide</span> Results</button>
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
<div id="resultBoxDIV"><span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span></div>
<div id="resultsDiv">
    <div class="row-fluid vert-sep-above">
        <div class="span1">
            <div class="newformlabel">
                <label for="badgeid" class="newformlabel">Badgeid:</label>
            </div>
            <div class="newforminput">
                <input class="span12 disabled" id="badgeid" type="text" readonly="readonly" size="8" />
            </div>
        </div>
        <?php if (USE_REG_SYSTEM === TRUE) {?>
        <div class="span3">
            <div class="newformlabel">
                <label for="lname_fname" class="newformlabel">Last name, first name:</label>
            </div>
            <div class="newforminput">
                <input class="span12 disabled" id="lname_fname" type="text" readonly="readonly" size="30" />
            </div>
        </div>
        <?php } else { ?>
        <div class="span3">
            <div class="newformlabel">
                <label for="lastname" class="newformlabel">Last name:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="lastname" type="text" size="40" onchange="textChange('lastname');" onkeyup="textChange('lastname');" />
            </div>
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="firstname" class="newformlabel">First name:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="firstname" type="text" size="35" onchange="textChange('firstname');" onkeyup="textChange('firstname');"/>
            </div>
        </div>
        <?php }; ?>
        <div class="span3">
            <div class="newformlabel">
                <label for="bname" class="newformlabel">Badge name:</label>
            </div>
            <div class="newforminput">
                <input class="span12 <?php if (USE_REG_SYSTEM === TRUE) { ?>disabled <?php }; ?>" id="bname" type="text" <?php if (USE_REG_SYSTEM === TRUE) { ?>readonly="readonly" <?php } else { ?> onchange="textChange('bname');" onkeyup="textChange('bname');" <?php }; ?> size="50" />
            </div>
        </div>
        <?php if (USE_REG_SYSTEM === FALSE) { ?> 
    </div>
    <div class="row-fluid vert-sep-above">
        <div class="span1">
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="phone" class="newformlabel">Phone number:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="phone" type="text" size="100" onchange="textChange('phone');" onkeyup="textChange('phone');" />
            </div>
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="email" class="newformlabel">Email address:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="email" type="text" size="100" onchange="textChange('email');" onkeyup="textChange('email');"/>
            </div>
        </div>
        <?php }; ?>
        <div class="span3">
            <div class="newformlabel">
                <label for="pname" class="newformlabel">Name for publications:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="pname" type="text" readonly="readonly" size="35" onchange="textChange('pname');" onkeyup="textChange('pname');" />
            </div>
        </div>
    </div>
    <?php if (USE_REG_SYSTEM === FALSE) { ?>
    <div class="row-fluid vert-sep-above">
        <div class="span1">
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="postaddress1" class="newformlabel">Postal Address:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="postaddress1" type="text" size="100" onchange="textChange('postaddress1');" onkeyup="textChange('postaddress1');"/>
            </div>
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="postaddress2" class="newformlabel">Postal Address Line 2:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="postaddress2" type="text" size="100" onchange="textChange('postaddress2');" onkeyup="textChange('postaddress2');" />
            </div>
        </div>
        <div class="span2">
            <div class="newformlabel">
                <label for="postcity" class="newformlabel">City:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="postcity" type="text" size="50" onchange="textChange('postcity');" onkeyup="textChange('postcity');" />
            </div>
        </div>
        <div class="span1">
            <div class="newformlabel">
                <label for="postzip" class="newformlabel">Zip Code:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="postzip" type="text" size="50" onchange="textChange('postzip');" onkeyup="textChange('postzip');" />
            </div>
        </div>
    </div>
    <div class="row-fluid vert-sep-above">
        <div class="span1">
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="postcountry" class="newformlabel">Country:</label>
            </div>
            <div class="newforminput">
                <input class="span12" id="postcountry" type="text" size="25" onchange="textChange('postcountry');" onkeyup="textChange('postcountry');"/>
            </div>
        </div>
        <div class="span3">
            <div class="newformlabel">
                <label for="regtype" class="newformlabel">Registration Type:</label>
            </div>
            <div class="newforminput">
                <input class="span12 disabled" id="regtype" type="text" size="100" readonly="readonly" />
            </div>
        </div>
    </div>
    <?php }; ?>
    <div class="row-fluid vert-sep-above">
        <div class="span8 offset2 borderBox">
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
    <div class="row-fluid vert-sep-above">
        <div class="span4">
            <div class="password2">
                <label for="password">Change Participant's Password:</label>
            </div>
            <div class="value">
                <input type="password" size="10" id="password" readonly="readonly" onchange="anyChange();"
                    onkeyup="anyChange();"/>
            </div>
            <span id="passwordsDontMatch" style="color: red;">Passwords don't match.</span>
        </div>
        <div class="span4">
            <div class="password2">
                <label for="cpassword">Confirm New Password:</label>
            </div>
            <div class="value">
                <input type="password" size="10" id="cpassword" readonly="readonly" onchange="anyChange();"
                    onkeyup="anyChange();"/>
            </div>
        </div>
    </div>
<?php }; ?>
    <div class="row-fluid vert-sep-above">
        <div class="span6">
            <div class="newformlabel">
                <label for="bio" class="newformlabel">Participant biography:</label>
            </div>
            <div class="newforminput">
                <textarea class="span12" id="bio" rows="4" cols="80" readonly="readonly" onchange="textChange('bio');" onkeyup="textChange('bio');"></textarea>
            </div>
        </div>
        <div class="span6">
            <div class="newformlabel">
                <label for="staffnotes" class="newformlabel">Staff notes re. participant:</label>
            </div>
            <div class="newforminput">
                <textarea class="span12" id="staffnotes" rows="4" cols="80" readonly="readonly"
                    onchange="textChange('snotes');" onkeyup="textChange('snotes');"></textarea>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-primary" data-loading-text="Updating..." id="updateBUTN"
        onclick="updateBUTN();" disabled="disabled">Update
    </button>
</div>
<?php
staff_footer();
?>