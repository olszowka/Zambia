//	Created by Peter Olszowka on 2016-05-18;
//	Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.

var SessionHistory = function() {
	this.initialize = function initialize() {
		//called when page has loaded
		var $sessionButton = document.getElementById('sessionBtn');
		var $sessionSelect = document.getElementById('sessionDropdown');
		if ($sessionSelect) {
			var sessionSelectChoices = new Choices($sessionSelect, {
				searchResultLimit: 9999,
				searchPlaceholderValue: "Type here to search list."
			});
			if ($sessionButton) {
				if ($sessionSelect.value === '0') {
					$sessionButton.disabled = true;
				}
				$sessionSelect.addEventListener('change', function() {
					$sessionButton.disabled = ($sessionSelect.value === '0');
				});
			}
		}
	}
};

var sessionHistory = new SessionHistory();

/* This file should be included only on relevant page.  See main.js and javascript_functions.php */
document.addEventListener('DOMContentLoaded', sessionHistory.initialize.bind(sessionHistory));
