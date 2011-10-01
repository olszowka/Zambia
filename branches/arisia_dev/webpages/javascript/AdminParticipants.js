var bioDirty = false;
var pnameDirty = false;
var snotesDirty = false;
var originalInterested = 0;

function anyChange() {
	var x = document.getElementById("password").value;
	var y = document.getElementById("cpassword").value;
	if (!x && !y && (document.getElementById("interested").value != originalInterested || 
			bioDirty || pnameDirty || snotesDirty) ||
		(x && x == y) ) {
			document.getElementById("updateBUTN").disabled = false;
			}
		else {
			document.getElementById("updateBUTN").disabled = true;			
			}
	var z = document.getElementById("passwordsDontMatch");
	if (x && y && x!=y)
			z.style.display = "inline-block";
		else
			z.style.display = "none";
}

function cancelSearchPartsBUTN() {
	$("#searchPartsDIV").dialog("close");
}

function chooseParticipant(badgeid) {
	//debugger;
	$("#searchPartsDIV").dialog("close");
	document.getElementById("badgeid").value = document.getElementById("bidSPAN_" + badgeid).innerHTML;
	document.getElementById("lname_fname").value = document.getElementById("lnameSPAN_" + badgeid).innerHTML;
	document.getElementById("bname").value = document.getElementById("bnameSPAN_" + badgeid).innerHTML;
	document.getElementById("pname").value = document.getElementById("pnameSPAN_" + badgeid).innerHTML;
	document.getElementById("pname").readOnly = false;
	originalInterested = document.getElementById("interestedHID_" + badgeid).value;
	if (originalInterested=="")
		originalInterested = 0;
	document.getElementById("interested").value = originalInterested;
	document.getElementById("interested").disabled = false;
	document.getElementById("bio").value = document.getElementById("bioHID_" + badgeid).value;
	document.getElementById("bio").readOnly = false;
	document.getElementById("staffnotes").value = document.getElementById("staffnotesHID_" + badgeid).value;
	document.getElementById("staffnotes").readOnly = false;
	document.getElementById("password").readOnly = false;
	document.getElementById("password").value = "";
	document.getElementById("cpassword").readOnly = false;
	document.getElementById("cpassword").value = "";
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	document.getElementById("updateBUTN").disabled = true;
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

function highlight(dohighlight, id) {
	if (dohighlight) {
			document.getElementById("actionDIV_" + id).className="action_hover";
			document.getElementById("lnameSPAN_" + id).className="action_hover";
			document.getElementById("pnameSPAN_" + id).className="actionB_hover";
			document.getElementById("bnameSPAN_" + id).className="action_hover";
			document.getElementById("bidSPAN_" + id).className="action_hover";
			}
		else {
			document.getElementById("actionDIV_" + id).className="action";
			document.getElementById("lnameSPAN_" + id).className="action";
			document.getElementById("pnameSPAN_" + id).className="actionB";
			document.getElementById("bnameSPAN_" + id).className="action";
			document.getElementById("bidSPAN_" + id).className="action";
			}
}

function initializeAdminParticipants() {
	//called when JQuery says AdminParticipants page has loaded
	//just a filler for now
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
	$("#doSearchPartsBUTN").button();
	$("#doSearchPartsBUTN").click(doSearchPartsBUTN);
	$("#cancelSearchPartsBUTN").button();
	$("#cancelSearchPartsBUTN").click(cancelSearchPartsBUTN);
	$("#cancelOpenSearchBUTN").button();
	$("#overrideOpenSearchBUTN").button();
	//window.status="Reached initializeAdminParticipants."
}

function openSearchPartsBUTN(mode) {
	//called when user clicks "Search for participants" on the page
	//just a filler for now
	//debugger;
	if (!mode && (bioDirty || pnameDirty || snotesDirty || 
		document.getElementById("interested").value != originalInterested ||
		(document.getElementById("password").value) && 
			document.getElementById("cpassword").value)) {
			$("#unsavedWarningDIV").dialog("open");
			$("#cancelOpenSearchBUTN").blur();
			return;	
			}
	if (mode)
		$("#unsavedWarningDIV").dialog("close");
	if (mode=="cancel")
		return;
	document.getElementById("searchPartsINPUT").value="";
	document.getElementById("searchResultsDIV").innerHTML="";
	$("#searchPartsDIV").dialog("open");
}

function showUpdateResults(data, textStatus, jqXHR) {
	//ajax success callback function
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	document.getElementById("updateBUTN").disabled = true;
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
	document.getElementById("searchResultsDIV").innerHTML = data;
}

