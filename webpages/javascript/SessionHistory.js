//	Created by Peter Olszowka on 2016-05-18;
//	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.

var sessionHistory = new SessionHistory();

function SessionHistory() {
	this.initializePage = initializePage;

	function initializePage() {
		$('#sessionBtn').prop('disabled', ($('#sessionDropdown').val() == '0'));	
		$('#sessionDropdown').change(
			function() {
				$('#sessionBtn').prop("disabled", ($(this).val() == '0'));
			}
		);              
	}
}
