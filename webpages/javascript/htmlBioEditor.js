// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// TinyMCE configuration shared by every editor of Participants.htmlbio (My Profile and Administer Participants)

function initHtmlBioEditor(selector, onChange) {
    window.tinymce?.init({
        selector: selector,
        license_key: 'gpl',
        invalid_styles: 'color background-color font-family text-align text-decoration',
        formats: {
            underline: { inline: 'u', exact: true }
        },
        plugins: 'wordcount fullscreen link preview searchreplace autolink charmap nonbreaking visualchars',
        browser_spellcheck: true,
        contextmenu: false,
        height: 400,
        min_height: 200,
        menubar: false,
        toolbar: [
            'undo redo | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | link | preview fullscreen',
            'searchreplace | outdent indent'
        ],
        toolbar_mode: 'wrap',
        content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; }',
        placeholder: 'Type biography here...',
        setup: function (ed) {
            ed.on('change', onChange);
        },
        init_instance_callback: function (editor) {
            // switch the word count display to character count
            $(editor.getContainer()).find('button.tox-statusbar__wordcount').click();
        }
    });
}
