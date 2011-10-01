var myProfile = new myProfile;

function myProfile() {
	var dirtyInputArr = [];
	var anyDirty = false;
	var pw;
	var cpw;
	var pwOK = true;
	var bioOK = true;
	
	this.anyChange = function anyChange(element) {
		$("#resultBoxDIV").html("&nbsp;");
		pw = $("#password").val();
		if (element != "password" && element != "cpassword") {
				anyDirty = true;
				dirtyInputArr[element] = true;
				}
			else {
				cpw = $("#cpassword").val();
				if (pw && pw!=cpw) {
						$("#badPassword").addClass("revealedWarning");
						$("#badPassword").removeClass("hiddenWarning");
						$("#submitBTN").attr("disabled","disabled");
						pwOK = false;
						return;
						}
					else {
						$("#badPassword").addClass("hiddenWarning");
						$("#badPassword").removeClass("revealedWarning");
						pwOK = true;
						}
				}
		if (element == "bioTXTA") {
			if ($("#bioTXTA").val().length > maxBioLen) {
					$("#badBio").addClass("revealedWarning");
					$("#badBio").removeClass("hiddenWarning");
					$("#submitBTN").attr("disabled","disabled");
					bioOK = false;
					}
				else {
					$("#badBio").addClass("hiddenWarning");
					$("#badBio").removeClass("revealedWarning");
					bioOK = true;
					}
			}
		if (pwOK && bioOK && (anyDirty || pw)) {
			$("#submitBTN").removeAttr("disabled");
			}
	}

	this.initialize = function initialize() {
		//called when JQuery says My Profile page has loaded
		//just a filler for now
		//debugger;
		$("#password").val("");
		dirtyInputArr = [];
		$("#submitBTN").attr("disabled","disabled");
		//window.status="Reached initializeMyProfile.";
	}

	this.updateBUTN = function updateBUTN() {
		//debugger;
		var postdata = {
			ajax_request_action : "update_participant"
			};
		if (pw)
			postdata.password = pw;
		if (dirtyInputArr["bioTXTA"]) {
			postdata.bioText = $("#bioTXTA").val();
			}
		if (dirtyInputArr["interested"])
			postdata.interested = $("#interested").val();
		if (dirtyInputArr["share_email"])
			postdata.share_email = $("#share_email").val();
		if (dirtyInputArr["bestway"])
			if ($("#bwemailRB").attr('checked'))
					postdata.bestway = "Email";
				else if ($("#bwpmailRB").attr('checked'))
					postdata.bestway = "Postal mail";
				else if ($("#bwphoneRB").attr('checked'))
					postdata.bestway = "Phone";
		if (dirtyInputArr["pubsname"])
			postdata.pubsname = $("#pubsname").val();
		$('[id^="credentialCHK"]').each( function() {
			var id = $(this).attr("id");
			if (dirtyInputArr[id])
				postdata[id] = ($(this).attr("checked")=="checked");
			});
		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "html",
			data: postdata,
			success: myProfile.showUpdateResults,
			type: "POST"
			});			
	}

	this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
		//ajax success callback function
		$("#resultBoxDIV").html(data);
		$("#password").val("");
		$("#cpassword").val("");
		dirtyInputArr = [];
		anyDirty = false;
		$("#submitBTN").attr("disabled","disabled");
		document.getElementById("resultBoxDIV").scrollIntoView(false);
	}

}