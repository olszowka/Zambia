//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
document.addEventListener( "DOMContentLoaded", function () {
    //this function is run whenever any page finishes loading if JQuery has been loaded
    //debugger;
    //client variable thisPage set to server variable $title in files ParticipantHeader.php and StaffHeader.php
	switch (thisPage) {
		case "Administer Participants":
			initializeAdminParticipants();
			break;
		case "My Profile":
			myProfile.initialize();
			break;
		case "Grid Scheduler":
			staffMaintainSchedule.initialize();
			break;
		case "Session Search Results":
			partSearchSessionsSubmit.initialize();
			break;
		case "Panel Interests":
			panelInterests.initialize();
			break;
		case "Edit Custom Text":
			editCustomText.initialize();
		default:
			window.status="Ready.";
		/**
		 * These js files initialize themselves and therefore should be included only on the relevant pages.
		 * See javascript_functions.php
		 *
		 * Session History -- SessionHistory.js
		 * Invite Participants -- InviteParticipants.js
		 * (Staff) Assign Participants -- StaffAssignParticipants.js
         * Maintain Room Schedule -- MaintainRoomSched.js
		 */
	}
	var $altHeaderContainer = document.getElementById("alt-header-container");
	var $regHeaderContainer = document.getElementById("reg-header-container");
	if ($altHeaderContainer && $regHeaderContainer) {
		if (getValue('zambiaHeader') === 'small') {
			$altHeaderContainer.classList.remove("hidden", "d-none");
			$regHeaderContainer.classList.add("collapsed", "hidden", "d-none");
			window.setTimeout(function () {
				$regHeaderContainer.classList.remove("hidden", "d-none");
			},800);
		} else {
			$altHeaderContainer.classList.add("collapsed");
			window.setTimeout(function () {
				$altHeaderContainer.classList.remove("hidden", "d-none");
			},800);
		}
		var $hideHeaderButton = document.getElementById("hide-header-but");
		if ($hideHeaderButton) {
			$hideHeaderButton.addEventListener("click", function (event) {
				$regHeaderContainer.classList.add("collapsed");
				$altHeaderContainer.classList.remove("collapsed");
				setValue('zambiaHeader', 'small');
			});
		}
		var $showHeaderButton = document.getElementById("show-header-but");
		if ($showHeaderButton) {
            $showHeaderButton.addEventListener("click", function (event) {
				$regHeaderContainer.classList.remove("collapsed");
				$altHeaderContainer.classList.add("collapsed");
				setValue('zambiaHeader', 'large');
			});
		}
	}
});

function supports_html5_storage() {
	try {
		return 'localStorage' in window && window['localStorage'] !== null;
	} catch (e) {
		return false;
	}
}

function setValue(key, val) {
	if (supports_html5_storage()) {
		localStorage[key] = val;
	}
}

function getValue(key) {
	if (supports_html5_storage()) {
		return localStorage[key];
	} else {
		return null;
	}
}

function clearValue(key) {
	if (supports_html5_storage()) {
		localStorage[key] = null;
	}
}

var lib = new Lib;

function Lib() {
	this.toggleCheckbox = function toggleCheckbox() {
		var thecheckbox = $(this).find(":checkbox");
		thecheckbox.prop("checked",!thecheckbox.prop("checked"));
		thecheckbox.triggerHandler("click");
	};
}
