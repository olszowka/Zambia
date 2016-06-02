//	$Header: https://svn.code.sf.net/p/zambia/code/branches/Track_changes/webpages/xsl/StaffAssignParticipants.xsl 1151 2015-11-23 13:31:52Z polszowka $
//	Created by Peter Olszowka on 2016-05-18;
//	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.

var participantAssignmentHistory = new ParticipantAssignmentHistory();

function ParticipantAssignmentHistory() {
	this.initializePage = initializePage;

	function initializePage() {
		$('#sessionBtn').prop('disabled', ($('#sessionDropdown').val() == '0'));	
		$('#sessionDropdown').change(
			function() {
				$('#sessionBtn').prop("disabled", ($(this).val() == 0));
			}
		);              
	}
}
