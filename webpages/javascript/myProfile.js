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
	var uploadLock = false;
	
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
		if (window.File && window.FileReader && window.FileList && window.Blob) {
			$uploadZone = document.getElementById("photoUploadArea");
			$uploadPhoto = document.getElementById("uploadedPhoto");
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
				$message = "";
				if (uploadLock) {
					message = "Upload already in progress, please wait";
				} else if (e.dataTransfer.files.length == 1) {
					f = e.dataTransfer.files[0];
					if (!f.type.match(/image\/(jpeg|png)/i)) {
						$message = "Only jpg and png files allowed";
					} else if (f.name.match(/\.(jpg|jpeg|png)$/i))
						myProfile.starttransfer(f);
					else
						$message = "Only jpg and png files allowed";
				} else {
					$message = "Drag only one picture";
				}
				if ($message) {
					var content;
					content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">$message.</div></div></div>`;
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
	};

	this.transfercomplete = function transfercomplete(data, textStatus, jqXHR) {
	}

	this.starttransfer = function starttransfer(f) {
		$resultBoxDiv.html("&nbsp;").css("visibility", "hidden");
		uploadLock = true;
		xhr = new XMLHttpRequest();
		xhr.open("POST", "SubmitMyContact.php");
		xhr.onload = myProfile.transfercomplete;
		xhr.onerror = myProfile.showAjaxError;
		data = new FormData();
		data.append('ajax_request_action', 'uploadPhoto');
		data.append('photo', f);
		console.log(data);
		xhr.send(data);
	};

}