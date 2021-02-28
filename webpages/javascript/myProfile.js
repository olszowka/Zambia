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
}