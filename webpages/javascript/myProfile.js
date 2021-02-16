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
	var $uploadedPhoto = null;
	var $uploadChooseFile = null;
	var $chooseFileName = null;
	var $uploadPhotoDelete = null;
	var $uploadUpdatedPhoto = null;
	var $uploadPhotoStatus = null;
	var uploadlock = false;
	var cropper = null;
	
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
		$uploadChooseFile = document.getElementById("uploadPhoto");
		$chooseFileName = document.getElementById("chooseFileName");
		$uploadedPhoto = document.getElementById("uploadedPhoto");
		$uploadPhotoDelete = document.getElementById("deleteUploadPhoto");
		$uploadUpdatedPhoto = document.getElementById("updateUploadPhoto");
		$uploadPhotoStatus = document.getElementById("uploadedPhotoStatus");
		if (document.getElementById("default_photo").value == '0')
			document.getElementById("crop").style.display = 'block';
		
		if ($uploadChooseFile) {
			$uploadChooseFile.addEventListener("click", function (e) {
				$chooseFileName.value = null;
				$chooseFileName.click();
			});
			$chooseFileName.addEventListener("change", function (e) {
				myProfile.loaduploadimage(e.target.files[0]);
			});
		}
		if (window.File && window.FileReader && window.FileList && window.Blob) {
			// hover
			$uploadedPhoto.addEventListener("dragenter", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$uploadZone.classList.remove('alert-secondary');
				$uploadZone.classList.add('alert-dark');
			});
			$uploadedPhoto.addEventListener("dragleave", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$uploadZone.classList.remove('alert-dark');
				$uploadZone.classList.add('alert-secondary');
			});
			// upload
			$uploadedPhoto.addEventListener("dragover", function (e) {
				e.preventDefault();
				e.stopPropagation();
			});
			$uploadedPhoto.addEventListener("drop", function (e) {
				e.preventDefault();
				e.stopPropagation();
				$uploadZone.classList.remove('alert-dark');
				$uploadZone.classList.add('alert-secondary');
				if (e.dataTransfer.files.length == 1) {
					f = e.dataTransfer.files[0];
					if (!f.type.match(/image\/(jpeg|png)/i)) {
						myProfile.showErrorMessage("Only jpg and png files allowed");
					} else if (f.name.match(/\.(jpg|jpeg|png)$/i))
						myProfile.loaduploadimage(f);
					else
						myProfile.showErrorMessage("Only jpg and png files allowed");
				} else {
					myProfile.showErrorMessage("Drag only one picture");
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
		$uploadPhotoDelete.style.display = 'block';
		$uploadUpdatedPhoto.style.display = 'none';
		document.getElementById("crop").style.display = 'block';
		document.getElementById("save_crop").style.display = 'none';
		document.getElementById("rotate_left").style.display = 'none';
		document.getElementById("rotate_right").style.display = 'none';
		document.getElementById("cancel_crop").style.display = 'none';

		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$uploadedPhoto.src = data_json["image"];
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

	this.starttransfer = function starttransfer() {
		if (uploadlock) {
			myProfile.showErrorMessage("Upload in progress, please wait");
			return false;
		}
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		uploadlock = true;
		
		var postdata = {
			ajax_request_action: 'uploadPhoto',
			photo: $uploadedPhoto.src
		};

		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "html",
			data: postdata,
			success: myProfile.transfercomplete,
			error: myProfile.showAjaxError,
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
		$uploadPhotoDelete.style.display = 'none';
		document.getElementById("crop").style.display = 'none';
		document.getElementById("save_crop").style.display = 'none';
		document.getElementById("rotate_left").style.display = 'none';
		document.getElementById("rotate_right").style.display = 'none';
		document.getElementById("cancel_crop").style.display = 'none';

		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$uploadedPhoto.src = data_json["image"];
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
		if (uploadlock) {
			myProfile.showErrorMessage("Upload in progress, please wait");
			return false;
		}
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

	this.crop = function crop() {
		if (cropper) {
			cropper.destroy();
			cropper = null;
		}
		document.getElementById("crop").style.display = 'none';
		document.getElementById("save_crop").style.display = 'block';
		document.getElementById("rotate_left").style.display = 'block';
		document.getElementById("rotate_right").style.display = 'block';
		document.getElementById("cancel_crop").style.display = 'block';
		$uploadUpdatedPhoto.style.display = 'none';

		cropper = new Croppie($uploadedPhoto, {
			boundary: { width: 400, height: 400, 'margin-right': 'auto', 'margin-left': 'auto' },
			enableResize: true,
			enforceBoundary: true,
			enableZoom: true,
			viewport: { width: 400, height: 400, type: 'square' },
			enableOrientation: true,
		});
	}

	this.rotate = function rotate(deg) {
		if (cropper) {
			cropper.rotate(deg);
		}
	};

	this.cancelcrop = function cancelcrop() {
		if (cropper) {
			cropper.destroy();
			cropper = null;
		}
		document.getElementById("crop").style.display = 'block';
		document.getElementById("save_crop").style.display = 'none';
		document.getElementById("rotate_left").style.display = 'none';
		document.getElementById("rotate_right").style.display = 'none';
		document.getElementById("cancel_crop").style.display = 'none';
		$uploadUpdatedPhoto.style.display = 'block';
	};

	this.savecrop = function savecrop() {
		if (cropper) {
			cropper.result({ type: 'base64', size: 'original', format: 'png', quality: 1, circle: false }).then(function (blob) {
				//console.log(blob);
				$uploadedPhoto.src = blob;
			});
			cropper.destroy();
			cropper = null;
			document.getElementById("crop").style.display = 'block';
			document.getElementById("save_crop").style.display = 'none';
			document.getElementById("rotate_left").style.display = 'none';
			document.getElementById("rotate_right").style.display = 'none';
			document.getElementById("cancel_crop").style.display = 'none';
			$uploadUpdatedPhoto.style.display = 'block';
		}
	};

	this.loaduploadimage = function loaduploadimage(file) {
		if (cropper) {
			cropper.destroy();
			cropper = null;
		}
		if (!(file.type.match('image/jp.*') || file.type.match('image/png.*'))) {
			alert("Only jpeg/jpg or png images allowed");
		}
		else {
			var reader = new FileReader();
			reader.onload = (function (thefile) {
				return function (e) {
					$uploadedPhoto.src = e.target.result;
					document.getElementById("crop").style.display = 'block';
				}
			})(file);

			reader.readAsDataURL(file);
		}

		pickedfile = $chooseFileName.value;
		$uploadUpdatedPhoto.style.display = 'block';
	}
}