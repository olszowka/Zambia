var staffMaintainSchedule = new staffMaintainSchedule;

function staffMaintainSchedule() {
	var dirtyInputArr = [];
	var anyDirty = false;
	var pw;
	var cpw;
	var pwOK = true;
	var bioOK = true;
	
	this.anyChange = function anyChange(element) {
	}
	
	this.changeRoomDisplay = function changeRoomDisplay() {
	}

	this.initialize = function initialize() {
		$("#tabs").tabs();
		$("#clearAllButton").button();
		$("#swapModeCheck").button();
		$(window).resize(staffMaintainSchedule.resizeMe);
		$(window).resize();
	}

	this.resizeMe = function resizeMe() {
		lib.onePageResize();
		$("#tabsContent").css("top", $("#tabsBar").outerHeight(true) + 1);
	}
}