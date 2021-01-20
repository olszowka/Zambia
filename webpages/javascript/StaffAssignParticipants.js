//	Created by Peter Olszowka on 2015-11-21;
//	Copyright (c) 2015-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.

var StaffAssignParticipants = function() {
    this.onClickEditNPS = function(event) {
        var $edit_NPS_Button = $("#editNPS_BUT");
        if ($edit_NPS_Button.text() === "Edit") {
            $edit_NPS_Button.text("Cancel");
            var $NPS_SPN = $("#NPS_SPN");
            var NPStext = $NPS_SPN.text();
            $NPS_SPN.hide();
            var $NPS_TXTA = $("<textarea id=\"NPS_TXTA\" name=\"NPStext\">" + NPStext + "</textarea>");
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
        if ($biographyButton) {
            $biographyButton.addEventListener('click', this.showPopover.bind(this));
            $biographyButton.disabled = true;
        }
        var $participantSelect = document.getElementById('partDropdown');
        if ($participantSelect) {
            var participantSelectChoices = new Choices($participantSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
            if ($biographyButton) {
                $participantSelect.addEventListener('change', function () {
                    $biographyButton.disabled = $participantSelect.value === '';
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
        $('body').click(function() {
            that.$popoverTarget.popover('hide');
            if ($participantSelect.value !== '') {
                $biographyButton.disabled = false;
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
    window.open('StaffViewSurveyResults.php?badgeid=' + badgeid, "_blank");
}
