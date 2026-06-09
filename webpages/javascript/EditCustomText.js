// Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.

var EditCustomText = function () {

    var currentCustomTextID = -1;
    var initialCustomText = '';
    var currentInitialActive = 0;
    var currentHtmlBlockLevel = '0';

    function UpdatePage(customTextID, initialText, initialActive, htmlBlockLevel) {
        tinyMCE.remove();

        if (currentCustomTextID < 0 && customTextID >= 0) {
            $("#customtextid option[value='-1']").remove();
        }

        currentCustomTextID = customTextID;
        initialCustomText = initialText;
        currentInitialActive = initialActive;
        currentHtmlBlockLevel = htmlBlockLevel;

        $('#active').prop('checked', initialActive === 1);
        $('#textcontents').val(initialText);
        $('#texteditor').css('display', 'block');
        $('#activeeditor').css('display', 'block');

        const tinyMceInitObject = {
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
            placeholder: 'Type custom content here...',
        }

        if (htmlBlockLevel === '1') {
            $('#htmlBlockInstructions').css('display', 'block');
            $('#notHtmlBlockInstructions').css('display', 'none');
        } else {
            $('#htmlBlockInstructions').css('display', 'none');
            $('#notHtmlBlockInstructions').css('display', 'block');
            tinyMceInitObject.forced_root_block = ' ';
        }

        tinyMCE.init(tinyMceInitObject);
    }


    function UpdateSelected() {
        const $customtextid = $('#customtextid');
        if (currentCustomTextID >= 0) {
            tinyMCE.triggerSave();
            
            const newText = document.getElementById('textcontents').value; // Jquery returned Undefined for this, using document. method
            if (newText !== initialCustomText) {
                if (!confirm('Discard changes?')) {
                    $customtextid.val(currentCustomTextID);
                    return;
                }
            }
        }
        const customTextID = $customtextid.val();
        const customTextValue = $customtextid.find(':selected').data('initialtext');
        const customTextActive = parseInt($customtextid.find(':selected').data('initialactive'));
        const htmlBlockLevel = $customtextid.find(':selected').data('htmlblocklevel');
        UpdatePage(customTextID, customTextValue, customTextActive, htmlBlockLevel);
    }

    this.resetPage = function() {
        if (currentCustomTextID >= 0) {
            UpdatePage(currentCustomTextID, initialCustomText, currentInitialActive, currentHtmlBlockLevel);
        }
    }

    this.savePage = function () {
        if (currentCustomTextID >= 0) {
            tinyMCE.triggerSave();
            const $active = $('#active');
            if ($active.prop('checked')) {
                $active.val(1);
            } else {
                $active.val(0)
                $active.prop('checked', true);
            }
        }
    }

    this.initialize = function () {
        //called when EditCustomText page has loaded

        const resetBtnElem = document.getElementById('resetbtn');
        resetBtnElem.addEventListener('click', this.resetPage);
        const submitBtnElem = document.getElementById('submitbtn');
        submitBtnElem.addEventListener('click', this.savePage);

        const customTextIdSelElem = document.getElementById('customtextid');
        customTextIdSelElem.addEventListener('change', UpdateSelected);
        const customTextID = customTextIdSelElem.options[customTextIdSelElem.selectedIndex].value;
        if (customTextID >= 0) {
            const customTextValue = customTextIdSelElem.options[customTextIdSelElem.selectedIndex].dataset.initialtext;
            const customTextActive = parseInt( customTextIdSelElem.options[customTextIdSelElem.selectedIndex].dataset.initialactive);
            const htmlBlockLevel = customTextIdSelElem.options[customTextIdSelElem.selectedIndex].dataset.htmlblocklevel;
            UpdatePage(customTextID, customTextValue, customTextActive, htmlBlockLevel);
        }
    };
};

var editCustomText = new EditCustomText();
