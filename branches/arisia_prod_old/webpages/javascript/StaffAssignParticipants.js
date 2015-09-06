var bio;
var fname;

function initAssignParticipants() {
  $('#BioBtn').click(function() {return showPopover();}).prop("disabled", true).button();
  $('#partDropdown').change(
    function() {
      $('#BioBtn').prop("disabled", ($(this).val() == 0));
    }
   );                            
  $('#sessionBtn').prop('disabled', true);
  $('#sessionDropdown').change(
    function() {
      $('#sessionBtn').prop("disabled", ($(this).val() == 0));
    }
   );              
  $('html').click(function(e) {$('#partDropdown').popover('hide');});              
  $('#partDropdown').popover({html: true,
                              placement: 'top',
                              title: function() {return 'Bio for ' + fname+"&nbsp;<i id='popoverClose' class='icon-remove-sign pull-right'></i>";},
                              content: function() {return bio;}});
  $('#popoverClose').click(function(e) {$('#BioBtn').prop("disabled", false); $('#partDropdown').popover('hide');});
  $('[rel="popover"]').popover();
}

function showPopover() {
  // Get the bio for the selected participant
  $('#BioBtn').button('loading');
  var badgeid = $('#partDropdown').val();
	$.ajax({
		url: "SubmitAdminParticipants.php",
		dataType: "xml",
		data: ({ badgeid : badgeid, ajax_request_action : "fetch_participant" }),
		success: showPopoverCallback,
		type: "GET"
		});
  return false;
}

function showPopoverCallback(data, textStatus, jqXHR) {
	//debugger;
  var node=data.firstChild.firstChild.firstChild;
  bio = node.getAttribute("bio");
  fname = node.getAttribute("firstname")+" "+node.getAttribute("lastname");

  $('#BioBtn').button('reset');
  setTimeout(function() {$("#BioBtn").button().prop("disabled", true);}, 0);

  $('#partDropdown').popover('show');
}
