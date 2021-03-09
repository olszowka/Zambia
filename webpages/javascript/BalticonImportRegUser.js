// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var importDirty = false;
var roleDirty = false;
var $updateBTN = null;

function isDirty(override) {
    //called when user clicks "Search for participants" on the page
    if (override === undefined) {
        override = false;
    }
    if (!override && importDirty) {
        $("#unsavedWarningModal").modal('show');
        $("#cancelOpenSearchBUTN").blur();
        return true;
    }
    if (override) {
        $("#unsavedWarningModal").modal('hide');
    }
    return false;
}

function doSearchImportBUTN() {
    //called when user clicks "Search" within dialog
    var x = document.getElementById("searchImportINPUT").value;
    if (!x)
        return false;
    $('#searchImportBUTN').button('loading');
    $.ajax({
        url: "BalticonSubmitImportRegUser.php",
        dataType: "html",
        data: ({
            searchString: x,
            ajax_request_action: "perform_search"
        }),
        success: writeSearchResults,
        error: showAjaxError,
        type: "POST"
    });
    return false;
}

function fetchUserPermRolesCallback(data, textStatus, jqXHR) {
    //ajax success callback function
    $("#role-container").html(data);
    $('#resultsDiv').show();
}

function initializeImportRegUser() {
    //called when JQuery says Import Reg USer page has loaded
    //debugger;
    $updateBTN = $('#updateBUTN');
    $('#resultsDiv').hide();
    $('#resultBoxDIV').hide();
    $("#unsavedWarningModal").modal({show: false});
    $("#searchImportBUTN").on('click', doSearchImportBUTN);
    $("#searchResultsDIV").html("").hide('fast');
    $("#importRegUserForm").on("input", ".mycontrol", processChange);
}


function processChange() {
    var $target = $(this);
    var targetId = $target.attr("id");
    if (targetId.match(/role_/))
        roleDirty = true;
    else
        importDirty = true;
    checkDirty();
}

function checkDirty() {
    if (importDirty && roleDirty) {
        $updateBTN.prop("disabled", false);
    } else {
        $updateBTN.prop("disabled", true);
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
}

function updateBUTTON() {
    //debugger;
    $updateBUTN.button('Importing...');
    var postdata = {
        ajax_request_action: "import_users",
        id: $("#badgeid").val()
    };
    
    var rolesToAdd = [];
    $(".tag-chk").each(function() {
        $check = $(this);
        checked = $check.is(":checked");
        defaultChecked = $check.prop("defaultChecked");
        if (checked && !defaultChecked) {
            rolesToAdd.push($check.val());
        }
    });
    if (rolesToAdd.length > 0) {
        postdata.rolesToAdd = rolesToAdd;
    }     
    $.ajax({
        url: "BalticonSubmitImportRegUser.php",
        dataType: "html",
        data: postdata,
        success: getImportResults,
        error: showAjaxError,
        type: "POST"
    });
}

function writeSearchResults(data, textStatus, jqXHR) {
    //ajax success callback function
    $("#searchResultsDIV").html(data).show('fast');
    $('#searchImportBUTN').button('reset');
    $('#resultBoxDIV').hide();
    showSearchResults();
    $.ajax({
        url: "BalticonSubmitImportRegUser.php",
        dataType: "html",
        data: ({
            ajax_request_action: "fetch_user_perm_roles"
        }),
        success: fetchUserPermRolesCallback,
        error: showAjaxError,
        type: "POST"
    });
}