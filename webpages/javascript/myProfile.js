var myProfile = new MyProfile;

function MyProfile() {
	var dirtyInputArr = [];
	var anyDirty = false;
	var pw;
	var cpw;
	var pwOK = true;
	var bioOK = true;
	
	this.anyChange = function anyChange(element) {
		$("#resultBoxDIV").html("&nbsp;").css("visibility", "hidden");
		pw = $("#password").val();
		if (element != "password" && element != "cpassword") {
				anyDirty = true;
				dirtyInputArr[element] = true;
				}
			else {
				cpw = $("#cpassword").val();
				if (pw && pw!=cpw) {
						$("#badPassword").show();
						$("#passGroup").addClass("error");
						$("#submitBTN").attr("disabled","disabled");
						pwOK = false;
						return;
						}
					else {
						$("#badPassword").hide();
						$("#passGroup").removeClass("error");
						$("#submitBTN").removeAttr("disabled");
						pwOK = true;
						}
				}
		if (element == "htmlbioTXTA") {
			if ($("#htmlbioTXTA").val().length > maxBioLen) {
					$("#badBio").show();
				    $("#bioGroup").addClass("error");
					$("#submitBTN").attr("disabled","disabled");
					bioOK = false;
				}
				else {
					$("#badBio").hide();
					$("#bioGroup").removeClass("error");
					$("#submitBTN").removeAttr("disabled");
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
		this.anyChange("password");
		this.anyChange("htmlbioTXTA");
		dirtyInputArr = [];
		$("#submitBTN").button().attr("disabled","disabled");
		//window.status="Reached initializeMyProfile.";
		tinymce.init({
			selector: 'textarea#htmlbioTXTA',
			plugins: 'table wordcount fullscreen advlist link preview searchreplace autolink charmap hr nonbreaking visualchars ',
			browser_spellcheck: true,
			contextmenu: false,
			height: 400,
			min_height: 200,
			menubar: false,
			toolbar: [
				'undo redo | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | forecolor backcolor | link| preview fullscreen ',
				'searchreplace | alignleft aligncenter alignright alignjustify | outdent indent'
			],
			toolbar_mode: 'wrap',
			content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
			placeholder: 'Type custom content here...',
			setup: function (ed) {
				ed.on('change', function (e) {
					bioDirty = true;
					myProfile.anyChange("htmlbioTXTA");
				});
			},
			init_instance_callback: function (editor) {
				$(editor.getContainer()).find('button.tox-statusbar__wordcount').click();  // if you use jQuery
			}
		});
	}

	this.updateBUTN = function updateBUTN() {
		//debugger;
		$("#submitBTN").button('loading');
		var postdata = {
			ajax_request_action : "update_participant"
			};
		if (pw)
			postdata.password = pw;
		if (dirtyInputArr["htmlbioTXTA"]) {
			tinymce.triggerSave();
			postdata.htmlbio = $("#htmlbioTXTA").val();
			}
		if (dirtyInputArr["interested"])
			postdata.interested = $("#interested").val();
		if (dirtyInputArr["share_email"])
			postdata.share_email = $("#share_email").val();
		if (dirtyInputArr["use_photo"])
			postdata.use_photo = $("#use_photo").val();
		if (dirtyInputArr["bestway"])
			if ($("#bwemailRB").attr('checked'))
					postdata.bestway = "Email";
				else if ($("#bwpmailRB").attr('checked'))
					postdata.bestway = "Postal mail";
				else if ($("#bwphoneRB").attr('checked'))
					postdata.bestway = "Phone";
		if (dirtyInputArr["pubsname"])
			postdata.pubsname = $("#pubsname").val();
		if (dirtyInputArr["firstname"])
			postdata.firstname = $("#firstname").val();
		if (dirtyInputArr["lastname"])
			postdata.lastname = $("#lastname").val();
		if (dirtyInputArr["badgename"])
			postdata.badgename = $("#badgename").val();
		if (dirtyInputArr["phone"])
			postdata.phone = $("#phone").val();
		if (dirtyInputArr["email"])
			postdata.email = $("#email").val();
		if (dirtyInputArr["postaddress1"])
			postdata.postaddress1 = $("#postaddress1").val();
		if (dirtyInputArr["postaddress2"])
			postdata.postaddress2 = $("#postaddress2").val();
		if (dirtyInputArr["postcity"])
			postdata.postcity = $("#postcity").val();
		if (dirtyInputArr["poststate"])
			postdata.poststate = $("#poststate").val();
		if (dirtyInputArr["postzip"])
			postdata.postzip = $("#postzip").val();
		if (dirtyInputArr["postcountry"])
			postdata.postcountry = $("#postcountry").val();
		$('[id^="credentialCHK"]').each( function() {
			var id = $(this).attr("id");
			if (dirtyInputArr[id])
				postdata[id] = ($(this).attr("checked")=="checked");
			});
		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "html",
			data: postdata,
			success: myProfile.getUpdateResults,
			type: "POST"
			});			
	}

	this.getUpdateResults = function getUpdateResults(data, textStatus, jqXHR) {
		//ajax success callback function
		$("#resultBoxDIV").html(data).css("visibility", "visible");
		$("#password").val("");
		$("#cpassword").val("");
		dirtyInputArr = [];
		anyDirty = false;
		$("#submitBTN").button('reset');
		setTimeout(function() {$("#submitBTN").button().attr("disabled","disabled");}, 0);
		//$("#submitBTN").html("Update").removeClass("disabled");
		document.getElementById("resultBoxDIV").scrollIntoView(false);
		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "xml",
			data: ({
				ajax_request_action: "fetch_bio"
			}),
			success: myProfile.showUpdateResults,
			type: "GET"
		});			
	}

	this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
		//ajax success callback function
		var node = data.firstChild.firstChild.firstChild;
		$("#bioTXTA").val(node.getAttribute("bio"));
	}
}