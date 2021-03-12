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
	var savedDeleteButtonDisplay = 'none';
	var savedUploadButtonDisplay = 'none';
	var cropper = null;
	var $cropBTN = null;
	var $cropsaveBTN = null;
	var $cropleftBTN = null;
	var $croprightBTN = null;
	var $cropcancelBTN = null;

	const crop_hideall = 0;
	const crop_showbtn = 1;
	const crop_showdirections = 2;

	this.initialize = function initialize() {
		// initialize message box for use by both ajax return messsages and javascript error messages
		$resultBoxDiv = document.getElementById("resultBoxDIV");
		myPhoto.clearMessage();

		// get element shortcuts
		$uploadZone = document.getElementById("photoUploadArea");
		$uploadChooseFile = document.getElementById("uploadPhoto");
		$chooseFileName = document.getElementById("chooseFileName");
		$uploadedPhoto = document.getElementById("uploadedPhoto");
		$approvedPhoto = document.getElementById("approvedPhoto");
		$uploadPhotoDelete = document.getElementById("deleteUploadPhoto");
		$approvedPhotoDelete = document.getElementById("deleteApprovedPhoto");
		$uploadUpdatedPhoto = document.getElementById("updateUploadPhoto");
		$uploadPhotoStatus = document.getElementById("uploadedPhotoStatus");
		$cropBTN = document.getElementById("crop");
		$cropsaveBTN = document.getElementById("save_crop");
		$cropleftBTN = document.getElementById("rotate_left");
		$croprightBTN = document.getElementById("rotate_right");
		$cropcancelBTN = document.getElementById("cancel_crop");

		// set initial crop button display settings
		myPhoto.changeCropDisplay(document.getElementById("default_photo").value == '0' ? crop_showbtn : crop_hideall);

		// if photos are enabled in the configuration
		if ($uploadChooseFile) {
			$uploadChooseFile.addEventListener("click", function (e) {
				$chooseFileName.value = null;
				$chooseFileName.click();
			});
			$chooseFileName.addEventListener("change", function (e) {
				myPhoto.loaduploadimage(e.target.files[0]);
			});
		}

		// if browser supports drag and drop of photos
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

	this.setMessage = function setMessage(content) {
		$resultBoxDiv.innerHTML = content
		$resultBoxDiv.style.visibility = "visible";
		$resultBoxDiv.scrollIntoView(false);
	}

	this.clearMessage = function clearMessage() {
		$resultBoxDiv.innerHTML = "&nbsp;";
		$resultBoxDiv.style.visibility = "hidden";
	}

	this.changeCropDisplay = function changeCropDisplay(cropstyle) { // 0 = hide all, 1 = show crop button, 2 = show crop directions
		$cropBTN.style.display = cropstyle == 1 ? 'block' : 'none';
		$cropsaveBTN.style.display = cropstyle == 2 ? 'block' : 'none';
		$cropleftBTN.style.display = cropstyle == 2 ? 'block' : 'none';
		$croprightBTN.style.display = cropstyle == 2 ? 'block' : 'none';
		$cropcancelBTN.style.display = cropstyle == 2 ? 'block' : 'none';
	}

	this.showErrorMessage = function showErrorMessage(message) {
		content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">` + message + `</div></div></div>`;
		myPhoto.setMessage(content);
	};

	this.showMessage = function showMessage(message) {
		if (message == "")
			return;
		if (message.startsWith("Error"))
			alert_type = "alert-danger";
		else
			alert_type = "alert-success";
		content = '<div class="row mt-3"><div class="col-12"><div class="alert ' + alert_type + '" role="alert">' + message + '</div></div></div>';

		myPhoto.setMessage(content);
	};

	this.showAjaxError = function showAjaxError(data, textStatus, jqXHR) {
		uploadlock = false;
        if (data && data.responseText)
			myPhoto.showErrorMessage(data.responseText);
        else
			myPhoto.showErrorMessage("An error occurred on the server.");
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
		myPhoto.changeCropDisplay(crop_showbtn);

		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$uploadedPhoto.src = data_json["image"];
		}

		if (data_json.hasOwnProperty("photostatus")) {
			$uploadPhotoStatus.innerHTML = data_json["photostatus"];
		}
		myPhoto.showMessage(message);
	}

	this.starttransfer = function starttransfer() {
		if (uploadlock) {
			myPhoto.showErrorMessage("Upload in progress, please wait");
			return false;
		}
		myPhoto.clearMessage();
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
		savedDeleteButtonDisplay = 'none';
		myPhoto.changeCropDisplay(crop_hideall);

		// reload default photo
		if (data_json.hasOwnProperty("image")) {
			$uploadedPhoto.src = data_json["image"];
		}
		if (data_json.hasOwnProperty("photostatus")) {
			$uploadPhotoStatus.innerHTML = data_json["photostatus"];
		}

		myPhoto.showMessage(message);
	}

	this.deleteuploadedphoto = function deleteuploadedphoto() {
		if (uploadlock) {
			myPhoto.showErrorMessage("Upload in progress, please wait");
			return false;
		}

		myPhoto.clearMessage();
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

		myPhoto.showMessage(message);
	}

	this.deleteapprovedphoto = function deleteapprovedphoto() {
		if (uploadlock) {
			myPhoto.showErrorMessage("Upload in progress, please wait");
			return false;
		}
		myPhoto.clearMessage();
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
		myPhoto.clearMessage();
		if (cropper) {
			cropper.destroy();
			cropper = null;
		}
		myPhoto.changeCropDisplay(crop_showdirections);
		savedUploadButtonDisplay = $uploadUpdatedPhoto.style.display;
		savedDeleteButtonDisplay = $uploadPhotoDelete.style.display;
		$uploadUpdatedPhoto.style.display = 'none';
		$uploadPhotoDelete.style.display = 'none';

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
		myPhoto.changeCropDisplay(crop_showbtn);
		$uploadUpdatedPhoto.style.display = savedUploadButtonDisplay;
		$uploadPhotoDelete.style.display = savedDeleteButtonDisplay;
	};

	this.savecrop = function savecrop() {
		if (cropper) {
			cropper.result({ type: 'base64', size: 'original', format: 'png', quality: 1, circle: false }).then(function (blob) {
				//console.log(blob);
				$uploadedPhoto.src = blob;
			});
			cropper.destroy();
			cropper = null;
			myPhoto.changeCropDisplay(crop_showbtn);
			$uploadUpdatedPhoto.style.display = 'block';
			$uploadPhotoDelete.style.display = savedDeleteButtonDisplay;
		}
	};

	this.loaduploadimage = function loaduploadimage(file) {
		if (cropper) {
			cropper.destroy();
			cropper = null;
			$uploadPhotoDelete.style.display = savedDeleteButtonDisplay;
		}
		myPhoto.clearMessage();
		if (!(file.type.match('image/jp.*') || file.type.match('image/png.*'))) {
			alert("Only jpeg/jpg or png images allowed");
		} else {
			var reader = new FileReader();
			reader.onload = (function (thefile) {
				return function (e) {
					$uploadedPhoto.src = e.target.result;
					myPhoto.changeCropDisplay(crop_showbtn);
				}
			})(file);

			reader.readAsDataURL(file);
		}

		pickedfile = $chooseFileName.value;
		$uploadUpdatedPhoto.style.display = 'block';
	};
}