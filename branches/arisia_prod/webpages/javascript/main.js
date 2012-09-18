$(document).ready(function() {
	//this function is run whenever any page finishes loading if JQuery has been loaded
	//debugger;
	switch (thisPage) {
		case "Administer Participants":
			initializeAdminParticipants();
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
		default:
			window.status="Ready."
		}
});

var lib = new lib;

function lib() {
	this.toggleCheckbox = function toggleCheckbox() {
		var thecheckbox = $(this).find(":checkbox");
		thecheckbox.prop("checked",!thecheckbox.prop("checked"));
		thecheckbox.triggerHandler("click");
		}
	
	this.onePageResize = function onePageResize() {
		$("#mainContentContainer").css("top", $("#top").outerHeight(true) + $("#staffNav").outerHeight(true) + 1);
		}	
}