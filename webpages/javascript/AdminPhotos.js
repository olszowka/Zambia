// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var fbadgeid;
var resultsHidden = true;
var $denyButton = null;
var $prevBTN = null;
var $nextBTN = null;
var searchIndex = 0;
var badgeList = null;
var searchCount = -1;
var $badgeid= null;
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
var $resultsDiv = null;
var $resultBoxDIV = null;
var $searchResultsDIV = null;
var $toggleSearchResultsBUTN = null
var $toggleText = null;
var $denyReasonDIV = null;
var $denyReason = null;
var $denyOtherText = null;
var $denialReasonSPAN = null
var $denyDetailsDIV = null;
var uploadlock = false;
var savedDeleteButtonDisplay = 'none';
var savedUploadButtonDisplay = 'none';
var savedApproveButtonDisplay = 'none';
var savedDenyButtonDisplay = 'none';
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

function setMessage(content) {
    if (content != null)
        $resultBoxDiv.innerHTML = content
    $resultBoxDiv.style.visibility = "visible";
    $resultBoxDiv.scrollIntoView(false);
}

function clearMessage() {
    $resultBoxDiv.innerHTML = "&nbsp;";
    $resultBoxDiv.style.visibility = "hidden";
}

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
    $badgeid.value = document.getElementById("bidSPAN_" + badgeidJQSel).textContent;
    var lnamefname = document.getElementById("lnameSPAN_" + badgeidJQSel).textContent;
    document.getElementById("lname_fname").value = lnamefname
    $badgename.value = document.getElementById("bnameSPAN_" + badgeidJQSel).textContent;
    $uploadPhotoStatus.innerHTML = document.getElementById("statustextSPAN_" + badgeidJQSel).textContent;
   
    $resultsDiv.style.visibility = 'visible';
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';
    $denyReason.selectedIndex = 0;
    clearMessage();
    uploadedPhotoName = document.getElementById("uploadedphotoHID_" + badgeidJQSel).value;
    var reasontext = document.getElementById('reasontextHID_' + badgeidJQSel).value;
    if (reasontext.length > 0) {
        $denyDetailsDIV.style.display = 'block';
        
        var denyothertext = document.getElementById('denialOtherTextHID_' + badgeidJQSel).value;
        if (denyothertext.length > 0) {
            $denyOtherText.value = denyothertext;
            reasontext += " (" + denyothertext + ")";
        }
        $denialReasonSPAN.textContent = reasontext;
    } else {
        $denyDetailsDIV.style.display = 'none';
        $denialReasonSPAN.textContent = "";
    }
    if (uploadedPhotoName.length > 0) {
        //console.log("loading photo " + uploadedPhotoName);
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
    approvedPhotoName = document.getElementById("approvedphotoHID_" + badgeidJQSel).value;
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
    $badgeid.value = curbadgeid;
    document.getElementById("lname_fname").value = node.getAttribute("lastname") + ", " + node.getAttribute("firstname");
    $badgename.value = node.getAttribute("badgename");
    $resultsDiv.style.visibility = 'visible';
    setMessage(null);
    hideSearchResults();
}

function hideSearchResults() {
    resultsHidden = true;
    $searchResultsDIV.style.display = 'none';
    $toggleSearchResultsBUTN.disabled = false;
    $toggleSearchResultsBUTN.style.display = 'block';
    $toggleText.innerHTML = "Show";
}

function initializeAdminPhotos() {
    //called when JQuery says AdminParticipants page has loaded
    //debugger;
    default_photo = document.getElementById('default_photo').value;
    approved_dir = default_photo.substr(0, default_photo.lastIndexOf('/') + 1);
    $badgeid = document.getElementById("badgeid");
    $badgename = document.getElementById("badgename");
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
    $resultBoxDiv = document.getElementById("resultBoxDIV");
    $resultsDiv = document.getElementById("resultsDiv");
    $searchResultsDIV = document.getElementById("searchResultsDIV");
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
    $resultsDiv.style.visibility = "hidden";
    clearMessage();
    $toggleText = document.getElementById("toggleText");
    $toggleSearchResultsBUTN = document.getElementById("toggleSearchResultsBUTN");
    $toggleSearchResultsBUTN.onclick = toggleSearchResultsBUTN;
    $toggleSearchResultsBUTN.disabled = true;
    $toggleSearchResultsBUTN.style.display = 'none';
    //$toggleSearchResultsBUTN.prop("disabled", true).prop("hidden", true);
    resultsHidden = true;
    document.getElementById("searchPartsBUTN").onclick = doSearchPartsBUTN;
    $searchResultsDIV.innerHTML = "";
    $searchResultsDIV.style.display = 'none';
    if (fbadgeid) { // signal from page initializer that page was requested to
        // to be preloaded with a participant
        fetchParticipant(fbadgeid);
    }
    $prevBTN = document.getElementById("prevSearchResultBUTN");
    $nextBTN = document.getElementById("nextSearchResultBUTN");

    // photo actions if configured for photos
    if ($uploadChooseFile) {
        $uploadChooseFile.addEventListener("click", function (e) {
            $chooseFileName.value = null;
            $chooseFileName.click();
        });
        $chooseFileName.addEventListener("change", function (e) {
            loaduploadimage(e.target.files[0]);
        });
    }
    // if browser supports drag and drop
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

function showMessage(message) {
    if (message == "")
        return;
    if (message.startsWith("Error"))
        alert_type = "alert-danger";
    else
        alert_type = "alert-success";
    content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';

    setMessage(content);
};

function showAjaxError(data, textStatus, jqXHR) {
    uploadlock = false;
    if (data && data.responseText)
        myPhoto.showErrorMessage(data.responseText);
    else
        myPhoto.showErrorMessage("An error occurred on the server.");
};

function showSearchResults() {
    resultsHidden = false;
    $searchResultsDIV.style.overflowY = 'auto';
    $searchResultsDIV.style.overflowX = 'hidden';
    $searchResultsDIV.style.display = 'block';
    $toggleSearchResultsBUTN.style.disabled = 'block';
    $toggleSearchResultsBUTN.disabled = false;
    $toggleText.innerHTML = "Hide";
}

function toggleSearchResultsBUTN() {
    $searchResultsDIV.style.display = $searchResultsDIV.style.display == 'block' ? 'none' : 'block';
    resultsHidden = !resultsHidden;
    $toggleText.innerHTML = resultsHidden ? "Show" : "Hide";
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
    //console.log(data_json);
    $searchResultsDIV.innerHTML = data_json["HTML"];
    $searchResultsDIV.style.display = 'block';
    $('#searchPartsBUTN').button('reset');
    searchCount = data_json["rowcount"];
    if (searchCount > 1) {
        if ($prevBTN) {
            $prevBTN.style.display = "block";
            $prevBTN.disabled = true;
        }

        if ($nextBTN) {
            $nextBTN.style.display = "block";
            $nextBTN.disabled = false;
        }
        badgeList = data_json["badgeids"];
    } else {
        badgeList = null;
        if ($prevBTN) {
            $prevBTN.style.display = "none";
            $prevBTN.disabled = true;
        }

        if ($nextBTN) {
            $nextBTN.style.display = "none";
            $nextBTN.disabled = true;
        }
    }

    searchIndex = -1;
    showSearchResults();
}

function crop() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    clearMessage();
    changeCropDisplay(crop_showdirections);
    savedUploadButtonDisplay = $uploadUpdatedPhoto.style.display;
    savedDeleteButtonDisplay = $uploadPhotoDelete.style.display;
    savedApproveButtonDisplay = $approvedPhoto.style.display;
    savedDenyButtonDisplay = $denyPhoto.style.display;
    $uploadUpdatedPhoto.style.display = 'none';
    $uploadPhotoDelete.style.display = 'none';
    $approvePhoto.style.display = 'none';
    $denyPhoto.style.display = 'none';


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
    $uploadUpdatedPhoto.style.display = savedUploadButtonDisplay;
    $uploadPhotoDelete.style.display = savedDeleteButtonDisplay;
    $denyPhoto.style.display = savedDenyButtonDisplay;
    $approvePhoto.style.display = savedApproveButtonDisplay;
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
        $uploadPhotoDelete.style.display = savedDeleteButtonDisplay;
        $denyPhoto.style.display = savedDenyButtonDisplay;
        $approvePhoto.style.display = savedApproveButtonDisplay;
    }
};

function showErrorMessage(message) {
    content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">` + message + `</div></div></div>`;
    myPhoto.setMessage(content);
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

    //console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;
    // enable delete button
    $uploadPhotoDelete.style.display = 'block';
    $uploadUpdatedPhoto.style.display = 'none';
    changeCropDisplay(crop_showbtn);

    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    //console.log("upload photo for badgeid = " + badgeidJQSel);

    // reload default photo
    if (data_json.hasOwnProperty("image")) {
        $uploadedPhoto.src = data_json["image"];
    }

    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        document.getElementById("photouploadstatusHID_" + badgeidJQSel).value = data_json["photostatusid"];
        document.getElementById("statustextSPAN_" + badgeidJQSel).textContent = data_json["photostatus"];
    }

    // update updated data in HID array
    if (data_json.hasOwnProperty("photoname")) {
        document.getElementById("uploadedphotoHID_" + badgeidJQSel).value = data_json["photoname"];
        $uploadPhotoDelete.style.display = 'block';
        $approvePhoto.style.display = 'block';
        $denyPhoto.style.display = 'block';
    }
    else
        document.getElementById("uploadedphotoHID_" + badgeidJQSel).value = "";

    document.getElementById("reasontextHID_" + badgeidJQSel).value = "";
    document.getElementById("denialOtherTextHID_" + badgeidJQSel).value = "";
    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';
    $denyDetailsDIV.style.display = 'none';
    $denialReasonSPAN.textContent = "";

    showMessage(message);
}

function starttransfer() {
    if (uploadlock) {
        showErrorMessage("Upload in progress, please wait");
        return false;
    }
    clearMessage();

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

    //console.log(data_json);
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
    //console.log("deleted photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        document.getElementById("photouploadstatusHID_" + badgeidJQSel).value = data_json["photostatus"];
        document.getElementById("statustextSPAN_" + badgeidJQSel).textContent = data_json["photostatus"];
    }

    // update updated data in HID array
    document.getElementById("uploadedphotoHID_" + badgeidJQSel).value = "";
    document.getElementById("reasontextHID_" + badgeidJQSel).value = "";
    document.getElementById("denialOtherTextHID_" + badgeidJQSel).value = "";

    showMessage(message);
}

function deleteuploadedphoto() {
    if (uploadlock) {
        showErrorMessage("Photo change in progress, please wait");
        return false;
    }
    clearMessage();
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
        $uploadPhotoDelete.style.display = savedDeleteButtonDisplay;
        $denyPhoto.style.display = savedDenyButtonDisplay;
        $approvePhoto.style.display = savedApproveButtonDisplay;
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
    clearMessage();
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

    //console.log(data_json);
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
        document.getElementById("photouploadstatusHID_" + badgeidJQSel).value = data_json["photostatus"];
        document.getElementById("statustextSPAN_" + badgeidJQSel).textContent = data_json["photostatus"];
    }

    var othertext = "";
    if (data_json.hasOwnProperty("othertext"))
        othertext = data_json["othertext"];

    document.getElementById("denialOtherTextHID_" + badgeidJQSel).value = othertext;

    if (data_json.hasOwnProperty("reasontext")) {
        var reasontext = data_json["reasontext"];
        document.getElementById('reasontextHID_' + badgeidJQSel).value = reasontext;
        if (othertext.length > 0)
            reasontext += " (" + othertext + " )";
        $denialReasonSPAN.textContent = reasontext;
    }
    $denyDetailsDIV.style.display = 'block';

    showMessage(message);
}

function doDeny() {
    if (uploadlock) {
        showErrorMessage("Photo change in progress, please wait");
        return false;
    }
    clearMessage();
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

    //console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;

    $denyOtherText.value = "";
    $denyReason.selectedIndex = 0;
    $denyButton.disabled = true;
    $denyReasonDIV.style.display = 'none';

    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    //console.log("approved photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        document.getElementById("photouploadstatusHID_" + badgeidJQSel).value = data_json["photostatus"];
        document.getElementById("statustextSPAN_" + badgeidJQSel).textContent = data_json["photostatus"];
    }
    if (data_json.hasOwnProperty("approvedphoto")) {
        $approvedPhoto.src = approved_dir + data_json["approvedphoto"];
        $uploadedPhoto.src = default_photo;
        document.getElementById("uploadedphotoHID_" + badgeidJQSel).value = "";
        document.getElementById("denialOtherTextHID_" + badgeidJQSel).value = "";
        document.getElementById('reasontextHID_' + badgeidJQSel).value = ""
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

    showMessage(message);
}

function approvephoto() {
    if (uploadlock) {
        showErrorMessage("Photo change in progress, please wait");
        return false;
    }
    clearMessage();
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

    //console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;
    // disable delete button
    $approvedPhotoDelete.style.display = 'none';
  
    // reload default photo
    if (data_json.hasOwnProperty("image")) {
        $approvedPhoto.src = data_json["image"];
    }
    var badgeidJQSel = curbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    //console.log("deleted approved photo for badgeid = " + badgeidJQSel);
    if (data_json.hasOwnProperty("photostatus")) {
        $uploadPhotoStatus.innerHTML = data_json["photostatus"];
        document.getElementById("photouploadstatusHID_" + badgeidJQSel).value = data_json["photostatus"];
        document.getElementById("statustextSPAN_" + badgeidJQSel).textConent = data_json["photostatus"];
    }

    // update updated data in HID array
    document.getElementById("approveddphotoHID_" + badgeidJQSel).value = "";

    showMessage(message);
}

function deleteapprovedphoto() {
    if (uploadlock) {
        showErrorMessage("Upload in progress, please wait");
        return false;
    }
    clearMessage();
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