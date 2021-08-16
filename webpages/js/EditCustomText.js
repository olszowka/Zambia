// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.

var EditCustomText = function () {

    var currentCustomTextID = -1;
    var initialCustomText = "";

    function UpdateTextEditor(customtextid, initialtext) {
        tinyMCE.remove();

        if (currentCustomTextID < 0 && customtextid >= 0) {
            $("#customtextid option[value='-1']").remove();
        }

        currentCustomTextID = customtextid;
        initialCustomText = initialtext;

        $('#textcontents').val(initialtext);
        $('#texteditor').css("display", "block");

        tinyMCE.init({
            selector: 'textarea#textcontents',
            plugins: 'table wordcount fullscreen lists advlist link preview searchreplace autolink charmap hr nonbreaking visualchars code ',
            browser_spellcheck: true,
            contextmenu: false,
            height: 400,
            min_height: 200,
            menubar: false,
            toolbar: [
                'undo redo | styleselect | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | preview fullscreen ',
                'searchreplace | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist | forecolor backcolor | link code'
            ],
            toolbar_mode: 'wrap',
            content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
            placeholder: 'Type custom content here...'
        });
    }

    function ResetTextarea() {
        if (currentCustomTextID >= 0) {
            UpdateTextEditor(currentCustomTextID, initialCustomText);
        }
    }

    function SaveTextaarea() {
        if (currentCustomTextID >= 0) {
            tinyMCE.triggerSave();
            mysubmit();
        }
    }

    function UpdateSelected() {
        var $customtextid = $('#customtextid');
        if (currentCustomTextID >= 0) {
            tinyMCE.triggerSave();
            
            var newText = document.getElementById("textcontents").value; // Jquery returned Undefined for this, using document. method
            if (newText !== initialCustomText) {
                if (!confirm("Discard changes?")) {
                    $customtextid.val(currentCustomTextID);
                    return;
                }
            }
        }
        var strValue = $customtextid.val();
        var strText = $customtextid.find(':selected').data("initialtext");
        UpdateTextEditor(strValue, strText);
    }

    this.initialize = function () {
        //called when EditCustomText page has loaded

        var e = document.getElementById("resetbtn");
        e.addEventListener('click', ResetTextarea);

        e = document.getElementById("customtextid");
        e.addEventListener('change', UpdateSelected);
        var strValue = e.options[e.selectedIndex].value;
        if (strValue >= 0) {
            var strText = e.options[e.selectedIndex].dataset.initialtext;
            UpdateTextEditor(strValue, strText);
        }
    };

};

var editCustomText = new EditCustomText();