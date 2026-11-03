// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
function mysubmit() {
    $("#sessionid").prop("disabled",false);
    window.tinymce?.triggerSave();
}

function initializeSessionEdit() {
    //called when JQuery says render session page has loaded
    //debugger;
    window.tinymce?.init({
        selector: 'textarea#progguidhtml',
        license_key: 'gpl',
        invalid_styles: 'color background-color font-family text-align text-decoration',
        formats: {
            underline: { inline: 'u', exact: true }
        },
        plugins: 'table wordcount fullscreen link preview searchreplace autolink charmap nonbreaking visualchars ',
        browser_spellcheck: true,
        contextmenu: false,
        height: 350,
        min_height: 200,
        menubar: false,
        toolbar: [
            'undo redo | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | link| preview fullscreen ',
            'searchreplace | outdent indent'
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
    window.tinymce?.triggerSave();
    var tempDivElement = document.createElement("div");
    tempDivElement.innerHTML = $("#progguidhtml").val();
    $("#progguiddesc").val(tempDivElement.textContent || tempDivElement.innerText || "");
    tempDivElement.remove();
}
