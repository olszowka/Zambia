var maintainRoomSched = new MaintainRoomSched;

function MaintainRoomSched() {
	var selroomArray = [];
	
	this.initialize = function initialize() {
		//called when JQuery says My Profile page has loaded
		//debugger;
		$("#showUnschedRmsCHK").click(maintainRoomSched.unschedRoomsClick);
		$("#selroom option[value!=0]").each( function() {
			selroomArray.push({
				value:$(this).val(),
				text:$(this).text(),
				is_scheduled:$(this).attr("is_scheduled")
				});
			});
		if (!$("#showUnschedRmsCHK").is(":checked"))
			$("#selroom option[is_scheduled='0']").remove();
	}

	this.unschedRoomsClick = function unschedRoomsClick() {
		//debugger;
		if ($("#showUnschedRmsCHK").is(":checked")) {
				var i,j;
				$("#selroom option[value!=0]").remove();
				var s = $("#selroom").get(0);
				for (i in selroomArray) {
					j = s.options.length;
					s.options[j]= new Option(selroomArray[i].text, selroomArray[i].value);
					$(s.options[j]).attr("is_scheduled",selroomArray[i].is_scheduled);
					}
				}
			else
				$("#selroom option[is_scheduled='0']").remove();
	}

}