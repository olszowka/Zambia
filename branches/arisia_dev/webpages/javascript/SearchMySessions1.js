var searchMySessions1 = new SearchMySessions1;

function SearchMySessions1() {

	this.clickCheckbox = function clickCheckbox() {
		$(this).nextAll("input").prop("disabled", ($(this).prop("checked") == $(this).prop("defaultChecked")));
	}

	this.initialize = function initialize() {
		//called when JQuery says My Profile page has loaded
		$("#searchMySessions1TAB").find(":checkbox").click(searchMySessions1.clickCheckbox);
	}

}