<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Administer Photos";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
$fbadgeid = getInt("badgeid");
staff_header($title, $bootstrap4);
if ($fbadgeid) {
    echo "<script type=\"text/javascript\">fbadgeid = $fbadgeid;</script>\n";
}
if (PARTICIPANT_PHOTOS === TRUE && may_I('AdminPhotos')) {
    $denyReasons = array();
    $sql = "SELECT photodenialreasonid, reasontext FROM PhotoDenialReasons ORDER BY display_order;";
    $result = mysqli_query_exit_on_error($sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $denyReasons[$row["photodenialreasonid"]] = $row["reasontext"];
    }
	mysqli_free_result($result);
?>
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
            <input type="checkbox" id="searchPhotoApproval"/> Photos Needing Approval&nbsp;
            <div class="btn-group" role="group" aria-label="search actions">
                <button type="button" class="btn btn-primary" data-loading-text="Searching..." id="searchPartsBUTN" style="margin-right:10px;">Search</button>
                <button type="button" class="btn btn-secondary" id="prevSearchResultBUTN" style="display: none; margin-right:10px;" disabled onclick="prevParticipant();">Previous</button>
                <button type="button" class="btn btn-secondary" id="nextSearchResultBUTN" style="display: none; margin-right:10px;" disabled onclick="nextParticipant();">Next</button>
                <button type="button" class="btn btn-secondary" id="toggleSearchResultsBUTN"><span id="toggleText">Hide</span> Results</button>
            </div>
        </div>
        <div style="margin-top: 1em; height:250px; border: 1px solid grey" id="searchResultsDIV">&nbsp;
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
            <div class="col-sm-3">
                <div class="">
                    <label for="lname_fname" class="mb-1">Last name, first name:</label>
                </div>
                <div>
                    <input class="col-text-input disabled" id="lname_fname" type="text" readonly="readonly" style="max-width:20rem;" />
                </div>
            </div>
            <div class="col-sm-3">
                <div class="">
                    <label for="badgename" class="mb-1">Badge name:</label>
                </div>
                <div>
                    <input type="text" id="badgename" class="col-text-input disabled" readonly="readonly" maxlength="50" />
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-sm-5 card alert-secondary">
                <input type="file" id="chooseFileName" name="chooseFileName" accept="image/png, image/jpeg, image/jpg" style="display: none"/>
                <p class="card-title">
                    Upload Photo: Drag/Drop file or <button type="button" class="btn btn-secondary btn-sm" id="uploadPhoto">Choose File</button>
                </p>
                <div class="card-body" id="photoUploadArea" style="margin-right: auto; margin-left: auto; margin-top:0;">
                    <input type="hidden" name="defaultPhoto" id="default_photo" value="<?php echo PHOTO_PUBLIC_DIRECTORY . '/' . PHOTO_DEFAULT_IMAGE ?>"/>
                    <img class="upload-image" style="width: 400px; height: 400px; object-fit: scale-down; margin-top:0; margin-right: auto; margin-left: auto;" id="uploadedPhoto"/>     
                </div>        
                <div class="btn-group" role="group" aria-label="crop actions">
                    <button type="button" class="btn btn-primary btn-sm" id="crop" style="display: none;" onClick="crop();">Crop</button>
                    <button type="button" class="btn btn-primary btn-sm" id="save_crop" style="display: none; margin-right: 10px;" onClick="savecrop();">Save Crop</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="rotate_left" style="display: none; margin-right: 10px;" onClick="rotate(90);">Rotate Left</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="rotate_right" style="display: none; margin-right: 10px;" onClick="rotate(-90);">Rotate Right</button>
                    <button type="button" class="btn btn-warning btn-sm" id="cancel_crop" style="display: none" onClick="cancelcrop();">Cancel Crop</button>
                </div>
            </div>
            <div class="col-sm-1"></div>
                <div class="col-sm-5 card alert-secondary">
                    <p class="card-title">Approved Photo</p>
                    <div class="card-body" style="margin-right: auto; margin-left: auto; margin-top:0;">
                        <img class="approved-image" id="approvedPhoto" style="width: 400px; height: 400px; object-fit: scale-down; margin-top:0; margin-right: auto; margin-left: auto;" />
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-sm-5">
                <input type="hidden" name="photouploadstatus"/>
                Photo Status:
                <span id="uploadedPhotoStatus"/>
            </div>
        </div>
        <div id="denyDetailsDIV" style="display: none;">
            <div class="row mt-1">
                <div class="col-sm-5">
                    Denial Reason:
                    <span id="denialReasonSPAN"/>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-sm-5">
                <div class="btn-group" role="group" aria-label="Photo Update/Delete actions">
                    <button type="button" class="btn btn-danger btn-sm" id="deleteUploadPhoto" onclick="deleteuploadedphoto();" style="display: none; margin-right: 10px;">
                        Delete Uploaded Photo
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="updateUploadPhoto" style="display: none; margin-right: 10px;" onclick="starttransfer();">
                        Upload Updated Photo
                    </button>
                    <button type="button" class="btn btn-success btn-sm" id="ApprovePhoto" style="display: none; margin-right: 10px;" onclick="approvephoto();">
                        Approve Photo
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="DenyPhoto" style="display: none;" onclick="denyphoto();">
                        Deny Photo
                    </button>
                </div>
            </div>
            <div class="col-sm-1"></div>
            <div class="col-sm-5">
                <button type="button" class="btn btn-danger btn-sm" id="deleteApprovedPhoto" onclick="deleteapprovedphoto();" style="display: none">
                    Delete Approved Photo
                </button>
            </div>
        </div>
        <div class="row mt-4" id="denyReasonDIV" style="display: none;">
            <div class="col-sm-12">
                <div class="btn-group" role="group" aria-label="Deny actions">
                    <label for="denyReason" class="control-label" style="margin-right: 10px;">Denial reason: </label>
                    <select id="denyReason" name="denyReason" onchange="enableDeny();">
                        <option value="" selected="selected">--</option>
<?php
        foreach ($denyReasons as $code => $reasontext) {
            echo '<option value="' . $code . '">' . $reasontext . "</option>\n";
        }
?>
                    </select>
                    <label for="denyOtherText" class="control-label" style="margin-left: 20px; margin-right: 10px;">Details: </label>
                    <input type="text" maxlength="500" size="50" id="denyOtherText" name="denyOtherText" />
                    <button type="button" id="denyBTN" class="btn btn-danger btn-sm" style="display: block; margin-left: 10px;" disabled onclick="doDeny();">Deny</button>
                    <button type="button" id="canceldenyBTN" class="btn btn-secondary btn-sm" style="display: block; margin-left: 10px;" onclick="cancelDeny();">Cancel</button>
                </div>
            </div>
        </div>
<?php
}
staff_footer();
?>
