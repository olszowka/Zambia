// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var fbadgeid;
var resultsHidden = true;
var $denyButton = null;
var $prevBTN = null;
var $nextBTN = null;
var searchIndex = 0;
var badgeList = null;
var searchCount = -1;
var $lastname = null;
var $firstname = null;
var $badgename = null;
var $uploadPhotoStatus = null;
var $uploadedPhoto = null;
var $uploadPhotoDelete = null;
var $approvedPhotoDelete = null;
var $uploadUpdatedPhoto = null;
var $uploadZone = null;
var $uploadChooseFile = null;
var $chooseFileName = null;
var $approvedPhoto = null;
var $approvePhoto = null;
var $denyPhoto = null;
var $resultBoxDIV = null;
var $denyReasonDIV = null;
var $denyReason = null;
var $denyOtherText = null;
var $denialReasonSPAN = null
var $denyDetailsDIV = null;
var uploadlock = false;
var cropper = null;
var $cropBTN = null;
var $cropsaveBTN = null;
var $cropleftBTN = null
var $croprightBTN = null;
var $cropcancelBTN = null;
var default_photo = null;
var approved_dir = '/';
var curbadgeid = null;

const crop_hideall = 0;
const crop_showbtn = 1;
const crop_showdirections = 2;

function changeCropDisplay(cropstyle) { // 0 = hide all, 1 = show crop button, 2 = show crop directions
    $cropBTN.style.display = cropstyle == 1 ? 'block' : 'none';
    $cropsaveBTN.style.display = cropstyle == 2 ? 'block' :  'none';
    $cropleftBTN.style.display = cropstyle == 2 ? 'block' : 'none';
    $croprightBTN.style.display = cropstyle == 2 ? 'block' : 'none';
    $cropcancelBTN.style.display = cropstyle == 2 ? 'block' : 'none';
}

function nextParticipant() {
    if (searchIndex < (searchCount - 1)) {
        searchIndex++;
        chooseParticipant(badgeList[searchIndex], false);
    }
}

function prevParticipant() {
    if (searchIndex > 0) {
        searchIndex--;
        chooseParticipant(badgeList[searchIndex], false);
    }
}

function chooseParticipant(badgeid, override) {
    //debugger;
    curbadgeid = badgeid;
    var badgeidJQSel = badgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    hideSearchResults();
    if (badgeList) {
        searchIndex = badgeList.indexOf(badgeid, 0);
        if ($prevBTN)
            $prevBTN.disabled = searchIndex < 1;
        if ($nextBTN)
            $nextBTN.disabled = searchIndex == (badgeList.length - 1);
    }
    $("#badgeid").val($("#bidSPAN_" + badgeidJQSel).text());
    var lastname = $("#lastnameHID_" + badgeidJQSel).val();
    $lastname.val(lastname).prop("defaultValue", lastname).prop("readOnly", false);
    var firstname = $("#firstnameHID_" + badgeidJQSel).val();
    $firstname.val(firstname).prop("defaultValue", firstname).prop("readOnly", false);
    lnamefname = $("#lnameSPAN_" + badgeidJQSel).text();
    $("#lname_fname").val(lnamefname);
    $badgename.val($("#bnameSPAN_" + badgeidJQSel).text());
    $uploadPhotoStatus.innerHTML = $("#statustextSPAN_" + badgeidJQSel).text();

    $('#warnName').html(lnamefname);
    $('#warnNewBadgeID').html(badgeid);
    var denyOtherText = $("#approvalNotesHID_" + badgeidJQSel).val();
    $('#resultsDiv').show();
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';
    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $("#resultBoxDIV").html("").css("visibility", "hidden");
    uploadedPhotoName = $("#uploadedphotoHID_" + badgeidJQSel).val();
    if ($('#reasontextHID_' + badgeidJQSel).val().length > 0) {
        $denyDetailsDIV.style.display = 'block';
        var reasontext = $('#reasontextHID_' + badgeidJQSel).val();
        if ($('#denialOtherTextHID_' + badgeidJQSel).val().length > 0)
            reasontext += " (" + $('#denialOtherTextHID_' + badgeidJQSel).val() + ")";
        $denialReasonSPAN.textContent = reasontext;
    } else {
        $denyDetailsDIV.style.display = 'none';
        $denialReasonSPAN.textContent = "";
    }
    if (uploadedPhotoName.length > 0) {
        console.log("loading photo " + uploadedPhotoName);
        $uploadedPhoto.src= "SubmitAdminPhotos.php?ajax_request_action=fetchphoto&badgeid=" + badgeidJQSel;
        $uploadPhotoDelete.style.display = 'block';
        $approvePhoto.style.display = 'block';
        $denyPhoto.style.display = 'block';
        $uploadUpdatedPhoto.style.display = 'none';
        changeCropDisplay(crop_showbtn);
        
    }
    else {
        $uploadPhotoDelete.style.display = 'none';
        $uploadUpdatedPhoto.style.display = 'none';
        $approvePhoto.style.display = 'none';
        $denyPhoto.style.display = 'none';
        $uploadedPhoto.src = default_photo;
        changeCropDisplay(crop_hideall);
    }
    approvedPhotoName = $("#approvedphotoHID_" + badgeidJQSel).val();
    if (approvedPhotoName.length > 0) {
        $approvedPhoto.src = approved_dir + approvedPhotoName;
        $approvedPhotoDelete.style.display = 'block';
    } else {
        $approvedPhoto.src = default_photo;
        $approvedPhotoDelete.style.display = 'none';
    }
}

function doSearchPartsBUTN() {
    //called when user clicks "Search" within dialog
    var x = document.getElementById("searchPartsINPUT").value;
    var p = document.getElementById("searchPhotoApproval").checked;
    if (!x && !p)
        return;
    $('#searchPartsBUTN').button('loading');
    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "html",
        data: ({
            searchString: x,
            photosApproval: p,
            ajax_request_action: "perform_search"
        }),
        success: writeSearchResults,
        error: showAjaxError,
        type: "POST"
    });
}

function fetchParticipant(badgeid) {
    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "xml",
        data: ({
            badgeid: badgeid,
            ajax_request_action: "fetch_participant"
        }),
        success: fetchParticipantCallback,
        error: showAjaxError,
        type: "GET"
    });
}

function fetchParticipantCallback(data, textStatus, jqXHR) {
    //debugger;
    var node = data.firstChild.firstChild.firstChild;
    curbadgeid = node.getAttribute("badgeid");
    $("#badgeid").val(curbadgeid);
    $("#lname_fname").val(node.getAttribute("lastname") + ", " + node.getAttribute("firstname"));
    $lastname.val(node.getAttribute("lastname")).prop("defaultValue", node.getAttribute("lastname"));
    $firstname.val(node.getAttribute("firstname")).prop("defaultValue", node.getAttribute("firstname"));
    $badgename.val(node.getAttribute("badgename")).prop("defaultValue", node.getAttribute("badgename"));
    $('#resultsDiv').show();
    $('#resultBoxDIV').show();
    hideSearchResults();
    
}

function hideSearchResults() {
    resultsHidden = true;
    $("#searchResultsDIV").hide("fast");
    $("#toggleSearchResultsBUTN").prop("disabled", false).prop("hidden", false);
    $("#toggleText").html("Show");
}

function initializeAdminPhotos() {
    //called when JQuery says AdminParticipants page has loaded
    //debugger;
    default_photo = document.getElementById('default_photo').value;
    approved_dir = default_photo.substr(0, default_photo.lastIndexOf('/') + 1);
    $lastname = $("#lastname");
    $firstname = $("#firstname");
    $badgename = $("#badgename");
    $uploadPhotoStatus = document.getElementById("uploadedPhotoStatus");
    $denyButton = document.getElementById("denyBTN");
    $approvedPhotoDelete = document.getElementById("deleteApprovedPhoto");
    $uploadPhotoDelete = document.getElementById("deleteUploadPhoto");
    $uploadUpdatedPhoto = document.getElementById("updateUploadPhoto");
    $approvePhoto = document.getElementById("ApprovePhoto");
    $denyPhoto = document.getElementById("DenyPhoto");
    $uploadZone = document.getElementById("photoUploadArea");
    $uploadChooseFile = document.getElementById("uploadPhoto");
    $chooseFileName = document.getElementById("chooseFileName");
    $uploadedPhoto = document.getElementById("uploadedPhoto");
    $approvedPhoto = document.getElementById("approvedPhoto");
    $resultBoxDiv = $("#resultBoxDIV");
    $denyReasonDIV = document.getElementById("denyReasonDIV");
    $denyReason = document.getElementById("denyReason");
    $denyOtherText = document.getElementById("denyOtherText");
    $denialReasonSPAN = document.getElementById("denialReasonSPAN");
    $denyDetailsDIV = document.getElementById("denyDetailsDIV");
    $cropBTN = document.getElementById("crop");
    $cropsaveBTN = document.getElementById("save_crop");
    $cropleftBTN = document.getElementById("rotate_left");
    $croprightBTN = document.getElementById("rotate_right");
    $cropcancelBTN = document.getElementById("cancel_crop");
    $('#resultsDiv').hide();
    $('#resultBoxDIV').css("visibility", "hidden");
    $("#unsavedWarningModal").modal({show: false});
    var $toggleSearchResultsBUTN = $("#toggleSearchResultsBUTN");
    $toggleSearchResultsBUTN.click(toggleSearchResultsBUTN);
    $toggleSearchResultsBUTN.prop("disabled", true).prop("hidden", true);
    resultsHidden = true;
    $("#searchPartsBUTN").on('click', doSearchPartsBUTN);
    $("#searchResultsDIV").html("").hide('fast');
    if (fbadgeid) { // signal from page initializer that page was requested to
        // to be preloaded with a participant
        fetchParticipant(fbadgeid);
    }
    $prevBTN = document.getElementById("prevSearchResultBUTN");
    $nextBTN = document.getElementById("nextSearchResultBUTN");

    // photo actions
    if ($uploadChooseFile) {
        $uploadChooseFile.addEventListener("click", function (e) {
            $chooseFileName.value = null;
            $chooseFileName.click();
        });
        $chooseFileName.addEventListener("change", function (e) {
            loaduploadimage(e.target.files[0]);
        });
    }
    if (window.File && window.FileReader && window.FileList && window.Blob) {
        // hover
        $uploadedPhoto.addEventListener("dragenter", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $uploadZone.classList.remove('alert-secondary');
            $uploadZone.classList.add('alert-dark');
        });
        $uploadedPhoto.addEventListener("dragleave", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $uploadZone.classList.remove('alert-dark');
            $uploadZone.classList.add('alert-secondary');
        });
        // upload
        $uploadedPhoto.addEventListener("dragover", function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
        $uploadedPhoto.addEventListener("drop", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $uploadZone.classList.remove('alert-dark');
            $uploadZone.classList.add('alert-secondary');
            if (e.dataTransfer.files.length == 1) {
                f = e.dataTransfer.files[0];
                if (!f.type.match(/image\/(jpeg|png)/i)) {
                    showErrorMessage("Only jpg and png files allowed");
                } else if (f.name.match(/\.(jpg|jpeg|png)$/i))
                    loaduploadimage(f);
                else
                    showErrorMessage("Only jpg and png files allowed");
            } else {
                showErrorMessage("Drag only one picture");
            }

        });
    };

}

function showAjaxError(data, textStatus, jqXHR) {
    uploadlock = false;
    var $resultBoxDIV = $("#resultBoxDIV");
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $resultBoxDIV.html(content).css("visibility", "visible");
    window.scrollTo(0, 0);
}

function showSearchResults() {
    resultsHidden = false;
    $("#searchResultsDIV").show("fast").css("overflow-y", "auto");
    $("#toggleSearchResultsBUTN").prop("disabled", false).prop("hidden", false);
    $("#toggleText").html("Hide");
}

function showUpdateResults(data, textStatus, jqXHR) {
    //ajax success callback function
    $('#updateBUTN').button('reset');
    $updateButton.prop("disabled", true);
    // update the selection list
    var node = data.firstChild.firstChild.firstChild;
    var retbadgeid = node.getAttribute("badgeid");
    var badgeidJQSel = retbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');

    $("#lnameSPAN_" + badgeidJQSel).html(node.getAttribute("lastname") + ", " + node.getAttribute("firstname"));
    $("#lastnameHID_" + badgeidJQSel).val(node.getAttribute("lastname"));
    $("#firstnameHID_" + badgeidJQSel).val(node.getAttribute("firstname"));
    $("#approvalHID_" + badgeidJQSel).val(node.getAttribute("approval"));
    $("#approvalNotesHID_" + badgeidJQSel).val(node.getAttribute("denyOtherText"));
}

function toggleSearchResultsBUTN() {
    $("#searchResultsDIV").slideToggle("fast");
    resultsHidden = !resultsHidden;
    $("#toggleText").html((resultsHidden ? "Show" : "Hide"));
}

function writeSearchResults(data, textStatus, jqXHR) {
    //ajax success callback function
    data_json = new Array();
    data_json["HTML"] = "";
    data_json["rowcount"] = 0;
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }
    console.log(data_json);
 
    $("#searchResultsDIV").html(data_json["HTML"]).show('fast');
    $('#searchPartsBUTN').button('reset');
    searchCount = data_json["rowcount"];
    if (searchCount > 1) {
        if ($prevBTN) {
            $prevBTN.style.display = "block";
            $prevBTN.disabled = true;
        }
        else
            $prevBTN.style.display = "none";

        if ($nextBTN) {
            $nextBTN.style.display = "block";
            $nextBTN.disabled = false;
        }
        else
            $nextBTN.style.display = "none";
        badgeList = data_json["badgeids"];
    } else
        badgeList = null;

    searchIndex = -1;
    showSearchResults();
}

function crop() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    changeCropDisplay(crop_showdirections);
    $uploadUpdatedPhoto.style.display = 'none';

    cropper = new Croppie($uploadedPhoto, {
        boundary: { width: 400, height: 400, 'margin-right': 'auto', 'margin-left': 'auto' },
        enableResize: true,
        enforceBoundary: true,
        enableZoom: true,
        viewport: { width: 400, height: 400, type: 'square' },
        enableOrientation: true,
    });
}

function rotate(deg) {
    if (cropper) {
        cropper.rotate(deg);
    }
};

function cancelcrop() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    changeCropDisplay(crop_showbtn);
    $uploadUpdatedPhoto.style.display = 'block';
};

function savecrop() {
    if (cropper) {
        cropper.result({ type: 'base64', size: 'original', format: 'png', quality: 1, circle: false }).then(function (blob) {
            //console.log(blob);
            $uploadedPhoto.src = blob;
        });
        cropper.destroy();
        cropper = null;
        changeCropDisplay(crop_showbtn);
        $uploadUpdatedPhoto.style.display = 'block';
    }
};

function showErrorMessage(message) {
    content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">` + message + `</div></div></div>`;
    $resultBoxDiv.html(content).css("visibility", "visible");
    document.getElementById("resultBoxDIV").scrollIntoView(false);
};

function transfercomplete(data, textStatus, jqXHR) {
    uploadlock = false;
    message = "";
    //console.log(data);
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;
    // enable delete button
    $uploadPhotoDelete.style.display = 'block';
    $uploadUpdatedPhoto.style.display = 'none';
    changeCropDisplay(crop_showbtn);

    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    console.log("uploSWS photo for badgeid = " + badgeidJQSel);

    // reload default photo
    if (data_json.hasOwnProperty("image")) {
        $uploadedPhoto.src = data_json["image"];
    }

    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        $("#photouploadstatusHID_" + badgeidJQSel).val(data_json["photostatusid"]);
        $("#statustextSPAN_" + badgeidJQSel).text(data_json["photostatus"]);
    }

    // update updated data in HID array
    if (data_json.hasOwnProperty("photoname")) {
        $("#uploadedphotoHID_" + badgeidJQSel).val(data_json["photoname"]);
        $uploadPhotoDelete.style.display = 'block';
        $approvePhoto.style.display = 'block';
        $denyPhoto.style.display = 'block';
    }
    else
        $("#uploadedphotoHID_" + badgeidJQSel).val("");

    $("#reasontextHID_" + badgeidJQSel).val("");
    $("#denialOtherTextHID_" + badgeidJQSel).val("");
    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';
    $denyDetailsDIV.style.display = 'none';
    $denialReasonSPAN.textContent = "";

    if (message != "") {
        if (message.startsWith("Error"))
            alert_type = "alert-danger";
        else
            alert_type = "alert-success";
        content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
        $resultBoxDiv.html(content).css("visibility", "visible");
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    }
}

function starttransfer() {
    if (uploadlock) {
        showErrorMessage("Upload in progress, please wait");
        return false;
    }
    $resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
    uploadlock = true;

    var postdata = {
        ajax_request_action: 'uploadPhoto',
        badgeid: curbadgeid,
        photo: $uploadedPhoto.src
    };

    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "html",
        data: postdata,
        success: transfercomplete,
        error: showAjaxError,
        type: "POST"
    });
};

function deleteuploadedcomplete(data, textStatus, jqXHR) {
    uploadlock = false;
    message = "";
    //console.log(data);
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;
    // disable delete button
    changeCropDisplay(crop_hideall);
    $uploadPhotoDelete.style.display = 'none';
    $approvePhoto.style.display = 'none';
    $denyPhoto.style.display = 'none';
    $uploadUpdatedPhoto.style.display = 'none';
    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';
    $denyDetailsDIV.style.display = 'none';
    $denialReasonSPAN.textContent = "";

    // reload default photo
    if (data_json.hasOwnProperty("image")) {
        $uploadedPhoto.src = data_json["image"];
    }
    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    console.log("deleted photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        $("#photouploadstatusHID_" + badgeidJQSel).val(data_json["photostatus"]);
        $("#statustextSPAN_" + badgeidJQSel).text(data_json["photostatus"]);
    }

    // update updated data in HID array
    $("#uploadedphotoHID_" + badgeidJQSel).val("");
    $("#reasontextHID_" + badgeidJQSel).val("");
    $("#denialOtherTextHID_" + badgeidJQSel).val("");

    if (message != "") {
        if (message.startsWith("Error"))
            alert_type = "alert-danger";
        else
            alert_type = "alert-success"
        content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
        $resultBoxDiv.html(content).css("visibility", "visible");
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    }
}

function deleteuploadedphoto() {
    if (uploadlock) {
        showErrorMessage("Photo change in progress, please wait");
        return false;
    }
    $resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
    uploadlock = true;
    var postdata = {
        ajax_request_action: "delete_uploaded_photo",
        badgeid: curbadgeid
    };
    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "html",
        data: postdata,
        success: deleteuploadedcomplete,
        error: showAjaxError,
        type: "POST"
    });
};

function loaduploadimage(file) {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    if (!(file.type.match('image/jp.*') || file.type.match('image/png.*'))) {
        alert("Only jpeg/jpg or png images allowed");
    }
    else {
        var reader = new FileReader();
        reader.onload = (function (thefile) {
            return function (e) {
                $uploadedPhoto.src = e.target.result;
                $cropBTN.style.display = 'block';
            }
        })(file);

        reader.readAsDataURL(file);
    }

    pickedfile = $chooseFileName.value;
    $uploadUpdatedPhoto.style.display = 'block';
    $approvePhoto.style.display = 'none';
    $denyPhoto.style.display = 'none';
};

function denyphoto() {
    $denyReasonDIV.style.display = 'block';
};

function cancelDeny() {
    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';
};

function enableDeny() {
    var selindex = $denyReason.selectedIndex;
    $denyButton.disabled = selindex == 0;
};

function denycomplete(data, textStatus, jqXHR) {
    uploadlock = false;
    message = "";
    //console.log(data);
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;

    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';

    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    //console.log("denied photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        $("#photouploadstatusHID_" + badgeidJQSel).val(data_json["photostatus"])
        $("#statustextSPAN_" + badgeidJQSel).text(data_json["photostatus"]);
    }

    var othertext = "";
    if (data_json.hasOwnProperty("othertext")) {
        othertext = data_json["othertext"];
        $("#denialOtherTextHID_" + badgeidJQSel).val(othertext);
    }
    else
        $("#denialOtherTextHID_" + badgeidJQSel).val("");

    if (data_json.hasOwnProperty("reasontext")) {
        var reasontext = data_json["reasontext"];
        $('#reasontextHID_' + badgeidJQSel).val(reasontext);
        if (othertext.length > 0)
            reasontext += " (" + othertext + " )";
        $denialReasonSPAN.textContent = reasontext;
    }
    $denyDetailsDIV.style.display = 'block';

    if (message != "") {
        if (message.startsWith("Error"))
            alert_type = "alert-danger";
        else
            alert_type = "alert-success"
        content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
        $resultBoxDiv.html(content).css("visibility", "visible");
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    }
}

function doDeny() {
    if (uploadlock) {
        showErrorMessage("Photo change in progress, please wait");
        return false;
    }
    $resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
    uploadlock = true;
    var postdata = {
        ajax_request_action: "deny_photo",
        badgeid: curbadgeid,
        reasonid: $denyReason.value,
        othertext: $denyOtherText.value
    };
    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "html",
        data: postdata,
        success: denycomplete,
        error: showAjaxError,
        type: "POST"
    });
};

function approvecomplete(data, textStatus, jqXHR) {
    uploadlock = false;
    message = "";
    //console.log(data);
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;

    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';

    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    console.log("approved photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        $("#photouploadstatusHID_" + badgeidJQSel).val(data_json["photostatus"]);
        $("#statustextSPAN_" + badgeidJQSel).text(data_json["photostatus"]);
    }
    if (data_json.hasOwnProperty("approvedphoto")) {
        $approvedPhoto.src = approved_dir + data_json["approvedphoto"];
        $uploadedPhoto.src = default_photo;
        $("#uploadedphotoHID_" + badgeidJQSel).val("");
        $("#denialOtherTextHID_" + badgeidJQSel).val("");
        $('#reasontextHID_' + badgeidJQSel).val("");
        $denialReasonSPAN.textContent = "";
        $denyDetailsDIV.style.display = 'none';
        changeCropDisplay(crop_hideall);
        $uploadPhotoDelete.style.display = 'none';
        $uploadUpdatedPhoto.style.display = 'none';
        $approvePhoto.style.display = 'none';
        $denyPhoto.style.display = 'none';
        $approvedPhotoDelete.style.display = 'block';
    } else {
        $approvedPhoto.src = default_photo;
        $approvedPhotoDelete.style.display = 'none';
    }

    if (message != "") {
        if (message.startsWith("Error"))
            alert_type = "alert-danger";
        else
            alert_type = "alert-success"
        content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
        $resultBoxDiv.html(content).css("visibility", "visible");
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    }
}

function approvephoto() {
    if (uploadlock) {
        showErrorMessage("Photo change in progress, please wait");
        return false;
    }
    $resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
    uploadlock = true;
    var postdata = {
        ajax_request_action: "approve_photo",
        badgeid: curbadgeid,
    };
    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "html",
        data: postdata,
        success: approvecomplete,
        error: showAjaxError,
        type: "POST"
    });
};

function deleteapprovedcomplete(data, textStatus, jqXHR) {
    uploadlock = false;
    message = "";
    //console.log(data);
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;
    // disable delete button
    $approvedPhotoDelete.style.display = 'none';
  
    // reload default photo
    if (data_json.hasOwnProperty("image")) {
        $approvedPhoto.src = data_json["image"];
    }
    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    console.log("deleted approved photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        $("#photouploadstatusHID_" + badgeidJQSel).val(data_json["photostatus"]);
        $("#statustextSPAN_" + badgeidJQSel).text(data_json["photostatus"]);
    }

    // update updated data in HID array
    $("#approveddphotoHID_" + badgeidJQSel).val("");

    if (message != "") {
        if (message.startsWith("Error"))
            alert_type = "alert-danger";
        else
            alert_type = "alert-success"
        content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
        $resultBoxDiv.html(content).css("visibility", "visible");
        document.getElementById("resultBoxDIV").scrollIntoView(false);
    }
}

function deleteapprovedphoto() {
    if (uploadlock) {
        showErrorMessage("Upload in progress, please wait");
        return false;
    }
    $resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
    uploadlock = true;
    var postdata = {
        ajax_request_action: "delete_approved_photo",
        badgeid: curbadgeid,
    };
    $.ajax({
        url: "SubmitAdminPhotos.php",
        dataType: "html",
        data: postdata,
        success: deleteapprovedcomplete,
        error: showAjaxError,
        type: "POST"
    });
};