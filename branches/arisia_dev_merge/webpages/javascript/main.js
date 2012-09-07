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
		//case "Edit Schedule":
		//	staffMaintainSchedule.initialize();
		case "Show Search Session Results":
			searchMySessions1.initialize();
			
		default:
			window.status="Ready."
		}
});

var lib = new lib;

function lib() {
	this.toggleCheckbox = function toggleCheckbox() {
		// for some fucking reason I can't fathom, under some, but not all, circumstances (and this code qualifies)
		// if you call .click() on a checkbox, the browser will call the handler before updating the value.
		var checkbox = $(this).find(":checkbox");
		checkbox.prop("checked",!checkbox.prop("checked"));
		checkbox.triggerHandler("click");
		}

	this.onePageResize = function onePageResize() {
		$("#mainContentContainer").css("top", $("#fullPageHeader").outerHeight(true) + 1);
		}	
}