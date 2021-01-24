//	Created by Peter Olszowka on 2015-11-21;
//	Copyright (c) 2015-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.
var participantSelectChoices = null;

var StaffAssignParticipants = function() {
    this.onClickEditNPS = function(event) {
        var $edit_NPS_Button = $("#editNPS_BUT");
        if ($edit_NPS_Button.text() === "Edit") {
            $edit_NPS_Button.text("Cancel");
            var $NPS_SPN = $("#NPS_SPN");
            var NPStext = $NPS_SPN.text();
            $NPS_SPN.hide();
            var $NPS_TXTA = $("<textarea id=\"NPS_TXTA\" name=\"NPStext\" cols=\"80\" wrap=\"soft\" style=\"vertical-align:middle;\">" + NPStext + "</textarea>");
            $NPS_SPN.after($NPS_TXTA);
        } else {
            $edit_NPS_Button.text("Edit");
            $("#NPS_TXTA").remove();
            $("#NPS_SPN").show();
        }
    };

    this.showPopoverCallback = function(data, textStatus, jqXHR) {
        var node = data.firstChild.firstChild.firstChild;
        this.this.bio = node.getAttribute("bio");
        this.this.fname = node.getAttribute("firstname") + " " + node.getAttribute("lastname");

        $('#BioBtn').button('reset');
        setTimeout(function() {
                $("#BioBtn").button().prop("disabled", true);
            },
            0);

        this.this.$popoverTarget.popover('show');
    };

    this.showPopover = function(event) {
        // Get the bio for the selected participant
        $('#BioBtn').button('loading');
        var badgeid = $('#partDropdown').val();
        $.ajax({
            url: "SubmitAdminParticipants.php",
            dataType: "xml",
            data: ({
                badgeid : badgeid,
                ajax_request_action : "fetch_participant"
            }),
            success: this.showPopoverCallback,
            type: "GET",
            this: this
        });
        event.stopPropagation();
        return false;
    };

    this.onPopoverClose = function() {
        var $biographyButton = document.getElementById('BioBtn');
        if ($biographyButton) {
            $biographyButton.disabled = false;
        }
    };

    this.initialize = function() {
        var that = this;
        var $biographyButton = document.getElementById('BioBtn');
        var $showsurveyButton = document.getElementById("SurveyBtn");
        if ($biographyButton) {
            $biographyButton.addEventListener('click', this.showPopover.bind(this));
            $biographyButton.disabled = true;
        }
        if ($showsurveyButton) { 
            $showsurveyButton.disabled = true;
        }
        var $participantSelect = document.getElementById('partDropdown');
        if ($participantSelect) {
             participantSelectChoices = new Choices($participantSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
            if ($biographyButton) {
                $participantSelect.addEventListener('change', function () {
                    $biographyButton.disabled = $participantSelect.value === '';
                });
            }
            if ($showsurveyButton) {
                $participantSelect.addEventListener('change', function () {
                    $showsurveyButton.disabled = $participantSelect.value === '';
                });
            }
        }
        var $chooseSessionButton = document.getElementById('sessionBtn');
        if ($chooseSessionButton) {
            $chooseSessionButton.disabled = true;
        }
        var $sessionSelect = document.getElementById('sessionDropdown');
        if ($chooseSessionButton && $sessionSelect) {
            $sessionSelect.addEventListener('change', function() {
                $chooseSessionButton.disabled = $sessionSelect.value === '0';
            });
            var sessionSelectChoices = new Choices($sessionSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
        }
        this.$popoverTarget = $("#popover-target");
        $('body').click(function () {
            if (that !== null) {
                that.$popoverTarget.popover('hide');
                if ($participantSelect !== null) {
                    if ($participantSelect.value !== '') {
                        $biographyButton.disabled = false;
                    }
                }
            }
        });
        this.$popoverTarget.popover({
            html: true,
            placement: 'top',
            title: function() {
                return 'Bio for ' + that.fname + "&nbsp;<i id='popoverClose' class='icon-remove-sign pull-right'></i>";
            },
            content: function() {
                return that.bio;
            }
        });
        $('#popoverClose').click(function(e) {
            var $biographyButton = document.getElementById('BioBtn');
            $biographyButton.disabled = false;
            that.$popoverTarget.popover('hide');
        });
        $('[rel="popover"]').popover();
        $("#editNPS_BUT").on("click", this.onClickEditNPS);
        this.$popoverTarget.on('close', this.onPopoverClose);

        $('.alert').alert();
        el = document.getElementById("surveysearch-div");
        if (el)
            el.style.display = 'none';
        el = document.getElementById("filter");
        if (el)
            el.addEventListener('click', filter);
    }

};

var assignParticipants = new StaffAssignParticipants();

/* This file should be included only on relevant page.  See main.js  and javascript_functions.php */
document.addEventListener('DOMContentLoaded', assignParticipants.initialize.bind(assignParticipants));

function showSurveyResults(who, whotype) {
    //console.log("In showSurveyResults(" + who + ',' + whotype + ')');
    if (whotype == 'element') {
        el = document.getElementById(who);
        badgeid = el.value;
    } else
        badgeid = who;
    //console.log("badgeid = '" + badgeid + "'");
    if (badgeid != '') {
        window.open('StaffViewSurveyResults.php?badgeid=' + badgeid, "_blank");
    }
}


function toggleShowFilter() {
    el = document.getElementById("surveysearch-div");
    current = el.style.display;
    if (current == 'block') {
        el.style.display = 'none';
        document.getElementById("showhideSurveyFilter").innerHTML = "Show Survey Filter";
    } else {
        el.style.display = 'block';
        document.getElementById("showhideSurveyFilter").innerHTML = "Hide Survey Filter";
    }
}

function filterComplete(data, textStatus, jqXHR) {
    message = "";
    alerttype = "success";
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
        return;
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
        var participantSelect = document.getElementById('partDropdown-div');
        if (participantSelect) {
            participantSelectChoices = null;
            participantSelect.innerHTML = data_json.select;
            participantSelect = document.getElementById('partDropdown');
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
    sessionid = document.getElementById('sessionDropdown').value;
    participantSelectChoices = null;

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
        matchall: document.getElementById("match-all").checked == "1" ? 'true' : 'false',
        source: "assign",
        sessionid: sessionid
    };
    document.getElementById("message").style.display = 'none';
    $.ajax({
        url: "SubmitFilterParticipants.php",
        dataType: "html",
        data: postdata,
        success: filterComplete,
        error: filterError,
        type: "POST"
    });
};