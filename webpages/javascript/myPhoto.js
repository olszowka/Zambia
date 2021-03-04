//	Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var myPhoto = new MyPhoto;

function MyPhoto() {
	var $resultBoxDiv = null;
	var $uploadZone = null;
	var $uploadedPhoto = null;
	var $approvedPhoto = null;
	var $uploadChooseFile = null;
	var $chooseFileName = null;
	var $uploadPhotoDelete = null;
	var $uploadUpdatedPhoto = null;
	var $uploadPhotoStatus = null;
	var $approvedPhotoDelete = null;
	var uploadlock = false;
	var cropper = null;

	this.initialize = function initialize() {
		$resultBoxDiv = $("#resultBoxDIV");
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		$uploadZone = document.getElementById("photoUploadArea");
		$uploadChooseFile = document.getElementById("uploadPhoto");
		$chooseFileName = document.getElementById("chooseFileName");
		$uploadedPhoto = document.getElementById("uploadedPhoto");
		$approvedPhoto = document.getElementById("approvedPhoto");
		$uploadPhotoDelete = document.getElementById("deleteUploadPhoto");
		$approvedPhotoDelete = document.getElementById("deleteApprovedPhoto");
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
				myPhoto.loaduploadimage(e.target.files[0]);
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
						myPhoto.showErrorMessage("Only jpg and png files allowed");
					} else if (f.name.match(/\.(jpg|jpeg|png)$/i))
						myPhoto.loaduploadimage(f);
					else
						myPhoto.showErrorMessage("Only jpg and png files allowed");
				} else {
					myPhoto.showErrorMessage("Drag only one picture");
				}
				
			});
		};
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
			myPhoto.showErrorMessage("Upload in progress, please wait");
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
			success: myPhoto.transfercomplete,
			error: myPhoto.showAjaxError,
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
			myPhoto.showErrorMessage("Upload in progress, please wait");
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
			success: myPhoto.deleteuploadedcomplete,
			error: myPhoto.showAjaxError,
			type: "POST"
		});
	};

	this.deleteapprovedcomplete = function deleteapprovedcomplete(data, textStatus, jqXHR) {
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
		$approvedPhotoDelete.style.display = 'none';

		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$approvedPhoto.src = data_json["image"];
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

	this.deleteapprovedphoto = function deleteapprovedphoto() {
		if (uploadlock) {
			myPhoto.showErrorMessage("Upload in progress, please wait");
			return false;
		}
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		uploadlock = true;
		var postdata = {
			ajax_request_action: "delete_approved_photo"
		};
		$.ajax({
			url: "SubmitMyContact.php",
			dataType: "html",
			data: postdata,
			success: myPhoto.deleteapprovedcomplete,
			error: myPhoto.showAjaxError,
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