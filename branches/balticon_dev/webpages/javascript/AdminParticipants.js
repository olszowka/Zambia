var bioDirty = false;
var pnameDirty = false;
var snotesDirty = false;
var originalInterested = 0;
var fbadgeid;

function anyChange() {
	var x = $("#password").val();
	var y = $("#cpassword").val();
	if (!x && !y && ($("#interested").val() != originalInterested || 
			bioDirty || pnameDirty || snotesDirty || regtypeDirty || 
			regdepartmentDirty || adminStatusDirty || staffStatusDirty || 
			partStatusDirty) ||
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

function cancelCreatePartsBUTN() {
	$("#createPartsDIV").dialog("close");
	}

function chooseParticipant(badgeid) {
	//debugger;
	$("#searchPartsDIV").dialog("close");
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	regtypeDirty= false;
	regdepartmentDirty=false;
	adminStatusDirty=false;
	staffStatusDirty=false;
	partStatusDirty=false;
	$("#updateBUTN").prop("disabled", true);
	$("#resultBoxDIV").html("");
	fetchParticipant(badgeid)
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

function doCreatePartsBUTN() {
	//called when user clicks "Create" within dialog
	
	var email = $("#create_email").val();
	var pname = $("#create_pname").val();
	
	var postdata = {
		ajax_request_action : "create_participant"
		};
	
	if(!email || !pname) {
		return;
		}
	
	postdata.email = email;
	postdata.pname = pname;
	postdata.regtype = $("#create_regtype").val();
	postdata.regdepartment = $("#create_regdepartment").val();
	postdata.staffStatus = $("#create_staffStatus")[0].checked;
	postdata.adminStatus = $("#create_adminStatus")[0].checked;
	postdata.partStatus = $("#create_partStatus")[0].checked;
	
	$.ajax({
		url: "SubmitCreateParticipant.php",
		dataType: "xml",
		data: postdata,
		success: writeCreateResults,
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
		type: "POST"
		});
}

function fetchParticipantCallback(data, textStatus, jqXHR) {
	//debugger;
	var doc = data.lastChild;
	var userdata = doc.firstChild; //first query
	var perms=userdata.nextSibling; // second query
	var node=userdata.firstChild;
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
	$("#regtype").val(node.getAttribute("regtype"));
	$("#regtype").prop("readOnly", false);
	$("#regdepartment").val(node.getAttribute("regdepartment"));
	$("#regdepartment").prop("readOnly", false);
	var permRoleResult=perms.firstChild;
	while(permRoleResult) {
	if(permRoleResult.getAttribute("permroleid")==1) {
		$("#adminStatus").prop("checked", "checked");
		}
	else if (permRoleResult.getAttribute("permroleid")==2){
		$("#staffStatus").prop("checked", "checked");
		}
	else if (permRoleResult.getAttribute("permroleid")==3){
		$("#partStatus").prop("checked", "checked");
		}
	permRoleResult = permRoleResult.nextSibling;
	}
	
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	
	regtypeDirty= false;
	regdepartmentDirty=false;
	adminStatusDirty=false;
	staffStatusDirty=false;
	partStatusDirty=false;
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
	$("#createPartsDIV").dialog({
		title: "Cretae Participants",
		height: "450",
		width: "550",
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
	
	$("#createPartsBUTN").button();
	$("#createPartsBUTN").click(createPartsBUTN);
	$("#doCreatePartsBUTN").button();
	$("#doCreatePartsBUTN").click(doCreatePartsBUTN);
	$("#cancelCreatePartsBUTN").button();
	$("#cancelCreatePartsBUTN").click(cancelCreatePartsBUTN);
	
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

function createPartsBUTN(mode) {
	//called when user clicks "Create participant" on the page
	//debugger;
	$("#create_email").val("");
	$("#create_fname").val("");
	$("#create_lname").val("");
	$("#create_pname").val("");
	$("#createPartsDIV").dialog("open");
}

function showUpdateResults(data, textStatus, jqXHR) {
	//ajax success callback function
	bioDirty = false;
	pnameDirty = false;
	snotesDirty = false;
	
	regtypeDirty= false;
	regdepartmentDirty=false;
	adminStatusDirty=false;
	staffStatusDirty=false;
	partStatusDirty=false;

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
		case 'regtype':
			regtypeDirty = true;
			break;
		case 'regdepartment':
			if($("#regdepartment").val())
				regdepartmentDirty=true;
			break;
		case 'adminStatus':
			adminStatusDirty = true;
			break;
		case 'staffStatus':
			staffStatusDirty = true;
			break;
		case 'partStatus':
			partStatusDirty = true;
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
	if (regtypeDirty) 
		postdata.regtype = $("#regtype").val();
	if (regdepartmentDirty) 
		postdata.regdepartment = $("#regdepartment").val();
	if (adminStatusDirty) 
		postdata.adminStatus = $("#adminStatus")[0].checked;
	if (staffStatusDirty) 
		postdata.staffStatus = $("#staffStatus")[0].checked;
	if (partStatusDirty) 
		postdata.partStatus = $("#partStatus")[0].checked;
		
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
function writeCreateResults(data, textStatus, jqXHR) {
	//ajax success callback function
	
	//$("#createResultsDIV").html(data);
	$("#createPartsDIV").dialog("close");
	fetchParticipantCallback(data, textStatus, jqXHR);
}


