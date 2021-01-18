// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var badgenameDirty = false;
var bioDirty = false;
var emailDirty = false;
var firstnameDirty = false;
var interestedDirty = false;
var lastnameDirty = false;
var passwordDirtyAndReady = false;
var phoneDirty = false;
var postaddress1Dirty = false;
var postaddress2Dirty = false;
var postcityDirty = false;
var postcountryDirty = false;
var poststateDirty = false;
var postzipDirty = false;
var pubsnameDirty = false;
var rolesDirty = false;
var staffnotesDirty = false;
var originalInterested = "0";
var fbadgeid;
var resultsHidden = true;
var max_bio_len = 500;
var $interested;
var $bio;
var $password;
var $cpassword;
var $staffnotes;
var $pubsname;
var $lastname;
var $firstname;
var $badgename;
var $phone;
var $email;
var $postaddress1;
var $postaddress2;
var $postcity;
var $poststate;
var $postzip;
var $postcountry;
var $passwordsDontMatch;
var $updateButton;
var saveNewBadgeId;

function isDirty(override) {
    //called when user clicks "Search for participants" on the page
    if (override === undefined) {
        override = false;
    }
    if (!override && (badgenameDirty || bioDirty || emailDirty || firstnameDirty || interestedDirty || lastnameDirty ||
        $password.val() || phoneDirty || postaddress1Dirty || postaddress2Dirty || postcityDirty || poststateDirty ||
        postzipDirty || postcountryDirty || pubsnameDirty || rolesDirty || staffnotesDirty)) {
        $("#unsavedWarningModal").modal('show');
        $("#cancelOpenSearchBUTN").blur();
        return true;
    }
    if (override) {
        $("#unsavedWarningModal").modal('hide');
    }
    return false;
}

function chooseParticipant(badgeid, override) {
    //debugger;
    if (isDirty(override)) {
        saveNewBadgeId = badgeid;
        return;
    }
    var badgeidJQSel = badgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    hideSearchResults();
    $("#badgeid").val($("#bidSPAN_" + badgeidJQSel).text());
    var lastname = $("#lastnameHID_" + badgeidJQSel).val();
    $lastname.val(lastname).prop("defaultValue", lastname).prop("readOnly", false);
    var firstname = $("#firstnameHID_" + badgeidJQSel).val();
    $firstname.val(firstname).prop("defaultValue", firstname).prop("readOnly", false);
    var phone = $("#phoneHID_" + badgeidJQSel).val();
    $phone.val(phone).prop("defaultValue", phone).prop("readOnly", false);
    var email = $("#emailHID_" + badgeidJQSel).val();
    $email.val(email).prop("defaultValue", email).prop("readOnly", false);
    var postaddress1 = $("#postaddress1HID_" + badgeidJQSel).val();
    $postaddress1.val(postaddress1).prop("defaultValue", postaddress1).prop("readOnly", false);
    var postaddress2 = $("#postaddress2HID_" + badgeidJQSel).val();
    $postaddress2.val(postaddress2).prop("defaultValue", postaddress2).prop("readOnly", false);
    var postcity = $("#postcityHID_" + badgeidJQSel).val();
    $postcity.val(postcity).prop("defaultValue", postcity).prop("readOnly", false);
    var poststate = $("#poststateHID_" + badgeidJQSel).val();
    $poststate.val(poststate).prop("defaultValue", poststate).prop("readOnly", false);
    var postzip = $("#postzipHID_" + badgeidJQSel).val();
    $postzip.val(postzip).prop("defaultValue", postzip).prop("readOnly", false);
    var postcountry = $("#postcountryHID_" + badgeidJQSel).val();
    $postcountry.val(postcountry).prop("defaultValue", postcountry).prop("readOnly", false);
    $("#lname_fname").val($("#lnameSPAN_" + badgeidJQSel).text());
    $badgename.val($("#bnameSPAN_" + badgeidJQSel).text());
    var pubsname = $("#pnameSPAN_" + badgeidJQSel).text();
    $pubsname.val(pubsname).prop("defaultValue", pubsname).prop("readOnly", false);
    $('#warnName').html(pubsname);
    $('#warnNewBadgeID').html(badgeid);
    originalInterested = $("#interestedHID_" + badgeidJQSel).val();
    if (!originalInterested) {
        originalInterested = "0";
    }
    $interested.val(originalInterested);
    $interested.prop("disabled", false);
    var bio = $("#bioHID_" + badgeidJQSel).val();
    $bio.val(bio).prop("defaultValue", bio).prop("readOnly", false);
    var staffnotes = $("#staffnotesHID_" + badgeidJQSel).val();
    $("#staffnotes").val(staffnotes).prop("defaultValue", staffnotes).prop("readOnly", false);
    $password.val("").prop("readOnly", false);
    $cpassword.val("").prop("readOnly", false);
    badgenameDirty = false;
    bioDirty = false;
    emailDirty = false;
    firstnameDirty = false;
    interestedDirty = false;
    lastnameDirty = false;
    passwordDirtyAndReady = false;
    phoneDirty = false;
    postaddress1Dirty = false;
    postaddress2Dirty = false;
    postcityDirty = false;
    postcountryDirty = false;
    poststateDirty = false;
    postzipDirty = false;
    pubsnameDirty = false;
    rolesDirty = false;
    staffnotesDirty = false;
    $('#resultsDiv').show();
    $updateButton.prop("disabled", true);
    $("#resultBoxDIV").html("").hide();
    $passwordsDontMatch.hide();
    max_bio_len = document.getElementById("bio").dataset.maxLength;
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: ({
            badgeid: badgeid,
            ajax_request_action: "fetch_user_perm_roles"
        }),
        success: fetchUserPermRolesCallback,
        error: showAjaxError,
        type: "POST"
    });
}

function doSearchPartsBUTN() {
    //called when user clicks "Search" within dialog
    var x = document.getElementById("searchPartsINPUT").value;
    if (!x)
        return;
    $('#searchPartsBUTN').button('loading');
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: ({
            searchString: x,
            ajax_request_action: "perform_search"
        }),
        success: writeSearchResults,
        error: showAjaxError,
        type: "POST"
    });
}

function fetchParticipant(badgeid) {
    $.ajax({
        url: "SubmitAdminParticipants.php",
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
    $("#badgeid").val(node.getAttribute("badgeid"));
    $("#lname_fname").val(node.getAttribute("lastname") + ", " + node.getAttribute("firstname"));
    $lastname.val(node.getAttribute("lastname")).prop("defaultValue", node.getAttribute("lastname")).prop("readOnly", false);
    $firstname.val(node.getAttribute("firstname")).prop("defaultValue", node.getAttribute("firstname")).prop("readOnly", false);
    $phone.val(node.getAttribute("phone")).prop("defaultValue", node.getAttribute("phone")).prop("readOnly", false);
    $email.val(node.getAttribute("email")).prop("defaultValue", node.getAttribute("email")).prop("readOnly", false);
    $postaddress1.val(node.getAttribute("postaddress1")).prop("defaultValue", node.getAttribute("postaddress1")).prop("readOnly", false);
    $postaddress2.val(node.getAttribute("postaddress2")).prop("defaultValue", node.getAttribute("postaddress2")).prop("readOnly", false);
    $postcity.val(node.getAttribute("postcity")).prop("defaultValue", node.getAttribute("postcity")).prop("readOnly", false);
    $poststate.val(node.getAttribute("poststate")).prop("defaultValue", node.getAttribute("poststate")).prop("readOnly", false);
    $postzip.val(node.getAttribute("postzip")).prop("defaultValue", node.getAttribute("postzip")).prop("readOnly", false);
    $postcountry.val(node.getAttribute("postcountry")).prop("defaultValue", node.getAttribute("postcountry")).prop("readOnly", false);
    $badgename.val(node.getAttribute("badgename")).prop("defaultValue", node.getAttribute("badgename")).prop("readOnly", false);
    $pubsname.val(node.getAttribute("pubsname")).prop("defaultValue", node.getAttribute("pubsname")).prop("readOnly", false);
    originalInterested = node.getAttribute("interested");
    if (!originalInterested)
        originalInterested = 0;
    $interested.val(originalInterested);
    $interested.prop("disabled", false);
    $bio.val(node.getAttribute("bio")).prop("defaultValue", node.getAttribute("bio"));
    $staffnotes.val(node.getAttribute("staff_notes")).prop("defaultValue", node.getAttribute("staff_notes")).prop("readOnly", false);
    $password.prop("readOnly", false);
    $password.val("");
    $cpassword.prop("readOnly", false);
    $cpassword.val("");
    badgenameDirty = false;
    bioDirty = false;
    emailDirty = false;
    firstnameDirty = false;
    interestedDirty = false;
    lastnameDirty = false;
    passwordDirtyAndReady = false;
    phoneDirty = false;
    postaddress1Dirty = false;
    postaddress2Dirty = false;
    postcityDirty = false;
    postcountryDirty = false;
    poststateDirty = false;
    postzipDirty = false;
    pubsnameDirty = false;
    rolesDirty = false;
    staffnotesDirty = false;
    $('#resultsDiv').show();
    $('#resultBoxDIV').show();
    $updateButton.prop("disabled", true);
    $passwordsDontMatch.hide();
    hideSearchResults();
}

function fetchUserPermRolesCallback(data, textStatus, jqXHR) {
    //ajax success callback function
    $("#role-container").html(data);
}

function getUpdateResults(data, textStatus, jqXHR) {
    $("#resultBoxDIV").html(data);
    $('#resultBoxDIV').show();
    window.scrollTo(0, 0);
    setTimeout(function () {
        $updateButton.button().attr("disabled", "disabled");
    }, 0);
    $updateButton.prop("disabled", true);
    var badgeid = $("#badgeid").val();
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "xml",
        data: ({
            badgeid: badgeid,
            ajax_request_action: "fetch_participant"
        }),
        success: showUpdateResults,
        error: showAjaxError,
        type: "GET"
    });
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: ({
            badgeid: badgeid,
            ajax_request_action: "fetch_user_perm_roles"
        }),
        success: fetchUserPermRolesCallback,
        error: showAjaxError,
        type: "POST"
    });
}

function hideSearchResults() {
    resultsHidden = true;
    $("#searchResultsDIV").hide("fast");
    $("#toggleSearchResultsBUTN").prop("disabled", false).prop("hidden", false);
    $("#toggleText").html("Show");
}

function initializeAdminParticipants() {
    //called when JQuery says AdminParticipants page has loaded
    //debugger;
    $interested = $("#interested");
    $bio = $("#bio");
    $password = $("#password");
    $cpassword = $("#cpassword");
    $staffnotes = $("#staffnotes");
    $pubsname = $("#pubsname");
    $lastname = $("#lastname");
    $firstname = $("#firstname");
    $badgename = $("#badgename");
    $phone = $("#phone");
    $email = $("#email");
    $postaddress1 = $("#postaddress1");
    $postaddress2 = $("#postaddress2");
    $postcity = $("#postcity");
    $poststate = $("#poststate");
    $postzip = $("#postzip");
    $postcountry = $("#postcountry");
    $passwordsDontMatch = $("#passwordsDontMatch");
    $updateButton = $("#updateBUTN");
    $passwordsDontMatch.hide();
    $('#resultsDiv').hide();
    $('#resultBoxDIV').hide();
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
    $("#adminParticipantsForm").on("input", ".mycontrol", processChange);
}

function loadNewParticipant() {
    chooseParticipant(saveNewBadgeId, true);
    return true;
}

function processChange() {
    var $target = $(this);
    var targetId = $target.attr("id");
    switch (targetId) {
        case 'password':
        case 'cpassword':
            var password = $password.val();
            var cpassword = $cpassword.val();
            if (password || cpassword) {
                if (password === cpassword) {
                    $passwordsDontMatch.hide();
                    passwordDirtyAndReady = true;
                } else {
                    $passwordsDontMatch.show();
                    passwordDirtyAndReady = false;
                }
            } else {
                $passwordsDontMatch.hide();
                passwordDirtyAndReady = false;
            }
            break;
        case 'interested':
            interestedDirty = ($interested.val() !== originalInterested);
            break;
        case 'bio':
            bioDirty = ($bio.val() !== $bio.prop("defaultValue"));
            break;
        case 'staffnotes':
            staffnotesDirty = ($staffnotes.val() !== $staffnotes.prop("defaultValue"));
            break;
        case 'pubsname':
            pubsnameDirty = ($staffnotes.val() !== $staffnotes.prop("defaultValue"));
            break;
        case 'lastname':
            lastnameDirty = ($lastname.val() !== $lastname.prop("defaultValue"));
            break;
        case 'firstname':
            firstnameDirty = ($firstname.val() !== $firstname.prop("defaultValue"));
            break;
        case 'badgename':
            badgenameDirty = ($badgename.val() !== $badgename.prop("defaultValue"));
            break;
        case 'phone':
            phoneDirty = ($phone.val() !== $phone.prop("defaultValue"));
            break;
        case 'email':
            emailDirty = ($email.val() !== $email.prop("defaultValue"));
            break;
        case 'postaddress1':
            postaddress1Dirty = ($postaddress1.val() !== $postaddress1.prop("defaultValue"));
            break;
        case 'postaddress2':
            postaddress2Dirty = ($postaddress2.val() !== $postaddress2.prop("defaultValue"));
            break;
        case 'postcity':
            postcityDirty = ($postcity.val() !== $postcity.prop("defaultValue"));
            break;
        case 'poststate':
            poststateDirty = ($poststate.val() !== $poststate.prop("defaultValue"));
            break;
        case 'postzip':
            postzipDirty = ($postzip.val() !== $postzip.prop("defaultValue"));
            break;
        case 'postcountry':
            postcountryDirty = ($postcountry.val() !== $postcountry.prop("defaultValue"));
            break;
        default:
            if ($target.is(".tag-chk")) {
                rolesDirty = false;
                $(".tag-chk").each(function() {
                    $checkbox = $(this);
                    if ($checkbox.is(":checked") !== $checkbox.prop("defaultChecked")) {
                        rolesDirty = true;
                        return false;
                    }
                });
            }
    }
    if (passwordDirtyAndReady || interestedDirty || bioDirty || staffnotesDirty || pubsnameDirty || lastnameDirty ||
        firstnameDirty || badgenameDirty || phoneDirty || emailDirty || postaddress1Dirty || postaddress2Dirty ||
        postcityDirty || poststateDirty || postzipDirty || postcountryDirty || rolesDirty) {

        $updateButton.prop("disabled", false);
    } else {
        $updateButton.prop("disabled", true);
    }
}

function showAjaxError(data, textStatus, jqXHR) {
    var $resultBoxDIV = $("#resultBoxDIV");
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $resultBoxDIV.html(content).show();
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
    badgenameDirty = false;
    bioDirty = false;
    emailDirty = false;
    firstnameDirty = false;
    interestedDirty = false;
    lastnameDirty = false;
    passwordDirtyAndReady = false;
    phoneDirty = false;
    postaddress1Dirty = false;
    postaddress2Dirty = false;
    postcityDirty = false;
    postcountryDirty = false;
    poststateDirty = false;
    postzipDirty = false;
    pubsnameDirty = false;
    rolesDirty = false;
    staffnotesDirty = false;
    $password.val("");
    $cpassword.val("");
    $('#updateBUTN').button('reset');
    $updateButton.prop("disabled", true);
    originalInterested = $interested.val();
    // update the selection list
    var node = data.firstChild.firstChild.firstChild;
    var retbadgeid = node.getAttribute("badgeid");
    var badgeidJQSel = retbadgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');

    $("#lnameSPAN_" + badgeidJQSel).html(node.getAttribute("lastname") + ", " + node.getAttribute("firstname"));
    $("#lastnameHID_" + badgeidJQSel).val(node.getAttribute("lastname"));
    $("#firstnameHID_" + badgeidJQSel).val(node.getAttribute("firstname"));
    $("#phoneHID_" + badgeidJQSel).val(node.getAttribute("phone"));
    $("#emailHID_" + badgeidJQSel).val(node.getAttribute("email"));
    $("#postaddress1HID_" + badgeidJQSel).val(node.getAttribute("postaddress1"));
    $("#postaddress2HID_" + badgeidJQSel).val(node.getAttribute("postaddress2"));
    $("#postcityHID_" + badgeidJQSel).val(node.getAttribute("postcity"));
    $("#poststateHID_" + badgeidJQSel).val(node.getAttribute("poststate"));
    $("#postzipHID_" + badgeidJQSel).val(node.getAttribute("postzip"));
    $("#postcountryHID_" + badgeidJQSel).val(node.getAttribute("postcountry"));
    $("#pnameSPAN_" + badgeidJQSel).html(node.getAttribute("pubsname"));
    $("#bnameSPAN_" + badgeidJQSel).html(node.getAttribute("badgename"));
    $("#interestedHID_" + badgeidJQSel).originalInterested = node.getAttribute("interested");
    $("#bioHID_" + badgeidJQSel).val(node.getAttribute("bio"));
    $("#staffnotesHID_" + badgeidJQSel).val(node.getAttribute("staff_notes"));
    $bio.val(node.getAttribute("bio"));
}

function toggleSearchResultsBUTN() {
    $("#searchResultsDIV").slideToggle("fast");
    resultsHidden = !resultsHidden;
    $("#toggleText").html((resultsHidden ? "Show" : "Hide"));
}

function updateBUTTON() {
    //debugger;
    if (!validateBioCharacterLength()) {
        return;
    }
    $('#updateBUTN').button('loading');
    var postdata = {
        ajax_request_action: "update_participant",
        badgeid: $("#badgeid").val()
    };
    if (passwordDirtyAndReady) {
        postdata.password = $password.val();
    }
    if (bioDirty) {
        postdata.bio = $bio.val();
    }
    if (pubsnameDirty) {
        postdata.pubsname = $pubsname.val();
    }
    if (staffnotesDirty) {
        postdata.staffnotes = $staffnotes.val();
    }
    if (interestedDirty) {
        postdata.interested = $interested.val();
    }
    if (lastnameDirty) {
        postdata.lastname = $lastname.val();
    }
    if (firstnameDirty) {
        postdata.firstname = $firstname.val();
    }
    if (badgenameDirty) {
        postdata.badgename = $badgename.val();
    }
    if (phoneDirty) {
        postdata.phone = $phone.val();
    }
    if (emailDirty) {
        postdata.email = $email.val();
    }
    if (postaddress1Dirty) {
        postdata.postaddress1 = $postaddress1.val();
    }
    if (postaddress2Dirty) {
        postdata.postaddress2 = $postaddress2.val();
    }
    if (postcityDirty) {
        postdata.postcity = $postcity.val();
    }
    if (poststateDirty) {
        postdata.poststate = $poststate.val();
    }
    if (postzipDirty) {
        postdata.postzip = $postzip.val();
    }
    if (postcountryDirty) {
        postdata.postcountry = $postcountry.val();
    }
    if (rolesDirty) {
        var rolesToAdd = [];
        var rolesToDelete = [];
        $(".tag-chk").each(function() {
           $check = $(this);
           checked = $check.is(":checked");
           defaultChecked = $check.prop("defaultChecked");
           if (checked && !defaultChecked) {
               rolesToAdd.push($check.val());
           } else if (!checked && defaultChecked) {
               rolesToDelete.push($check.val());
           }
        });
        if (rolesToAdd.length > 0) {
            postdata.rolesToAdd = rolesToAdd;
        }
        if (rolesToDelete.length > 0) {
            postdata.rolesToDelete = rolesToDelete;
        }
    }
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: postdata,
        success: getUpdateResults,
        error: showAjaxError,
        type: "POST"
    });
}

function validateBioCharacterLength() {
    if ($bio.val().length > max_bio_len) {
        alert("Bio too long at " + count + "; " + max_bio_len + " characters allowed.");
        return false;
    }
    return true;
}

function writeSearchResults(data, textStatus, jqXHR) {
    //ajax success callback function
    $("#searchResultsDIV").html(data).show('fast');
    $('#searchPartsBUTN').button('reset');
    showSearchResults();
}

