//	Created by Peter Olszowka on 2019-11-19;
//	Copyright (c) 2019 The Peter Olszowka. All rights reserved. See copyright document for more details.

var participantSelectChoices = null;
var filterlist = [];

var InviteParticipants = function() {
    this.initialize = function initialize() {
        //called when page has loaded
        var participantSelect = document.getElementById('participant-select');
        if (participantSelect) {
            participantSelectChoices = new Choices(participantSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
        }
        var sessionSelect = document.getElementById('session-select');
        if (sessionSelect) {
            var sessionSelectChoices = new Choices(sessionSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
        }
        $('[data-toggle="tooltip"]').each(function () {
            this.title = '<span class="text-left" style="white-space: nowrap;">' + this.title + '</span>';
        })
        $('[data-toggle="tooltip"]').tooltip();
    };
};

var inviteParticipants = new InviteParticipants();

function inviteComplete(data, textStatus, jqXHR) {
    message = "";
    alerttype = "success";
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    //console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;

    if (data_json.hasOwnProperty("alerttype"))
        alerttype = data_json.alerttype;

    el = document.getElementById("message");
    if (message != "") {
        el.innerHTML = message;
        el.className = 'alert mt-4 alert-' + alerttype;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

function inviteError(xhdr, status, error) {
    message = "Invite Error: " + xhdr.status + ': ' + xhdr.statusText;
    el = document.getElementById("message");
    el.innerHTML = message;
    el.className = 'alert mt-4 alert-danger';
    el.style.display = 'block';
    el.innerHTML = message;
}

function invite() {
    var postdata = {
        ajax_request_action: "invite",
        selpart: document.getElementById("participant-select").value,
        selsess: document.getElementById("session-select").value
    };
    document.getElementById("message").style.display = 'none';
    $.ajax({
        url: "SubmitInviteParticipants.php",
        dataType: "html",
        data: postdata,
        success: inviteComplete,
        error: inviteError,
        type: "POST"
    });
};

function filterComplete(data, textStatus, jqXHR) {
    message = "";
    alerttype = "success";
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    if (data_json.hasOwnProperty("message"))
        message = data_json.message;

    if (data_json.hasOwnProperty("alerttype"))
        alerttype = data_json.alerttype;

    el = document.getElementById("message");
    if (message != "") {
        el.innerHTML = message;
        el.className = 'alert mt-4 alert-' + alerttype;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }

    if (data_json.hasOwnProperty("select")) {
        document.getElementById('participant-select-div').innerHTML = data_json.select;
        var participantSelect = document.getElementById('participant-select');
        if (participantSelect) {
            participantSelectChoices = null;
            participantSelectChoices = new Choices(participantSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
        }
    }
}

function filterError(xhdr, status, error) {
    message = "Filter Error: " + xhdr.status + ': ' + xhdr.statusText;
    el = document.getElementById("message");
    el.innerHTML = message;
    el.className = 'alert mt-4 alert-danger';
    el.style.display = 'block';
    el.innerHTML = message;
}

function filter() {
    //console.log(data_json);
    filterlist = [];

    // build select items
    $('[data-filter]').each(function () {
        filtertype = this.getAttribute('data-filter');
        id = this.id;
        questionid = this.id.replace(/-.*/, '');
        add = false;
        switch (filtertype) {
            case 'check':
            case 'month':
                value = this.id.replace(/^[^-]*-/, '');
                add = this.checked;
                break;
            case 'min':
            case 'max':
            case 'text':
                value = this.value;
                add = value != "";
                break;
            case 'from-monthyear':
            case 'to-monthyear':
                value = this.value;
                add = value != "";
                break;
        }

        if (add)
            filterlist.push({ type: filtertype, id: id, questionid: questionid, value: value });
    });

    //console.log(filterlist);
    var postdata = {
        ajax_request_action: "filter",
        filters: JSON.stringify(filterlist),
        matchall: document.getElementById("match-all").checked == "1" ? 'true' : 'false'
    };
    document.getElementById("message").style.display = 'none';
    $.ajax({
        url: "SubmitInviteParticipants.php",
        dataType: "html",
        data: postdata,
        success: filterComplete,
        error: filterError,
        type: "POST"
    });
};