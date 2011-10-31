$(document).ready(function() {
	//this function is run whenever any page finishes loading if JQuery has been loaded
	//debugger;
	switch (thisPage) {
		case "Administer Participants":
			initializeAdminParticipants();
			break;
		case "My Profile":
			myProfile.initialize();
			break;
		case "Edit Schedule":
			staffMaintainSchedule.initialize();
			break;
		case "Show Search Session Results":
			searchMySessions1.initialize();
			break;
		default:
			window.status="Ready."
		}
});

var lib = new lib;

function lib() {
	this.toggleCheckbox = function toggleCheckbox(me) {
		var thecheckbox = $(me).find(":checkbox");
		thecheckbox.prop("checked",!thecheckbox.prop("checked"));
		}

	this.onePageResize = function onePageResize() {
		$("#mainContentContainer").css("top", $("#fullPageHeader").outerHeight(true) + 1);
		}	
}