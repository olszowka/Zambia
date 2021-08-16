// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
var PartSurvey = function () {

	this.initialize = function () {
		$('[data-toggle="tooltip"]').each(function () {
			this.title = '<span class="text-left" style="white-space: nowrap;">' + this.title + '</span>';
		})
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-mce="yes"]').each(function () {
			fieldname = this.getAttribute("id");
			height = this.getAttribute("rows") * 50;
			maxlength = this.getAttribute("maxlength");
			tinyMCE.init({
				selector: "textarea#" + fieldname,
				plugins: 'fullscreen lists advlist link preview searchreplace autolink charmap hr nonbreaking visualchars code ',
				browser_spellcheck: true,
				contextmenu: false,
				height: height,
				width: 900,
				min_height: 200,
				maxlength: maxlength,
				menubar: false,
				toolbar: [
					'undo redo searchreplace | styleselect | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | preview fullscreen ',
					'alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist | forecolor backcolor | link'
				],
				toolbar_mode: 'floating',
				content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
				placeholder: 'Type content here...'
			});
		});
		$('[data-othertextselect="1"]').each(function () {
			SelectChangeOthertext(this);
		});
		$('[data-othertextmultidisplay="1"]').each(function () {
			lrChangeOthertext(this);
		});
		$('[data-othertextradio="1"]').each(function () {
			var checked = this.getAttribute("checked");
			if (checked !== null)
				RadioChangeOthertext(this);
		});
		$('[data-othertextcheckbox="1"]').each(function () {
			var checked = this.getAttribute("checked");
			if (checked !== null)
				CheckboxChangeOthertext(this);
		});
	};
}

var partSurvey = new PartSurvey();

function UpdateSurvey() {
	tinyMCE.triggerSave();
	var i;
	$('[data-multidisplay="yes"]').each(function () {
		//console.log("saving " + this.getAttribute("id"));
		for (i = 0; i < this.length; i++) {
			//console.log("setting " + i + " selected");
			this.options[i].selected = true;
		}
	});
	alert_msg = "";
	$('[data-required="1"]').each(function () {
		value = "FnF!";
		id = this.getAttribute("id").replace('-prompt', '');
		prompt = this.innerHTML.replace(/<span.*/, '');
		console.log("checking required field: " + id + ':' + prompt);

		// openend, textarea, number types
		fieldname = id + '-input';
		el = document.getElementById(fieldname);

		if (el) {
			value = el.value;
			//console.log('-input: ' + value);
		}

		// month-year type  id_month & id_year
		if (value == "FnF!") {
			value1 = '';
			value2 = '';
			monthfound = false;
			yearfound = false;
			el = document.getElementById(id + '_month');
			if (el) {
				value1 = el.value;
				monthfound = true;
				//console.log('_month: ' + value1);
			}
			el = document.getElementById(id + '_year');
			if (el) {
				value2 = el.value;
				yearfound = true;
				//console.log('_year: ' + value2);
			}
			if (monthfound || yearfound) {
				if (value1 == '' || value2 == '')
					value = '';
				else
					value = value1 + ' ' + value2;
				//console.log('monthyear: ' + value);
			}
		}

		// select types
		if (value == "FnF!") {
			el = document.getElementById(id);
			if (el) {
				opt = el.options[el.selectedIndex];
				if (opt) {
					value = opt.value;
					//console.log('selectedindex[' + el.selectedIndex + ']: ' + value);
				}
			}
		}

		// multi-display type id-dest getElemementsByName id
		if (value == "FnF!") {
			el = document.getElementById(id + '-dest');
			if (el) {
				opt = el.options;
				value = opt.length > 0 ? 'x' : '';
				//console.log('display, ' + opt.length + ': ' + value);
			}
		}

		// multi-checkbox type getElemementsByName id
		if (value == "FnF!") {
			value1 = '';
			els = document.getElementsByName(id + '[]');
			//console.log('multi, els length: ' + els.length);
			if (els.length > 0) {
				els.forEach(function (el) {
					value1 = value1 + (el.checked ? 'x' : '');
				});
				value = value1;
				//console.log('multi-checkbox: ' + value);
			}
		}

		// radio types  getElemementsByName id
		if (value == "FnF!") {
			value1 = '';
			els = document.getElementsByName(id);
			//console.log('radio, els length: ' + els.length);
			if (els.length > 0) {
				els.forEach(function (el) {
					value1 = value1 + (el.checked ? 'x' : '');
				});
				value = value1;
				//console.log('radio: ' + value);
			}
		}

		//console.log("'" + value + "'");
		if (value == 'FnF!' || value == '')
			alert_msg = alert_msg + "<br/>" + prompt;
		//console.log('alert msg now ' + alert_msg);
	});

	if (alert_msg != "") {
		el = document.getElementById("message");
		el.innerHTML = "The following required questions are not answered: <br/>" + alert_msg;
		el.setAttribute("class", "alert alert-danger mt-4");
		el.setAttribute("style", "display: block;");
		window.scrollTo(0, 0);
		return false;
	}

	return true;
}