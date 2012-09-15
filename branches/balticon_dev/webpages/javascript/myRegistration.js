var myRegistration = new MyRegistration;

function MyRegistration() {
	var dirtyInputArr = [];
	var anyDirty = false;
	
	this.anyChange = function anyChange(element) {
		$("#resultBoxDIV").html("&nbsp;");

		anyDirty = true;
		dirtyInputArr[element] = true;

		if (anyDirty) {
			$("#regSubmitBTN").removeAttr("disabled");
			}
	}

	this.initialize = function initialize() {
		//called when JQuery says My Profile page has loaded
		//just a filler for now
		//debugger;
		dirtyInputArr = [];
		$("#regSubmitBTN").attr("disabled","disabled");
		//window.status="Reached initializeMyProfile.";
	}

	this.updateBUTN = function updateBUTN() {
		//debugger;
		var postdata = {
			ajax_request_action : "update_registration"
			};
		if(dirtyInputArr["firstname"]) {
			postdata.firstname = $("#firstname").val();
			}
		if(dirtyInputArr["middleInit"]) {
			postdata.middleInit = $("#middleInit").val();
			}
		if(dirtyInputArr["lastname"]) {
			postdata.lastname = $("#lastname").val();
			}
		if(dirtyInputArr["regtype"]) {
			postdata.regtype = $("#regtype").val();
			}
		if(dirtyInputArr["badgename"]) {
			postdata.badgename = $("#badgename").val();
			}
		if(dirtyInputArr["phone"]) {
			postdata.phone = $("#phone").val();
			}
		if(dirtyInputArr["email"]) {
			postdata.email = $("#email").val();
			}
		if(dirtyInputArr["postaddress1"]) {
			postdata.postaddress1 = $("#postaddress1").val();
			}
		if(dirtyInputArr["postaddress2"]) {
			postdata.postaddress2 = $("#postaddress2").val();
			}
		if(dirtyInputArr["postcity"]) {
			postdata.postcity = $("#postcity").val();
			}
		if(dirtyInputArr["poststate"]) {
			postdata.poststate = $("#poststate").val();
			}
		if(dirtyInputArr["postzip"]) {
			postdata.postzip = $("#postzip").val();
			}
		if(dirtyInputArr["postcountry"]) {
			postdata.postcountry = $("#postcountry").val();
			}
		$.ajax({
			url: "SubmitMyRegistration.php",
			dataType: "html",
			data: postdata,
			success: myRegistration.showUpdateResults,
			type: "POST"
			});			
	}

	this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
		//ajax success callback function
		$("#resultBoxDIV").html(data);
		dirtyInputArr = [];
		anyDirty = false;
		$("#regSubmitBTN").attr("disabled","disabled");
		document.getElementById("resultBoxDIV").scrollIntoView(false);
	}

}