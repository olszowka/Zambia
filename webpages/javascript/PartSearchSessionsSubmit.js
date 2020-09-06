var partSearchSessionsSubmit = new PartSearchSessionsSubmit;

function PartSearchSessionsSubmit() {

	this.initialize = function initialize() {
		var interestForm = document.getElementById('sessionInterestFRM');
		interestForm.addEventListener('submit', 
			function(event) {
				var interestsCheckboxes = document.querySelectorAll('.interestsCHK');
				interestsCheckboxes.forEach(function(intCheck) {
					if (intCheck.checked != intCheck.defaultChecked) {
						if (intCheck.checked) {
							interestForm.insertAdjacentHTML('beforeend', '<input type="hidden" name="addInterest[]" value="' + intCheck.value + '" />');
						} else {
							interestForm.insertAdjacentHTML('beforeend', '<input type="hidden" name="deleteInterest[]" value="' + intCheck.value + '" />');
						}
					}
				});
				
			}
		);
	};

}