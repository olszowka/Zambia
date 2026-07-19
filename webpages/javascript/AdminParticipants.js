// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
var badgenameDirty = false;
var bioDirty = false;
var htmlbioused = false;
var emailDirty = false;
var firstnameDirty = false;
var interestedDirty = false;
var lastnameDirty = false;
var name_for_sortingDirty = false;
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
var tagsDirty = false;
var originalInterested = "0";
var fbadgeid;
var curbadgeid;
var resultsHidden = true;
var max_bio_len = 500;
var mce_running = false;
var interestedElem;
var bioElem;
var htmlbioElem;
var passwordElem;
var cpasswordElem;
var staffnotesElem;
var $pubsname;
var $name_for_sorting;
var $lastname;
var $firstname;
var $badgename;
var phoneElemArr;
var $email;
var $postaddress1;
var $postaddress2;
var $postcity;
var $poststate;
var $postzip;
var $postcountry;
var $passwordsDontMatch;
var updateButton;
var $showSurveyDiv;
var $showSurveyBtn;
var saveNewBadgeId;
var prevBtnArr = [];
var nextBtnArr = [];
var searchIndex = 0;
var badgeList = null;
var searchCount = -1;
var unsavedWarningModal;
var toggleSearchResultsBtnArr = [];

function setButtonLoading(btn) {
    if (!(btn.dataset?.originalHtml)) {
        btn.dataset.originalHtml = btn.innerHTML;
    }
    btn.disabled = true;
    btn.innerHTML = btn.dataset.loadingText;
}

function resetButtonLoading(btn) {
    btn.disabled = false;
    btn.innerHTML = btn.dataset?.originalHtml;
}

function isDirty(override) {
    //called when user clicks "Search for participants" on the page
    if (override === undefined) {
        override = false;
    }
    if (!override && (badgenameDirty || bioDirty || emailDirty || firstnameDirty || interestedDirty || lastnameDirty ||
        passwordElem?.value || phoneDirty || postaddress1Dirty || postaddress2Dirty || postcityDirty || poststateDirty ||
        postzipDirty || postcountryDirty || pubsnameDirty || name_for_sortingDirty || rolesDirty || staffnotesDirty || tagsDirty)) {
        unsavedWarningModal.show();
        $("#cancelOpenSearchBUTN").blur();
        return true;
    }
    if (override) {
        unsavedWarningModal.hide();
    }
    return false;
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
    if (isDirty(override)) {
        saveNewBadgeId = badgeid;
        return;
    }

    if (mce_running) {
        tinymce.remove();
        mce_running = false;
    }
    curbadgeid = badgeid;
    var badgeidJQSel = badgeid.replace(/[']/g, "\\'").replace(/["]/g, '\\"');
    hideSearchResults();
    if (badgeList) {
        searchIndex = badgeList.indexOf(badgeid, 0);
        prevBtnArr.forEach((prevBtn) => prevBtn.disabled = searchIndex < 1);
        nextBtnArr.forEach((nextBtn) => nextBtn.disabled = searchIndex == (badgeList.length - 1));
    }
    $("#badgeid").val($("#bidSPAN_" + badgeidJQSel).text());
    var lastname = $("#lastnameHID_" + badgeidJQSel).val();
    $lastname.val(lastname).prop("defaultValue", lastname).prop("readOnly", false);
    var firstname = $("#firstnameHID_" + badgeidJQSel).val();
    $firstname.val(firstname).prop("defaultValue", firstname).prop("readOnly", false);
    var phone = $("#phoneHID_" + badgeidJQSel).val();
    phoneElemArr.forEach((elem) => {
        elem.value = phone;
        elem.defaultValue = phone;
        elem.readOnly = false;
    });
    var email = $("#emailHID_" + badgeidJQSel).val();
    $email.val(email).prop("defaultValue", email);
    if (!$email.data("readonly")) {
        $email.prop("readOnly", false);
    }
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
    regtypeInpArr.forEach((inp) => (inp.value = document.getElementById('regmessageHID_' + badgeidJQSel)?.value));
    $("#lname_fname").val($("#lnameSPAN_" + badgeidJQSel).text());
    var badgename = $("#bnameSPAN_" + badgeidJQSel).text();
    $badgename.val(badgename).prop("defaultValue", badgename).prop("readOnly", false);
    var pubsname = $("#pnameSPAN_" + badgeidJQSel).text();
    $pubsname.val(pubsname).prop("defaultValue", pubsname).prop("readOnly", false);
    var name_for_sorting = $("#name_for_sortingHID_" + badgeidJQSel).val();
    $name_for_sorting.val(name_for_sorting).prop("defaultValue", name_for_sorting).prop("readOnly", false);
    $('#warnName').html(pubsname);
    $('#warnNewBadgeID').html(badgeid);
    originalInterested = $("#interestedHID_" + badgeidJQSel).val();
    if (!originalInterested) {
        originalInterested = "0";
    }
    interestedElem.value = originalInterested;
    interestedElem.disabled = false;
    var bio = $("#bioHID_" + badgeidJQSel).val();
    var htmlbio = $("#htmlbioHID_" + badgeidJQSel).val();
    if (htmlbioused) {
        htmlbioElem.value = htmlbio;
    } else {
        bioElem.readOnly = false;
    }
    bioElem.value = bio;
    bioElem.defaultValue = bio;
    var staffnotes = $("#staffnotesHID_" + badgeidJQSel).val();
    staffnotesElem.value = staffnotes;
    staffnotesElem.defaultValue = staffnotes;
    staffnotesElem.readOnly = false;
    if (passwordElem && cpasswordElem) {
        passwordElem.value = '';
        passwordElem.readOnly = false;
        cpasswordElem.value = '';
        cpasswordElem.readOnly = false;
    }
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
    name_for_sortingDirty = false;
    rolesDirty = false;
    staffnotesDirty = false;
    tagsDirty = false;
    $('#resultsDiv').show();
    updateButton.disabled = true;
    $("#resultBoxDIV").html("").hide();
    $passwordsDontMatch.hide();
    var answercount = $("#answercountHID_" + badgeidJQSel).val();
    if (answercount > 0) {
        $showSurveyDiv.show();
        $showSurveyBtn.prop("disabled", false);
    } else {
        $showSurveyDiv.hide();
        $showSurveyBtn.prop("disabled", true);
    }
    if (htmlbioused) {
        startTinymce();
    }

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
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: ({
            badgeid: badgeid,
            ajax_request_action: "fetch_participant_tags"
        }),
        success: fetchParticipantTagsCallback,
        error: showAjaxError,
        type: "POST"
    });
}

function startTinymce() {
    if (mce_running)
        tinymce.remove();

    tinymce.init({
        selector: 'textarea#htmlbio',
        plugins: 'table wordcount fullscreen advlist link preview searchreplace autolink charmap hr nonbreaking visualchars ',
        browser_spellcheck: true,
        contextmenu: false,
        height: 400,
        min_height: 200,
        menubar: false,
        toolbar: [
            'undo redo | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | forecolor backcolor | link| preview fullscreen ',
            'searchreplace | alignleft aligncenter alignright alignjustify | outdent indent'
        ],
        toolbar_mode: 'wrap',
        content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
        placeholder: 'Type custom content here...',
        setup: function (ed) {
            ed.on('change', function (e) {
                bioDirty = true;
                textChange();
            });
        },
        init_instance_callback: function (editor) {
            $(editor.getContainer()).find('button.tox-statusbar__wordcount').click();  // if you use jQuery
        }
    });
    mce_running = true;
}

function doSearchPartsBUTN() {
    //called when user clicks "Search" within dialog
    const searchString = Array.from(document.getElementsByClassName('searchPartsINPUT')).
        filter(elem => elem.checkVisibility())[0].value;
    //let searchString = document.getElementById("searchPartsINPUT").value;
    const photosApproval = Array.from(document.getElementsByClassName('searchPhotoApproval')).
        filter(elem => elem.checkVisibility())[0]?.checked;
    //let photosApproval = document.getElementById("searchPhotoApproval").checked;
    let tags = [];
    document.querySelectorAll('.tag-search-container .tag-check').forEach((checkbox) => {
        if (checkbox.checkVisibility() && checkbox.checked) {
            tags.push(checkbox.value);
        }
    });
    if (!searchString && !photosApproval && tags.length === 0)
        return;
    let tagSearchType = Array.from(document.querySelectorAll("[name='tagmatchRadio']:checked")).
        filter(elem => elem.checkVisibility())[0]?.value;
    searchPartsBtnArr.forEach((searchPartsBtn) => (setButtonLoading(searchPartsBtn)));
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: ({
            searchString,
            photosApproval,
            tags,
            tagSearchType,
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

function fetchParticipantCallback(data, textStatus, jqXHR) {
    var node = data.firstChild.firstChild.firstChild;
    $("#badgeid").val(node.getAttribute("badgeid"));
    $("#lname_fname").val(node.getAttribute("lastname") + ", " + node.getAttribute("firstname"));
    $lastname.val(node.getAttribute("lastname")).prop("defaultValue", node.getAttribute("lastname")).prop("readOnly", false);
    $firstname.val(node.getAttribute("firstname")).prop("defaultValue", node.getAttribute("firstname")).prop("readOnly", false);
    phoneElemArr.forEach((elem) => {
        elem.value = node.getAttribute('phone');
        elem.defaultValue = node.getAttribute('phone');
        elem.readOnly = false;
    });
    $email.val(node.getAttribute("email")).prop("defaultValue", node.getAttribute("email")).prop("readOnly", false);
    $postaddress1.val(node.getAttribute("postaddress1")).prop("defaultValue", node.getAttribute("postaddress1")).prop("readOnly", false);
    $postaddress2.val(node.getAttribute("postaddress2")).prop("defaultValue", node.getAttribute("postaddress2")).prop("readOnly", false);
    $postcity.val(node.getAttribute("postcity")).prop("defaultValue", node.getAttribute("postcity")).prop("readOnly", false);
    $poststate.val(node.getAttribute("poststate")).prop("defaultValue", node.getAttribute("poststate")).prop("readOnly", false);
    $postzip.val(node.getAttribute("postzip")).prop("defaultValue", node.getAttribute("postzip")).prop("readOnly", false);
    $postcountry.val(node.getAttribute("postcountry")).prop("defaultValue", node.getAttribute("postcountry")).prop("readOnly", false);
    $badgename.val(node.getAttribute("badgename")).prop("defaultValue", node.getAttribute("badgename")).prop("readOnly", false);
    $pubsname.val(node.getAttribute("pubsname")).prop("defaultValue", node.getAttribute("pubsname")).prop("readOnly", false);
    $name_for_sorting.val(node.getAttribute("name_for_sorting")).prop("defaultValue", node.getAttribute("name_for_sorting")).prop("readOnly", false);
    originalInterested = node.getAttribute("interested");
    if (!originalInterested)
        originalInterested = 0;
    interestedElem.value = originalInterested;
    interestedElem.disabled = false;
    bioElem.value = node.getAttribute("bio");
    bioElem.defaultValue = node.getAttribute("bio");
    if (htmlbioused) {
        htmlbioElem.value = node.getAttribute("htmlbio");
        htmlbioElem.defaultValue = node.getAttribute("htmlbio");
        startTinymce();
    }
    staffnotesElem.value = node.getAttribute("staff_notes");
    staffnotesElem.defaultValue = node.getAttribute("staff_notes");
    staffnotesElem.readOnly = false;
    regtypeInpArr.forEach((inp) => (inp.value = node.getAttribute("regmessage")));
    if (passwordElem && cpasswordElem) {
        passwordElem.readOnly = false;
        passwordElem.value = '';
        cpasswordElem.readOnly = false;
        cpasswordElem.value = '';
    }
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
    name_for_sortingDirty = false;
    rolesDirty = false;
    staffnotesDirty = false;
    tagsDirty = false;
    $('#resultsDiv').show();
    $('#resultBoxDIV').show();
    updateButton.disabled = true;
    $passwordsDontMatch.hide();
    hideSearchResults();
}

function fetchUserPermRolesCallback(data, textStatus, jqXHR) {
    //ajax success callback function
    $("#role-container").html(data);
}

function fetchParticipantTagsCallback(data, textStatus, jqXHR) {
    //ajax success callback function
    $("#tag-container").html(data);
}

function getUpdateResults(data, textStatus, jqXHR) {
    $("#resultBoxDIV").html(data);
    $('#resultBoxDIV').show();
    window.scrollTo(0, 0);
    setTimeout(function () {
        updateButton.disabled = true;
    }, 0);
    updateButton.disabled = true;
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
    $.ajax({
        url: "SubmitAdminParticipants.php",
        dataType: "html",
        data: ({
            badgeid: badgeid,
            ajax_request_action: "fetch_participant_tags"
        }),
        success: fetchParticipantTagsCallback,
        error: showAjaxError,
        type: "POST"
    });
}

function hideSearchResults() {
    resultsHidden = true;
    $('#resultBoxDIV').hide()
    $("#searchResultsDIV").hide("fast");
    toggleSearchResultsBtnArr.forEach((btn) => {
        btn.disabled = false;
        btn.hidden = false;
        btn.innerHTML = 'Show Results';
    });
}

function initializeAdminParticipants() {
    //called when JQuery says AdminParticipants page has loaded
    interestedElem = document.getElementById("interested");
    bioElem = document.getElementById("bio");
    htmlbioElem = document.getElementById("htmlbio");
    if (htmlbioElem) {
        htmlbioused = true;
    }
    passwordElem = document.getElementById('password');
    cpasswordElem = document.getElementById('cpassword');
    staffnotesElem = document.getElementById("staffnotes");
    $pubsname = $("#pubsname");
    $name_for_sorting = $("#name_for_sorting");
    $lastname = $("#lastname");
    $firstname = $("#firstname");
    $badgename = $("#badgename");
    phoneElemArr = Array.from(document.getElementsByClassName('phone-inp'));
    $email = $("#email");
    $postaddress1 = $("#postaddress1");
    $postaddress2 = $("#postaddress2");
    $postcity = $("#postcity");
    $poststate = $("#poststate");
    $postzip = $("#postzip");
    $postcountry = $("#postcountry");
    $passwordsDontMatch = $("#passwordsDontMatch");
    updateButton = document.getElementById('updateBUTN');
    $showSurveyDiv = $("#showsurveydiv");
    $showSurveyBtn = $("#showsurveyBTN");
    regtypeInpArr = Array.from(document.getElementsByClassName('regtype'));
    $passwordsDontMatch.hide();
    $('#resultsDiv').hide();
    $('#resultBoxDIV').hide();
    unsavedWarningModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('unsavedWarningModal'));
    toggleSearchResultsBtnArr = Array.from(document.getElementsByClassName('toggleSearchResultsBUTN'));
    toggleSearchResultsBtnArr.forEach((btn) => {
        btn.addEventListener('click', toggleSearchResultsBUTN);
        btn.disabled = true;
        btn.hidden = true;
    });
    resultsHidden = true;
    $("#searchResultsDIV").html("").hide('fast');
    if (fbadgeid) { // signal from page initializer that page was requested to
        // to be preloaded with a participant
        fetchParticipant(fbadgeid);
    }
    document.getElementById('resultsDiv').addEventListener('input', processChange);
    document.getElementById('adminParticipantsForm').addEventListener('submit', onSubmit);
    Array.from(document.getElementsByClassName('searchPartsINPUT')).forEach(elem => elem.addEventListener('keydown', searchKeypr));
    searchPartsBtnArr = Array.from(document.getElementsByClassName("searchPartsBUTN"));
    prevBtnArr = Array.from(document.getElementsByClassName("prevSearchResultBUTN"));
    nextBtnArr = Array.from(document.getElementsByClassName("nextSearchResultBUTN"));
    searchPartsBtnArr.forEach((searchPartsBtn) => searchPartsBtn.addEventListener('click', doSearchPartsBUTN));
    max_bio_len = bioElem.dataset.maxLength;
}

function searchKeypr(event) {
    if (event.code === "Enter") {
        doSearchPartsBUTN();
    }
}

function loadNewParticipant() {
    chooseParticipant(saveNewBadgeId, true);
    return true;
}

function onSubmit(event) {
    event.preventDefault();
}

function processChange(event) {
    var target = event.target;
    var targetId = target.id;
    switch (targetId) {
        case 'password':
        case 'cpassword':
            var password = passwordElem.value;
            var cpassword = cpasswordElem.value;
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
            interestedDirty = (interestedElem.value !== originalInterested);
            break;
        case 'bio':
            bioDirty = (bioElem.value !== bioElem.defaultValue);
            break;
        case 'htmlbio':
            bioDirty = (htmlbioElem.value !== htmlbioElem.defaultValue);
            break;
        case 'staffnotes':
            staffnotesDirty = (staffnotesElem.value !== staffnotesElem.defaultValue);
            break;
        case 'pubsname':
            pubsnameDirty = ($pubsname.val() !== $pubsname.prop("defaultValue"));
            break;
        case 'name_for_sorting':
            name_for_sortingDirty = ($name_for_sorting.val() !== $name_for_sorting.prop("defaultValue"));
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
        case 'phone1':
        case 'phone2':
            phoneDirty = target.value !== target.defaultValue;
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
            if ($target.is(".role-check")) {
                rolesDirty = false;
                $(".role-check").each(function () {
                    $checkbox = $(this);
                    if ($checkbox.is(":checked") !== $checkbox.prop("defaultChecked")) {
                        rolesDirty = true;
                        return false;
                    }
                });
            }
            if ($target.is("#tag-container .tag-check")) {
                tagsDirty = false;
                $("#tag-container .tag-check").each(function () {
                    $checkbox = $(this);
                    if ($checkbox.is(":checked") !== $checkbox.prop("defaultChecked")) {
                        tagsDirty = true;
                        return false;
                    }
                });
            }
    }
    checkDirty();
}

function checkDirty() {
    if (passwordDirtyAndReady || interestedDirty || bioDirty || staffnotesDirty || pubsnameDirty || name_for_sortingDirty ||
        lastnameDirty || firstnameDirty || badgenameDirty || phoneDirty || emailDirty || postaddress1Dirty || postaddress2Dirty ||
        postcityDirty || poststateDirty || postzipDirty || postcountryDirty || rolesDirty || tagsDirty) {
        updateButton.disabled = false;
    } else {
        updateButton.disabled = true;
    }
}


function updatePlainText() {
    tinymce.triggerSave();
    var tempDivElement = document.createElement("div");
    tempDivElement.innerHTML = htmlbioElem.value;
    bioElem.value = tempDivElement.textContent || tempDivElement.innerText || "";
    tempDivElement.remove();
}

function textChange(id) {
    if (htmlbioused) {
        updatePlainText();
        bioDirty = (htmlbioElem.value !== htmlbioElem.defaultValue);
        checkDirty();
    }
}        

function showAjaxError(data, textStatus, jqXHR) {
    var $resultBoxDIV = $("#resultBoxDIV");
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-36"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-36"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $resultBoxDIV.html(content).show();
    window.scrollTo(0, 0);
}

function showSearchResults() {
    resultsHidden = false;
    $("#searchResultsDIV").show("fast").css("overflow-y", "auto");
    toggleSearchResultsBtnArr.forEach((btn) => {
        btn.disabled = false;
        btn.hidden = false;
        btn.innerHTML = 'Hide Results';
    });
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
    name_for_sortingDirty = false;
    rolesDirty = false;
    staffnotesDirty = false;
    tagsDirty = false;
    if (passwordElem && cpasswordElem) {
        passwordElem.value = '';
        cpasswordElem.value = '';
    }
    resetButtonLoading(updateButton);
    updateButton.disabled = true;
    originalInterested = interestedElem.value;
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
    $("#name_for_sortingHID_" + badgeidJQSel).val(node.getAttribute("name_for_sorting"));
    $("#bnameSPAN_" + badgeidJQSel).html(node.getAttribute("badgename"));
    $("#interestedHID_" + badgeidJQSel).originalInterested = node.getAttribute("interested");
    $("#bioHID_" + badgeidJQSel).val(node.getAttribute("bio"));
    if (htmlbioused) {
        $("#htmlbioHID_" + badgeidJQSel).val(node.getAttribute("htmlbio"));
        htmlbioElem.value = node.getAttribute("bio");
        startTinymce();
    }
    $("#staffnotesHID_" + badgeidJQSel).val(node.getAttribute("staff_notes"));
    bioElem.value = node.getAttribute("bio");
}

function toggleSearchResultsBUTN() {
    $("#searchResultsDIV").slideToggle("fast");
    resultsHidden = !resultsHidden;
    toggleSearchResultsBtnArr.forEach((btn) => (btn.innerHTML = resultsHidden ? 'Show Results' : 'Hide Results'));
}

function updateBUTTON() {
    if (!validateBioCharacterLength()) {
        return;
    }
    setButtonLoading(updateButton);
    var postdata = {
        ajax_request_action: "update_participant",
        badgeid: $("#badgeid").val()
    };
    if (passwordDirtyAndReady) {
        postdata.password = passwordElem.value;
    }
    if (bioDirty) {
        postdata.bio = bioElem.value;
        if (htmlbioused) {
            tinymce.triggerSave();
            postdata.htmlbio = htmlbioElem.value;
        }
    }
    if (pubsnameDirty) {
        postdata.pubsname = $pubsname.val();
    }
    if (name_for_sortingDirty) {
        postdata.name_for_sorting = $name_for_sorting.val();
    }
    if (staffnotesDirty) {
        postdata.staffnotes = staffnotesElem.value;
    }
    if (interestedDirty) {
        postdata.interested = interestedElem.value;
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
        postdata.phone = phoneElemArr.filter(elem => elem.checkVisibility())[0].value;
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
        $(".role-check").each(function() {
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
    if (tagsDirty) {
        var tagsToAdd = [];
        var tagsToDelete = [];
        $("#tag-container .tag-check").each(function() {
            $check = $(this);
            checked = $check.is(":checked");
            defaultChecked = $check.prop("defaultChecked");
            if (checked && !defaultChecked) {
                tagsToAdd.push($check.val());
            } else if (!checked && defaultChecked) {
                tagsToDelete.push($check.val());
            }
        });
        if (tagsToAdd.length > 0) {
            postdata.tagsToAdd = tagsToAdd;
        }
        if (tagsToDelete.length > 0) {
            postdata.tagsToDelete = tagsToDelete;
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
    count = bioElem.value.length;
    if (count > max_bio_len) {
        alert("Bio too long at " + count + "; " + max_bio_len + " characters allowed.");
        return false;
    }
    return true;
}

function writeSearchResults(data, textStatus, jqXHR) {
    //ajax success callback function
    data_json = [];
    data_json["HTML"] = "";
    data_json["rowcount"] = 0;
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    $("#searchResultsDIV").html(data_json["HTML"]).show('fast');
    searchPartsBtnArr.forEach((searchPartsBtn) => (resetButtonLoading(searchPartsBtn)));
    searchCount = data_json["rowcount"];
    if (searchCount > 1) {
        prevBtnArr.forEach((prevBtn) => {
            prevBtn.style.display = "block";
            prevBtn.disabled = true;
        });
        nextBtnArr.forEach((prevBtn) => {
            prevBtn.style.display = "block";
            prevBtn.disabled = false;
        });
        badgeList = data_json["badgeids"];
    } else {
        badgeList = null;
    }
    searchIndex = -1;
    showSearchResults();
}

function showSurveyBUTTON() {
    if (curbadgeid != '') {
        window.open('StaffViewSurveyResults.php?badgeid=' + curbadgeid, "_blank");
    }
}
