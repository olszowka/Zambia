var bioDirty = false;
var pnameDirty = false;
var snotesDirty = false;
var originalInterested = 0;
var fbadgeid;
var resultsHidden = true;

function anyChange() {
	var x = $("#password").val();
	var y = $("#cpassword").val();
	if (!x && !y && ($("#interested").val() != originalInterested || 
			bioDirty || pnameDirty || snotesDirty) ||
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
	if (!mode && (bioDirty || pnameDirty || snotesDirty || 
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
	hideSearchResults();
	$("#badgeid").val($("#bidSPAN_" + badgeid).html());
	$("#lname_fname").val($("#lnameSPAN_" + badgeid).html());
	$("#bname").val($("#bnameSPAN_" + badgeid).html());
	var pname = $("#pnameSPAN_" + badgeid).html();
	$("#pname").val(pname).prop("defaultValue", pname).prop("readOnly", false);
	originalInterested = $("#interestedHID_" + badgeid).val();
	if (originalInterested=="")
		originalInterested = 0;
	$("#interested").val(originalInterested);
	$("#interested").prop("disabled", false);
	var bio = $("#bioHID_" + badgeid).val();
	$("#bio").val(bio).prop("defaultValue", bio).prop("readOnly", false);
	var staffnotes = $("#staffnotesHID_" + badgeid).val();
	$("#staffnotes").val(staffnotes).prop("defaultValue", staffnotes).prop("readOnly", false);
	$("#password").val("").prop("readOnly", false);
	$("#cpassword").val("").prop("readOnly", false);
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
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
	$("#lname_fname").val(node.getAttribute("lastname")+", "+node.getAttribute("firstname"));
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
	$("#toggleSearchResultsBUTN").prop("disabled", false);
	$("#toggleText").html("Hide");
}

function showUpdateResults(data, textStatus, jqXHR) {
	//ajax success callback function
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
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

