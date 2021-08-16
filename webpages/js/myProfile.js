//	Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var myProfile = new MyProfile;

function MyProfile() {
    var anyDirty = false;
    var pw;
    var cpw;
    var pwOK = true;
    var bioOK = true;
	var maxBioLen;
	var htmlbioused = false;
	var bio_updated = false;
	var $password;
	var $cpassword;
	var $submitBTN;
	var $bioTextarea;
	var $htmlbioTextarea;
	var $resultBoxDiv;
	var $badBio;
	
	
    this.validatePW = function validatePW() {
		pw = $password.val();
		cpw = $cpassword.val();
		if (pw && pw !== cpw) {
			$password.addClass("is-invalid");
			$cpassword.addClass("is-invalid");
			pwOK = false;
		} else {
			$password.removeClass("is-invalid");
			$cpassword.removeClass("is-invalid");
			pwOK = true;
		}
	};
    
	this.validateBio = function validateBio() {
		var biolen;
		if ($bioTextarea.length < 1) {
			return;
		}
		var bio = $bioTextarea.val();
		biolen = bio.length;
		if (biolen > maxBioLen) {
			$bioTextarea.addClass("is-invalid");
			$badBio.show();
			bioOK = false;
		} else {
			$bioTextarea.removeClass("is-invalid");
			$badBio.hide();
			bioOK = true;
		}
	};

	this.bioChange = function bioChange() {
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		anyDirty = true;
		this.validateBio();
		$("#submitBTN").prop("disabled", (!pwOK || !bioOK || (!anyDirty && !pw)));
	};

    this.anyChange = function anyChange(event) {
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		anyDirty = true;
		var $target = $(event.target);
		var targetId = $target.attr("id");
		if (targetId === "bioTXTA" || targetId === "htmlbioTXTA") {
			this.validateBio();
		} else if (targetId === "password" || targetId === "cpassword") {
			this.validatePW();
		}
		$("#submitBTN").prop("disabled", (!pwOK || !bioOK || (!anyDirty && !pw)));
    };

    this.initialize = function initialize() {
        //called when JQuery says My Profile page has loaded
		var boundAnyChange = this.anyChange.bind(this);
		$password = $("#password");
		$cpassword = $("#cpassword");
		$password.val("");
		this.validatePW();
		$submitBTN = $("#submitBTN");
		$submitBTN.button().prop("disabled", true);
		$bioTextarea = $("#bioTXTA");
		$badBio = $("#badBio");
		maxBioLen = $bioTextarea.data("maxLength");
		$htmlbioTextarea = $("#htmlbioTXTA");
		if ($htmlbioTextarea) {
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
						myProfile.bioChange();
					});
				},
				init_instance_callback: function (editor) {
					$(editor.getContainer()).find('button.tox-statusbar__wordcount').click();  // if you use jQuery
				}
			});
			htmlbioused = true;
		}
		this.validateBio();
		$("select.mycontrol").on("change", boundAnyChange);
		$("input.mycontrol[type='text']").on("input", boundAnyChange);
		$("input.mycontrol[type='password']").on("input", boundAnyChange);
		$(":checkbox.mycontrol").on("change", boundAnyChange);
		$(":radio.mycontrol").on("change", boundAnyChange);
		$("textarea.mycontrol").on("input", boundAnyChange);
		$resultBoxDiv = $("#resultBoxDIV");
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		
	};

	this.getLength = function getLength(data, textStatus, jqXHR) {
		//console.log(data);
		try {
			jsondata = JSON.parse(data);
		} catch (error) {
			console.log(error);
			return;
		}
		$bioTextarea.val(jsondata["bio"]);
		bio_updated = true;
		myProfile.validateBio();
		if (bioOK) 
			updateBUTTON();
	}

	this.updateBUTN = function updateBUTN() {
		$("#submitBTN").button('loading');
		if (htmlbioused && bio_updated == false) {
			tinymce.triggerSave();

			if ($htmlbioTextarea.val().length > maxBioLen) {
				var postdata = {
					ajax_request_action: "convert_bio",
					htmlbio: $htmlbioTextarea.val()
				};
				$.ajax({
					url: "SubmitMyContact.php",
					dataType: "html",
					data: postdata,
					success: myProfile.getLength,
					error: myProfile.showAjaxError,
					type: "POST"
				});
				return;
			}
		}
		bio_updated = false;

        var postdata = {
            ajax_request_action: "update_participant"
        };
        $(".mycontrol").each(function() { // this is element
        	var $elem = $(this);
			if ($elem.is(":disabled") || $elem.attr("readonly")) {
				return;
			}
        	var name = $elem.attr("name");
        	if (name === "cpassword") {
        		return;
			}
        	if ($elem.attr("type") === "radio") {
        		if ($elem.prop("checked") && !$elem.prop("defaultChecked")) {
					postdata[$elem.attr("name")] = $elem.val();
				}
			} else if ($elem.prop("tagName") === "SELECT") {
				if ($elem.val() !== $elem.find("option").filter(function () { return this.defaultSelected; }).attr("value")) {
					postdata[$elem.attr("name")] = $elem.val();
				}
			} else if ($elem.attr("type") === "checkbox") {
        		if ($elem.prop("defaultChecked") !== $elem.is(":checked")) {
					postdata[$elem.attr("id")] = $elem.is(":checked") ? 1 : 0;
				}
			} else { // text or textarea
				if ($elem.prop("defaultValue") !== $elem.val()) {
					postdata[$elem.attr("name")] = $elem.val();
				}
			}
		});
        $.ajax({
            url: "SubmitMyContact.php",
            dataType: "html",
            data: postdata,
            success: myProfile.showUpdateResults,
            error: myProfile.showAjaxError,
            type: "POST"
        });
    };

	this.showUpdateBio = function showUpdateBio(data, textStatus, jqXHR) {
		//console.log(data);
		try {
			jsondata = JSON.parse(data);
		} catch (error) {
			console.log(error);
			return;
		}
		$("#bioTXTA").val(jsondata["bio"]);
	};

    this.showUpdateResults = function showUpdateResults(data, textStatus, jqXHR) {
        //ajax success callback function
		$resultBoxDiv.html(data).css("visibility", "visible");
		$password.val("");
		$cpassword.val("");
        anyDirty = false;
		$submitBTN.button('reset');
        setTimeout(function () {
			$submitBTN.button().prop("disabled", true);
        }, 0);
		document.getElementById("resultBoxDIV").scrollIntoView(false);
		if (htmlbioused) {
			$.ajax({
				url: "SubmitMyContact.php",
				dataType: "html",
				data: ({
					ajax_request_action: "fetch_bio"
				}),
				success: myProfile.showUpdateBio,
				type: "POST"
			});
		}
    };

	this.showErrorMessage = function showErrorMessage(message) {
		content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">` + message + `</div></div></div>`;
		$resultBoxDiv.html(content).css("visibility", "visible");
		document.getElementById("resultBoxDIV").scrollIntoView(false);
	};

    this.showAjaxError = function showAjaxError(data, textStatus, jqXHR) {
        var content;
        if (data && data.responseText) {
            content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
        } else {
            content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
        }
        $resultBoxDiv.html(content).css("visibility", "visible");
		document.getElementById("resultBoxDIV").scrollIntoView(false);
		uploadlock = false;
	};
}