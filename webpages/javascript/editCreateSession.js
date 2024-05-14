// Copyright (c) 2011-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
function mysubmit() {
    $("#sessionid").prop("disabled",false);
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
        init_instance_callback: function (editor) {
            $(editor.getContainer()).find('button.tox-statusbar__wordcount').click();  // if you use jQuery
        },
        setup: function (ed) {
            ed.on('change', function (e) {
                guiddescChange();
            });
        },
    });
}

function guiddescChange() {
    tinymce.triggerSave();
    var tempDivElement = document.createElement("div");
    tempDivElement.innerHTML = $("#progguidhtml").val();
    $("#progguiddesc").val(tempDivElement.textContent || tempDivElement.innerText || "");
    tempDivElement.remove();
}
