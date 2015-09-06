// $Header$
$(document).ready(function() {
	//this function is run whenever any page finishes loading if JQuery has been loaded
	//debugger;
	//client variable thisPage set to server variable $title in files ParticipantHeader.php and StaffHeader.php
	switch (thisPage) {
		case "Administer Participants":
			initializeAdminParticipants();
			break;
		case "Assign Participants":
			initAssignParticipants();
			break;
		case "Maintain Room Schedule":
			maintainRoomSched.initialize();
			break;
		case "My Profile":
			myProfile.initialize();
			break;
		case "Grid Scheduler":
			staffMaintainSchedule.initialize();
			break;
		case "Search Results":
			searchMySessions1.initialize();
			break;
		case "Panel Interests":
			panelInterests.initialize();
			break;
		default:
			window.status="Ready."
		}
	if (getValue('zambiaHeader') == 'small' && !alwaysShowLargeHeader) {
			$('#altHeader').show();
			$('#regHeader').hide();
			  }
		  else {
			$('#altHeader').hide();
			$('#regHeader').show();
			  };
	$('#hideHeader').click(function() {
		$('#regHeader').slideUp();
		$('#altHeader').show();
		setValue('zambiaHeader', 'small');
		window.setTimeout(staffMaintainSchedule.resizeMe,300);
		window.setTimeout(staffMaintainSchedule.resizeMe,600);
		});
	$('#showHeader').click(function() {
		$('#altHeader').hide();
		$('#regHeader').slideDown();
		setValue('zambiaHeader', 'large');
		window.setTimeout(staffMaintainSchedule.resizeMe,300);
		window.setTimeout(staffMaintainSchedule.resizeMe,600);
		});
});

function supports_html5_storage() {
	try {
			return 'localStorage' in window && window['localStorage'] !== null;
			}
		catch (e) {
			return false;
			}
}

function setValue(key, val) {
	if (supports_html5_storage())
		localStorage[key] = val;
}

function getValue(key) {
	if (supports_html5_storage())
			return localStorage[key];
		else
			return null;
}

function clearValue(key) {
	if (supports_html5_storage())
		localStorage[key] = null;
}

var lib = new Lib;

function Lib() {
	this.toggleCheckbox = function toggleCheckbox() {
		var thecheckbox = $(this).find(":checkbox");
		thecheckbox.prop("checked",!thecheckbox.prop("checked"));
		thecheckbox.triggerHandler("click");
		}
	this.onePageResize = function onePageResize() {
		$("#mainContentContainer").css("top", $("#top").outerHeight(true) + $("#staffNav").outerHeight(true) + 1);
		}	
}
