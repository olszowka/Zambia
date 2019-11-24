//	Created by Peter Olszowka on 2019-11-19;
//	Copyright (c) 2019 The Peter Olszowka. All rights reserved. See copyright document for more details.

var InviteParticipants = function() {
    this.initialize = function initialize() {
        //called when page has loaded
        var $participantSelect = document.getElementById('participant-select');
        if ($participantSelect) {
            var participantSelectChoices = new Choices($participantSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
        }
        var $sessionSelect = document.getElementById('session-select');
        if ($sessionSelect) {
            var sessionSelectChoices = new Choices($sessionSelect, {
                searchResultLimit: 9999,
                searchPlaceholderValue: "Type here to search list."
            });
        }
    };
};

var inviteParticipants = new InviteParticipants();

/* This file should be included only on relevant page.  See main.js and javascript_functions.php */
document.addEventListener('DOMContentLoaded', inviteParticipants.initialize.bind(inviteParticipants));
