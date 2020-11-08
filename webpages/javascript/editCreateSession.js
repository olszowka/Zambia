function fadditems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    for ( i = 0 ; i < source.length ; i++ ) {
        if (source.options[i].selected==true) {
            itemtext=source.options[i].text;
            itemvalue=source.options[i].value;
            dest.options[dest.options.length] = new Option(text=itemtext, value=itemvalue);
            source.options[i] = null;
            i--
            }
        }
    }

function fdropitems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    for ( i = 0 ; i < dest.length ; i++ ) {
        if (dest.options[i].selected==true) {
            itemtext=dest.options[i].text;
            itemvalue=dest.options[i].value;
            source.options[source.options.length] = new Option(text=itemtext, value=itemvalue);
            dest.options[i] = null;
            i--
            }
        }
    }

function mysubmit() {
	$("#sessionid").prop("disabled",false);
    var i;
    for ( i = 0 ; i < document.sessform.featdest.length ; i++ ) {
        document.sessform.featdest.options[i].selected=true;
        }
    for ( i = 0 ; i < document.sessform.servdest.length ; i++ ) {
        document.sessform.servdest.options[i].selected=true;
    }
    tinymce.triggerSave();
}

function initializeSessionEdit() {
	//called when JQuery says render session page has loaded
	//debugger;
	tinymce.init({
        selector: 'textarea#progguidhtml',
		plugins: 'table wordcount fullscreen advlist link preview searchreplace autolink charmap hr nonbreaking visualchars ',
		browser_spellcheck: true,
		contextmenu: false,
		height: 350,
		min_height: 200,
		menubar: false,
		toolbar: [
			'undo redo | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | forecolor backcolor | link| preview fullscreen ',
			'searchreplace | alignleft aligncenter alignright alignjustify | outdent indent'
		],
		toolbar_mode: 'wrap',
		content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
		placeholder: 'Type custom content here...',
		setup: function (ed) {
			ed.on('change', function (e) {
				bioDirty = true;
				myProfile.anyChange("htmlbioTXTA");
			});
		},
		init_instance_callback: function (editor) {
			$(editor.getContainer()).find('button.tox-statusbar__wordcount').click();  // if you use jQuery
		}
	});
}
