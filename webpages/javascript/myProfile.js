//	Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var myProfile = new MyProfile;

function MyProfile() {
    var anyDirty = false;
    var pw;
    var cpw;
    var pwOK = true;
    var bioOK = true;
    var maxBioLen;
	var $password;
	var $cpassword;
	var $submitBTN;
	var $bioTextarea;
	var $resultBoxDiv;
	var $uploadZone = null;
	var $uploadPhoto = null;
	var $uploadPhotoDelete = null;
	var $uploadPhotoStatus = null;
	var uploadlock = false;
	
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
        if ($bioTextarea.length < 1) {
            return;
        }
    	var bio = $bioTextarea.val();
		if (bio.length > maxBioLen) {
			$bioTextarea.addClass("is-invalid");
			bioOK = false;
		} else {
			$bioTextarea.removeClass("is-invalid");
			bioOK = true;
		}
	};

    this.anyChange = function anyChange(event) {
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		anyDirty = true;
		var $target = $(event.target);
		var targetId = $target.attr("id");
		if (targetId === "bioTXTA") {
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
		maxBioLen = $bioTextarea.data("maxLength");
		this.validateBio();
		$("select.mycontrol").on("change", boundAnyChange);
		$("input.mycontrol[type='text']").on("input", boundAnyChange);
		$("input.mycontrol[type='password']").on("input", boundAnyChange);
		$(":checkbox.mycontrol").on("change", boundAnyChange);
		$(":radio.mycontrol").on("change", boundAnyChange);
		$("textarea.mycontrol").on("input", boundAnyChange);
		$resultBoxDiv = $("#resultBoxDIV");
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		$uploadZone = document.getElementById("photoUploadArea");
		$uploadPhoto = document.getElementById("uploadedPhoto");
		$uploadPhotoDelete = document.getElementById("deleteUploadPhoto");
		$uploadPhotoStatus = document.getElementById("uploadedPhotoStatus");
		if ($uploadPhotoDelete) {
			$uploadPhotoDelete.addEventListener("click", function (e) {
				if (uploadlock) {
					message = "Upload in progress, please wait";
				} else {
					myProfile.deleteuploadedphoto();
				}
			});
		}
		if (window.File && window.FileReader && window.FileList && window.Blob) {
			// hover
			$uploadPhoto.addEventListener("dragenter", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$uploadZone.classList.remove('alert-secondary');
				$uploadZone.classList.add('alert-dark');
			});
			$uploadPhoto.addEventListener("dragleave", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$uploadZone.classList.remove('alert-dark');
				$uploadZone.classList.add('alert-secondary');
			});
			// upload
			$uploadPhoto.addEventListener("dragover", function (e) {
				e.preventDefault();
				e.stopPropagation();
			});
			$uploadPhoto.addEventListener("drop", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$uploadZone.classList.remove('alert-dark');
				$uploadZone.classList.add('alert-secondary');
				message = "";
				if (uploadlock) {
					message = "Upload already in progress, please wait";
				} else if (e.dataTransfer.files.length == 1) {
					f = e.dataTransfer.files[0];
					if (!f.type.match(/image\/(jpeg|png)/i)) {
						message = "Only jpg and png files allowed";
					} else if (f.name.match(/\.(jpg|jpeg|png)$/i))
						myProfile.starttransfer(f);
					else
						message = "Only jpg and png files allowed";
				} else {
					message = "Drag only one picture";
				}
				if (message) {
					var content;
					content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">` + message + `.</div></div></div>`;
					$resultBoxDiv.html(content).css("visibility", "visible");
					document.getElementById("resultBoxDIV").scrollIntoView(false);
				}
			});
		};
	};

    this.updateBUTN = function updateBUTN() {
		$("#submitBTN").button('loading');
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

	this.transfercomplete = function transfercomplete(data, textStatus, jqXHR) {
		uploadlock = false;
		message = "";
		//console.log(data);
		try {
			data_json = JSON.parse(data);
		} catch (error) {
			console.log(error);
		}

		//console.log(data_json);
		if (data_json.hasOwnProperty("message"))
			message = data_json.message;
		// enable delete button
		if ($uploadPhotoDelete)
			$uploadPhotoDelete.disabled = false;

		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$uploadPhoto.src = data_json["image"];
		}

		if (data_json.hasOwnProperty("photostatus")) {
			$uploadPhotoStatus.innerHTML = data_json["photostatus"];
		}
		if (message != "") {
			if (message.startsWith("Error"))
				alert_type = "alert-danger";
			else
				alert_type = "alert-success";
			content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
			$resultBoxDiv.html(content).css("visibility", "visible");
			document.getElementById("resultBoxDIV").scrollIntoView(false);
		}
	}

	this.starttransfer = function starttransfer(f) {
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		uploadlock = true;
		postdata = new FormData();
		postdata.append('ajax_request_action', 'uploadPhoto');
		postdata.append('photo', f);

		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "html",
			data: postdata,
			success: myProfile.transfercomplete,
			error: myProfile.showAjaxError,
			async: true,
			cache: false,
			contentType: false,
			processData: false,
			timeout: 60000,
			type: "POST"
		});
	};

	this.deleteuploadedcomplete = function deleteuploadedcomplete(data, textStatus, jqXHR) {
		uploadlock = false;
		message = "";
		//console.log(data);
		try {
			data_json = JSON.parse(data);
		} catch (error) {
			console.log(error);
		}

		//console.log(data_json);
		if (data_json.hasOwnProperty("message"))
			message = data_json.message;
		// disable delete button
		if ($uploadPhotoDelete)
			$uploadPhotoDelete.disabled = true;
		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$uploadPhoto.src = data_json["image"];
		}
		if (data_json.hasOwnProperty("photostatus")) {
			$uploadPhotoStatus.innerHTML = data_json["photostatus"];
		}

		if (message != "") {
			if (message.startsWith("Error"))
				alert_type = "alert-danger";
			else
				alert_type = "alert-success"
			content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';
			$resultBoxDiv.html(content).css("visibility", "visible");
			document.getElementById("resultBoxDIV").scrollIntoView(false);
		}
	}

	this.deleteuploadedphoto = function deleteuploadedphoto() {
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		uploadlock = true;
		var postdata = {
			ajax_request_action: "delete_uploaded_photo"
		};
		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "html",
			data: postdata,
			success: myProfile.deleteuploadedcomplete,
			error: myProfile.showAjaxError,
			type: "POST"
		});
	};

}