// Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
var bioDirty = false;
var pnameDirty = false;
var snotesDirty = false;
var lastnameDirty = false;
var firstnameDirty = false;
var badgenameDirty = false;
var phoneDirty = false;
var emailDirty = false;
var post1Dirty = false;
var post2Dirty = false;
var postcityDirty = false;
var poststateDirty = false;
var postzipDirty = false;
var postcountryDirty = false;
var originalInterested = 0;
var fbadgeid;
var resultsHidden = true;

function anyChange() {
	var x = $("#password").val();
	var y = $("#cpassword").val();
	if (!x && !y && ($("#interested").val() != originalInterested || 
		bioDirty || pnameDirty || snotesDirty || lastnameDirty || firstnameDirty ||
		phoneDirty || emailDirty || post1Dirty || post2Dirty ||
		postcityDirty || poststateDirty || postzipDirty || postcountryDirty ||
		badgenameDirty) ||
		(x && x == y) ) { 
			$("#updateBUTN").prop("disabled", false);
			}
		else {
			$("#updateBUTN").prop("disabled", true);
			}
	var z = $("#passwordsDontMatch");
	if (x && y && x!=y)
			z.show();
		else
			z.hide();
}

function checkIfDirty(mode) {
	//called when user clicks "Search for participants" on the page
	//debugger;
	if (!mode && (bioDirty || pnameDirty || snotesDirty || lastnameDirty || firstnameDirty ||
		phoneDirty || emailDirty || post1Dirty || post2Dirty ||
		postcityDirty || poststateDirty || postzipDirty || postcountryDirty ||
		badgenameDirty ||
		$("#interested").val() != originalInterested ||
		($("#password").val()) && 
			$("#cpassword").val())) {
			$("#unsavedWarningDIV").modal('show');
			$("#cancelOpenSearchBUTN").blur();
			return false;	
			}
	if (mode)
		$("#unsavedWarningDIV").modal('hide');
	if (mode=="cancel")
		return false;
	return true;
}

function chooseParticipant(badgeid, override) {
	//debugger;
	if (!checkIfDirty(override)) {
		$('#warnName').html($("#pname").val());
		$('#warnNewBadgeID').html(badgeid);
		return;
		}
	var badgeidJQSel = badgeid.replace(/[']/g,"\\'").replace(/["]/g,'\\"');
	hideSearchResults();
	$("#badgeid").val($("#bidSPAN_" + badgeidJQSel).html());
	var lastname = $("#lastnameHID_" + badgeidJQSel).val();
	$("#lastname").val(lastname).prop("defaultValue", lastname).prop("readOnly", false);
	var firstname = $("#firstnameHID_" + badgeidJQSel).val();
	$("#firstname").val(firstname).prop("defaultValue", firstname).prop("readOnly", false);
	var phone = $("#phoneHID_" + badgeidJQSel).val();
	$("#phone").val(phone).prop("defaultValue", phone).prop("readOnly", false);
	var email = $("#emailHID_" + badgeidJQSel).val();
	$("#email").val(email).prop("defaultValue", email).prop("readOnly", false);
	var postaddress1 = $("#postaddress1HID_" + badgeidJQSel).val();
	$("#postaddress1").val(postaddress1).prop("defaultValue", postaddress1).prop("readOnly", false);
	var postaddress2 = $("#postaddress2HID_" + badgeidJQSel).val();
	$("#postaddress2").val(postaddress2).prop("defaultValue", postaddress2).prop("readOnly", false);
	var postcity = $("#postcityHID_" + badgeidJQSel).val();
	$("#postcity").val(postcity).prop("defaultValue", postcity).prop("readOnly", false);
	var poststate = $("#poststateHID_" + badgeidJQSel).val();
	$("#poststate").val(poststate).prop("defaultValue", poststate).prop("readOnly", false);
	var postzip = $("#postzipHID_" + badgeidJQSel).val();
	$("#postzip").val(postzip).prop("defaultValue", postzip).prop("readOnly", false);
	var postcountry = $("#postcountryHID_" + badgeidJQSel).val();
	$("#postcountry").val(postcountry).prop("defaultValue", postcountry).prop("readOnly", false);
	var regtype = $("#regtypeHID_" + badgeidJQSel).val();
	$("#regtype").val(regtype).prop("readOnly", true);
	$("#lname_fname").val($("#lnameSPAN_" + badgeidJQSel).html());
	$("#bname").val($("#bnameSPAN_" + badgeidJQSel).html());
	var pname = $("#pnameSPAN_" + badgeidJQSel).html();
	$("#pname").val(pname).prop("defaultValue", pname).prop("readOnly", false);
	originalInterested = $("#interestedHID_" + badgeidJQSel).val();
	if (originalInterested=="")
		originalInterested = 0;
	$("#interested").val(originalInterested);
	$("#interested").prop("disabled", false);
	var bio = $("#bioHID_" + badgeidJQSel).val();
	$("#bio").val(bio).prop("defaultValue", bio).prop("readOnly", false);
	var staffnotes = $("#staffnotesHID_" + badgeidJQSel).val();
	$("#staffnotes").val(staffnotes).prop("defaultValue", staffnotes).prop("readOnly", false);
	$("#password").val("").prop("readOnly", false);
	$("#cpassword").val("").prop("readOnly", false);
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	lastnameDirty = false;
	firstnameDirty = false;
	badgenameDirty = false;
	phoneDirty = false;
	emailDirty = false;
	post1Dirty = false;
	post2Dirty = false;
	postcityDirty = false;
	poststateDirty = false;
	postzipDirty = false;
	postcountryDirty = false;
	$('#resultsDiv').show();
	$("#updateBUTN").prop("disabled", true);
	$("#resultBoxDIV").html("").hide();
	$("#passwordsDontMatch").hide();
}

function doSearchPartsBUTN() {
	if (!checkIfDirty())
		return;
	//called when user clicks "Search" within dialog
	var x = document.getElementById("searchPartsINPUT").value;
	if (!x)
		return;
	$('#searchPartsBUTN').button('loading');
	$.ajax({
		url: "SubmitAdminParticipants.php",
		dataType: "html",
		data: ({
			searchString : x,
			ajax_request_action : "perform_search"
			}),
		success: writeSearchResults,
		type: "POST"
		});
}

function fetchParticipant(badgeid) {
	$.ajax({
		url: "SubmitAdminParticipants.php",
		dataType: "xml",
		data: ({
			badgeid : badgeid,
			ajax_request_action : "fetch_participant"
			}),
		success: fetchParticipantCallback,
		type: "GET"
		});
}

function fetchParticipantCallback(data, textStatus, jqXHR) {
	//debugger;
	var node=data.firstChild.firstChild.firstChild;
	$("#badgeid").val(node.getAttribute("badgeid"));
	$("#lname_fname").val(node.getAttribute("lastname") + ", " + node.getAttribute("firstname"));
	$("#lastname").val(node.getAttribute("lastname")).prop("defaultValue", node.getAttribute("lastname")).prop("readOnly", false);
	$("#firstname").val(node.getAttribute("firstname")).prop("defaultValue", node.getAttribute("firstname")).prop("readOnly", false);
	$("#phone").val(node.getAttribute("phone")).prop("defaultValue", node.getAttribute("phone")).prop("readOnly", false);
	$("#email").val(node.getAttribute("email")).prop("defaultValue", node.getAttribute("email")).prop("readOnly", false);
	$("#postaddress1").val(node.getAttribute("postaddress1")).prop("defaultValue", node.getAttribute("postaddress1")).prop("readOnly", false);
	$("#postaddress2").val(node.getAttribute("postaddress2")).prop("defaultValue", node.getAttribute("postaddress2")).prop("readOnly", false);
	$("#postcity").val(node.getAttribute("postcity")).prop("defaultValue", node.getAttribute("postcity")).prop("readOnly", false);
	$("#poststate").val(node.getAttribute("poststate")).prop("defaultValue", node.getAttribute("poststate")).prop("readOnly", false);
	$("#postzip").val(node.getAttribute("postzip")).prop("defaultValue", node.getAttribute("postzip")).prop("readOnly", false);
	$("#postcountry").val(node.getAttribute("postcountry")).prop("defaultValue", node.getAttribute("postcountry")).prop("readOnly", false);
	$("#bname").val(node.getAttribute("badgename"));
	$("#pname").val(node.getAttribute("pubsname")).prop("defaultValue", node.getAttribute("pubsname")).prop("readOnly", false);
	originalInterested = node.getAttribute("interested");
	if (originalInterested=="")
		originalInterested = 0;
	$("#interested").val(originalInterested);
	$("#interested").prop("disabled", false);
	$("#bio").val(node.getAttribute("bio")).prop("defaultValue", node.getAttribute("bio")).prop("readOnly", false);
	$("#staffnotes").val(node.getAttribute("staff_notes")).prop("defaultValue", node.getAttribute("staff_notes")).prop("readOnly", false);
	$("#password").prop("readOnly", false);
	$("#password").val("");
	$("#cpassword").prop("readOnly", false);
	$("#cpassword").val("");
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	lastnameDirty = false;
	firstnameDirty = false;
	badgenameDirty = false;
	phoneDirty = false;
	emailDirty = false;
	post1Dirty = false;
	post2Dirty = false;
	postcityDirty = false;
	poststateDirty = false;
	postzipDirty = false;
	postcountryDirty = false;
	$('#resultsDiv').show();
	$('#resultBoxDIV').show();
	$("#updateBUTN").prop("disabled", true);	
	$("#passwordsDontMatch").hide();
	hideSearchResults();
}

function hideSearchResults() {
	resultsHidden = true;
	$("#searchResultsDIV").hide("fast");
	$("#toggleSearchResultsBUTN").prop("disabled", false);
	$("#toggleSearchResultsBUTN").prop("hidden", false);
	$("#toggleText").html("Show");
}

function initializeAdminParticipants() {
	//called when JQuery says AdminParticipants page has loaded
	//debugger;
	$("#passwordsDontMatch").hide();
	$('#resultsDiv').hide();
	$('#resultBoxDIV').hide();
	$("#unsavedWarningDIV").modal({backdrop: 'static', keyboard: true, show: false});
	$("#toggleSearchResultsBUTN").click(toggleSearchResultsBUTN);
	$("#toggleSearchResultsBUTN").prop("disabled", true);
	$("#toggleSearchResultsBUTN").prop("hidden", true);
	resultsHidden = true;
	$("#searchPartsBUTN").click(doSearchPartsBUTN);
	$("#cancelOpenSearchBUTN").button();
	$("#overrideOpenSearchBUTN").button();
	$("#searchResultsDIV").html("").hide('fast');
	if (fbadgeid)  // signal from page initializer that page was requested to
					       // to be preloaded with a participant
		fetchParticipant(fbadgeid);
}

function loadNewParticipant() {
	var id = $('#warnNewBadgeID').html();
	chooseParticipant(id, 'override');
	return true;
}

function showSearchResults() {
	resultsHidden = false;
	$("#searchResultsDIV").show("fast");
	$("#searchResultsDIV").css("overflow-y", "auto");
	$("#toggleSearchResultsBUTN").prop("disabled", false);
	$("#toggleSearchResultsBUTN").prop("hidden", false);
	$("#toggleText").html("Hide");
}

function showUpdateResults(data, textStatus, jqXHR) {
	//ajax success callback function
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	lastnameDirty = false;
	firstnameDirty = false;
	badgenameDirty = false;
	phoneDirty = false;
	emailDirty = false;
	post1Dirty = false;
	post2Dirty = false;
	postcityDirty = false;
	poststateDirty = false;
	postzipDirty = false;
	postcountryDirty = false;
	$("#password").val("");
	$("#cpassword").val("");
	$('#updateBUTN').button('reset');
	originalInterested = $("#interested").val();
	setTimeout(function() {$("#updateBUTN").button().attr("disabled","disabled");}, 0);
	$("#resultBoxDIV").html(data);
	$('#resultBoxDIV').show();
}

function textChange(which) {
	switch(which) {
		case 'bio':
			bioDirty = ($("#bio").val() != $("#bio").prop("defaultValue"));
			break;
		case 'snotes':
			snotesDirty = ($("#staffnotes").val() != $("#staffnotes").prop("defaultValue"));
			break;
		case 'pname':
			pnameDirty = ($("#pname").val() != $("#pname").prop("defaultValue"));
			break;
		case 'lastname':
			lastnameDirty = ($("#lastname").val() != $("#lastname").prop("defaultValue"));
			break;
		case 'firstname':
			firstnameDirty = ($("#firstname").val() != $("#lastname").prop("defaultValue"));
			break;
		case 'bname':
			badgenameDirty = ($("#bname").val() != $("#bname").prop("defaultValue"));
			break;
		case 'phone':
			phoneDirty = ($("#phone").val() != $("#phone").prop("defaultValue"));
			break;
		case 'email':
			emailDirty = ($("#email").val() != $("#email").prop("defaultValue"));
			break;
		case 'postaddress1':
			post1Dirty = ($("#postaddress1").val() != $("#postaddress1").prop("defaultValue"));
			break;
		case 'postaddress2':
			post2Dirty = ($("#postaddress2").val() != $("#postaddress2").prop("defaultValue"));
			break;
		case 'postcity':
			postcityDirty = ($("#postcity").val() != $("#postcity").prop("defaultValue"));
			break;
		case 'poststate':
			poststateDirty = ($("#poststate").val() != $("#poststate").prop("defaultValue"));
			break;
		case 'postzip':
			postzipDirty = ($("#postzip").val() != $("#postzip").prop("defaultValue"));
			break;
		case 'poststate':
			postcountryDirty = ($("#postcountry").val() != $("#postcountry").prop("defaultValue"));
			break;
		}
	anyChange();
}

function toggleSearchResultsBUTN() {
	$("#searchResultsDIV").slideToggle("fast");
	resultsHidden = !resultsHidden;
	$("#toggleText").html((resultsHidden ? "Show" : "Hide"));
}

function updateBUTN() {
	//debugger;
	$('#updateBUTN').button('loading');
	var postdata = {
		ajax_request_action : "update_participant",
		badgeid : $("#badgeid").val()
		};
	if (x = $("#password").val())
		postdata.password = x;
	if (bioDirty)
		postdata.bio = $("#bio").val();
	if (pnameDirty)
		postdata.pname = $("#pname").val();
	if (snotesDirty)
		postdata.staffnotes = $("#staffnotes").val();
	if ($("#interested").val() != originalInterested)
		postdata.interested = $("#interested").val();
	if (lastnameDirty)
		postdata.lastname = $("#lastname").val();
	if (firstnameDirty)
		postdata.firstname = $("#firstname").val();
	if (badgenameDirty)
		postdata.bname = $("#bname").val();
	if (phoneDirty)
		postdata.phone = $("#phone").val();
	if (emailDirty)
		postdata.email = $("#email").val();
	if (post1Dirty)
		postdata.postaddress1 = $("#postaddress1").val();
	if (post2Dirty)
		postdata.postaddress2 = $("#postaddress2").val();
	if (postcityDirty)
		postdata.postcity = $("#postcity").val();
	if (poststateDirty)
		postdata.poststate = $("#poststate").val();
	if (postzipDirty)
		postdata.postzip = $("#postzip").val();
	if (postcountryDirty)
		postdata.postcountry = $("#postcountry").val();
	$.ajax({
		url: "SubmitAdminParticipants.php",
		dataType: "html",
		data: postdata,
		success: showUpdateResults,
		type: "POST"
		});
}

function writeSearchResults(data, textStatus, jqXHR) {
	//ajax success callback function
	$("#searchResultsDIV").html(data).show('fast');
	$('#searchPartsBUTN').button('reset');
	showSearchResults();
}

