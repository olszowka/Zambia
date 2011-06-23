$(document).ready(function() {
	//this function is run whenever any page finishes loading if JQuery has been loaded
	//debugger;
	switch (thisPage) {
		case "Administer Participants":
			initializeAdminParticipants();
			break;
		default:
			window.status="Ready."
		}
});