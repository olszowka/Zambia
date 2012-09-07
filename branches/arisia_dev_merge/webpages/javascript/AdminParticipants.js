var bioDirty = false;
var pnameDirty = false;
var snotesDirty = false;
var originalInterested = 0;
var fbadgeid;

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
			z.css("display","inline-block");
		else
			z.hide();
}

function cancelSearchPartsBUTN() {
	$("#searchPartsDIV").dialog("close");
}

function chooseParticipant(badgeid) {
	//debugger;
	$("#searchPartsDIV").dialog("close");
	$("#badgeid").val($("#bidSPAN_" + badgeid).html());
	$("#lname_fname").val($("#lnameSPAN_" + badgeid).html());
	$("#bname").val($("#bnameSPAN_" + badgeid).html());
	$("#pname").val($("#pnameSPAN_" + badgeid).html());
	$("#pname").prop("readOnly", false);
	originalInterested = $("#interestedHID_" + badgeid).val();
	if (originalInterested=="")
		originalInterested = 0;
	$("#interested").val(originalInterested);
	$("#interested").prop("disabled", false);
	$("#bio").val($("#bioHID_" + badgeid).val());
	$("#bio").prop("readOnly", false);
	$("#staffnotes").val($("#staffnotesHID_" + badgeid).val());
	$("#staffnotes").prop("readOnly", false);
	$("#password").prop("readOnly", false);
	$("#password").val("");
	$("#cpassword").prop("readOnly", false);
	$("#cpassword").val("");
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	$("#updateBUTN").prop("disabled", true);
	$("#resultBoxDIV").html("");
}

function doSearchPartsBUTN() {
	//called when user clicks "Search" within dialog
	var x = document.getElementById("searchPartsINPUT").value;
	if (!x)
		return;
	$.ajax({
		url: "SubmitAdminParticipants.php",
		dataType: "html",
		data: ({ searchString : x,
				ajax_request_action : "perform_search" }),
		success: writeSearchResults,
		type: "POST"
		});
}

function fetchParticipant(badgeid) {
	$.ajax({
		url: "SubmitAdminParticipants.php",
		dataType: "xml",
		data: ({ badgeid : badgeid,
				ajax_request_action : "fetch_participant" }),
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
	$("#pname").val(node.getAttribute("pubsname"));
	$("#pname").prop("readOnly", false);
	originalInterested = node.getAttribute("interested");
	if (originalInterested=="")
		originalInterested = 0;
	$("#interested").val(originalInterested);
	$("#interested").prop("disabled", false);
	$("#bio").val(node.getAttribute("bio"));
	$("#bio").prop("readOnly", false);
	$("#staffnotes").val(node.getAttribute("staff_notes"));
	$("#staffnotes").prop("readOnly", false);
	$("#password").prop("readOnly", false);
	$("#password").val("");
	$("#cpassword").prop("readOnly", false);
	$("#cpassword").val("");
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	$("#updateBUTN").prop("disabled", true);	
}

function highlight(dohighlight, id) {
	if (dohighlight) {
			$("#actionDIV_" + id).removeClass().addClass("action_hover");
			$("#lnameSPAN_" + id).removeClass().addClass("action_hover");
			$("#pnameSPAN_" + id).removeClass().addClass("actionB_hover");
			$("#bnameSPAN_" + id).removeClass().addClass("action_hover");
			$("#bidSPAN_" + id).removeClass().addClass("action_hover");
			}
		else {
			$("#actionDIV_" + id).removeClass().addClass("action");
			$("#lnameSPAN_" + id).removeClass().addClass("action");
			$("#pnameSPAN_" + id).removeClass().addClass("actionB");
			$("#bnameSPAN_" + id).removeClass().addClass("action");
			$("#bidSPAN_" + id).removeClass().addClass("action");
			}
}

function initializeAdminParticipants() {
	//called when JQuery says AdminParticipants page has loaded
	//debugger;
	$("#searchPartsDIV").dialog({
		title: "Search for participants",
		height: "450",
		width: "550",
		modal: true,
		autoOpen: false,
		resizable: false,
		draggable: true
		});
	$("#unsavedWarningDIV").dialog({
		title: "Data not saved",
		height: "175",
		width: "350",
		modal: true,
		autoOpen: false,
		resizable: false,
		draggable: true
		});
	$("#openSearchPartsBUTN").button();
	$("#openSearchPartsBUTN").click(openSearchPartsBUTN);
	$("#doSearchPartsBUTN").button();
	$("#doSearchPartsBUTN").click(doSearchPartsBUTN);
	$("#cancelSearchPartsBUTN").button();
	$("#cancelSearchPartsBUTN").click(cancelSearchPartsBUTN);
	$("#cancelOpenSearchBUTN").button();
	$("#overrideOpenSearchBUTN").button();
	//window.status="Reached initializeAdminParticipants."
	if (fbadgeid)	// signal from page initializer that page was requested to
					// to be preloaded with a participant
		fetchParticipant(fbadgeid);
}

function openSearchPartsBUTN(mode) {
	//called when user clicks "Search for participants" on the page
	//debugger;
	if (!mode && (bioDirty || pnameDirty || snotesDirty || 
		$("#interested").val() != originalInterested ||
		($("#password").val()) && 
			$("#cpassword").val())) {
			$("#unsavedWarningDIV").dialog("open");
			$("#cancelOpenSearchBUTN").blur();
			return;	
			}
	if (mode)
		$("#unsavedWarningDIV").dialog("close");
	if (mode=="cancel")
		return;
	$("#searchPartsINPUT").val("");
	$("#searchResultsDIV").html("");
	$("#searchPartsDIV").dialog("open");
}

function showUpdateResults(data, textStatus, jqXHR) {
	//ajax success callback function
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	$("#updateBUTN").prop("disabled", true);
	originalInterested = $("#interested").val();
	$("#resultBoxDIV").html(data);
}

function textChange(which) {
	switch(which) {
		case 'bio':
			if ($("#bio").val())
				bioDirty = true;
			break;
		case 'snotes':
			if ($("#staffnotes").val())
				snotesDirty = true;
			break;
		case 'pname':
			if ($("#pname").val())
				pnameDirty = true;
			break;
		}
	anyChange();
}

function updateBUTN() {
	//debugger;
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
	$("#searchResultsDIV").html(data);
}

