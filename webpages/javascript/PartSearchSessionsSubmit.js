// Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var partSearchSessionsSubmit = new PartSearchSessionsSubmit;

function PartSearchSessionsSubmit() {

	this.initialize = function initialize() {
		var interestForm = document.getElementById('sessionInterestFRM');
		if (interestForm) {
			interestForm.addEventListener('submit', 
				function(event) {
					var interestsCheckboxes = document.querySelectorAll('.interestsCHK');
					interestsCheckboxes.forEach(function(intCheck) {
						if (intCheck.checked !== intCheck.defaultChecked) {
							if (intCheck.checked) {
								interestForm.insertAdjacentHTML('beforeend', '<input type="hidden" name="addInterest[]" value="' + intCheck.value + '" />');
							} else {
								interestForm.insertAdjacentHTML('beforeend', '<input type="hidden" name="deleteInterest[]" value="' + intCheck.value + '" />');
							}
						}
					});
					
				}
			);
		}
	};
}
