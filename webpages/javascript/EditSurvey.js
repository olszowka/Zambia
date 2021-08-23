// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
var configtable;
var optiontable;
var message = "";
var previewmce = false;
var questionoptions = [];
var in_editconfig = false;  // tabulator seems to want to run the onclick twice, so this prevents that, allowing render preview to work right.
var warningcolor = "#fff3cd";
var hoverdirty = false;
var promptdirty = false;
var optionsdirty = false;
var questionsdirty = false;
var nextrow = null;
var questiondivshown = false;

var EditSurvey = function () {
    var newid = -1;
    var curid = -99999;
    var newoptionid = -1;
    var curoptionid = -99999;

    function escapeQuotesAccessor(value, data, type, params, column, row) {
        //value - original value of the cell
        //data - the data for the row
        //type - the type of access occurring  (data|download|clipboard)
        //params - the accessorParams object passed from the column definition
        //column - column component for the column this accessor is bound to
        //row - row component for the row
        //val = value.replace(/\\/g, '\\\\');
        //return val.replace(/"/g, '\\"');
        //console.log("value: " + value);
        val = btoa(value);
        //console.log("becomes: " + val);
        return val;
    };

    function editor_init() {
        //console.log("editor_init: tinyMCE.init('input#prompt')");
        hoverdirty = false;
        promptdirty = false;
        tinyMCE.init({
            selector: 'input#prompt',
            plugins: 'fullscreen link preview searchreplace autolink charmap nonbreaking visualchars ',
            browser_spellcheck: true,
            contextmenu: false,
            height: 100,
            width: 900,
            min_height: 100,
            maxlength: 512,
            menubar: false,
            toolbar: [
                'undo redo searchreplace | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | forecolor backcolor | link code | preview fullscreen'
            ],
            toolbar_mode: 'floating',
            content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
            placeholder: 'Type prompt here...',
            setup: function (editor) {
                editor.on('Change', function (e) {
                    qfChange("prompt", true);
                    promptdirty = true;
                })
            }
        });

        //console.log("editor_init: tinyMCE.init('textarea#hover')");
        tinyMCE.init({
            selector: 'textarea#hover',
            plugins: 'fullscreen lists advlist link preview searchreplace autolink charmap hr nonbreaking visualchars code ',
            browser_spellcheck: true,
            contextmenu: false,
            height: 200,
            width: 900,
            min_height: 200,
            maxlength: 8192,
            menubar: false,
            toolbar: [
                'undo redo searchreplace | styleselect | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | preview fullscreen ',
                'alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist | forecolor backcolor | link'
            ],
            toolbar_mode: 'floating',
            content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
            placeholder: 'Type hover content here...',
            setup: function (editor) {
                editor.on('Change', function (e) {
                    //console.log(e);
                    qfChange("hover", true);
                    hoverdirty = true;
                })
            }
        });
    }

    function addupdaterow(table, opttable) {
        // update table itself for future save
        tinyMCE.triggerSave();

        var shortname = document.getElementById("shortname").value;
        var description = document.getElementById("description").value;
        var prompt = document.getElementById("prompt").value;
        var hover = document.getElementById("hover").value;
        var typeselect = document.getElementById("typename")
        var typeid = typeselect.selectedOptions.item(0).getAttribute("data-typeid");
        var typename = typeselect.value;
        var required = document.getElementById("required-1").checked ? 1 : 0;
        var publish = document.getElementById("publish-1").checked ? 1 : 0;
        var privacy_user = document.getElementById("privacy_user-1").checked ? 1 : 0;
        var searchable = document.getElementById("searchable-1").checked ? 1 : 0;
        var ascending = document.getElementById("ascending-1").checked ? 1 : 0;
        var display_only = document.getElementById("display_only-1").checked ? 1 : 0;
        var minvalue = document.getElementById("min_value").value;
        var maxvalue = document.getElementById("max_value").value;

        // remove paragraph tags from prompt tag added by tinymce
        if (prompt.substring(0, 3) == '<p>') {
            prompt = prompt.substring(3, prompt.length-4);
        }

        if (curid == -99999) {
            curid = newid;
        }
        //console.log("add/update " + curid);

        if (opttable) {
            options = opttable.getData();
            for (i = 0; i < options.length; i++) {
                options[i].questionid = curid;
            }
            option = btoa(JSON.stringify(options));
            //console.log("-used opttable");
        } else if (questionoptions.length > 0) {
            option = btoa(JSON.stringify(questionoptions));
            //console.log("-used question options: " + questionoptions.length);
        } else {
            option = "";
            //console.log("-no options");
        }
        
       //console.log("options-save");
        //console.log(option);
        table.updateOrAddData([{
            questionid: curid, shortname: shortname, description: description, prompt: prompt, hover: hover,
            typeid: typeid, typename: typename, required: required, publish: publish, privacy_user: privacy_user,
            searchable: searchable, ascending: ascending, display_only: display_only, min_value: minvalue, max_value: maxvalue, options: option
           }, 
        ]);
        newid = newid - 1;
        curid = -99999;
        questionoptions = [];

        document.getElementById("submitbtn").innerHTML = "Save*";
        document.getElementById("previewbtn").style.display = "none";
        document.getElementById("general-question-div").style.display = "none";
        document.getElementById("preview").innerHTML = "";
        //console.log("addupdaterow: tinyMCE.remove");
        tinyMCE.remove();
        previewmce = false;
        table.clearHistory();
        if (opttable) {
            opttable = null;
        }
        questiondivshown = false;
    };

    function addnewquestion() {
        if (questionDirtyCheck(null))
            return;

        editSurvey.processNewQuestion()
    }

    this.processNewQuestion = function () {
        tinyMCE.remove();
        previewmce = false;
        //console.log("addnewquestion: tinyMCE.remove");
        curid = -99999;

        document.getElementById("general-header").innerHTML = '<h3 class="col-auto">New Question - General Configuration</h3>';
        document.getElementById("option-header").innerHTML = '<h4 class="col-auto">New Question - Options</h4>';
        document.getElementById("add-row").innerHTML = "Add to Survey";
        // Default values
        document.getElementById("questionid").value = curid;
        el = document.getElementById("shortname");
        el.value = "";
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("description");
        el.value = "";
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("prompt");
        el.value = "";
        el.setAttribute('default-value', el.value);
        colorel = document.getElementById("prompt-area")
        colorel.style.cssText = null;
        colorel.style.backgroundColor = null;

        el = document.getElementById("hover");
        el.value = "";
        el.setAttribute('default-value', el.value);
        colorel = document.getElementById("hover-area")
        colorel.style.cssText = null;
        colorel.style.backgroundColor = null;

        el = document.getElementById("typename");
        el.value = 'openend';
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("required-1");
        el.checked = true;
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;
        el = document.getElementById("required-0");
        el.checked = false;
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("publish-1");
        el.checked = false;
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;
        el = document.getElementById("publish-0");
        el.checked = true;
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("privacy_user-1");
        el.checked = false;
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;
        el = document.getElementById("privacy_user-0");
        el.checked = true;
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("searchable-1");
        el.checked = false;
        document.getElementById(el.name).style.backgroundColor = null;
        el = document.getElementById("searchable-0");
        el.checked = true;
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("ascending-1");
        el.checked = true;
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;
        el = document.getElementById("ascending-0");
        el.checked = false;
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("display_only-1");
        el.checked = false;
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;
        el = document.getElementById("display_only-0");
        el.checked = true;
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("min_value");
        el.value = "0";
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;
        el = document.getElementById("max_value");
        el.value = "8192";
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;
        // now display it, hiding ones not used by show_radios for default openend
        document.getElementById("asc_desc").style.display = "none";
        document.getElementById("value_range").style.display = "none";
        document.getElementById("general-question-div").style.display = "block";
        document.getElementById("message").style.display = 'none';
        optiontable = null;
        questionoptions = [];
        edit_typechange(false);
        editor_init();
        document.getElementById("preview").innerHTML = "";
    };

    function editconfig(row) {
        // check for dirty prior configuration section
        if (questionDirtyCheck(row))
            return;

        editSurvey.processEditConfig(row);
    }

    this.processEditConfig = function (row) {
        var name = row.getCell("shortname").getValue();
        in_editconfig = true;
        //console.log("editconfig: tinyMCE.remove()");
        tinyMCE.remove();
        previewmce = false;

        document.getElementById("message").style.display = 'none';

        document.getElementById("general-header").innerHTML = '<h3 class="col-auto">' + name + ' - General Configuration</h3>';
        document.getElementById("option-header").innerHTML = '<h4 class="col-auto">' + name + ' - Options</h4>';

        // Set up current value for all row items
        curid = row.getCell("questionid").getValue();
        document.getElementById("questionid").value = curid;

        el = document.getElementById("shortname");
        el.value = name;
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("description");
        el.value = row.getCell("description").getValue();
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("prompt");
        el.value = row.getCell("prompt").getValue();
        el.setAttribute('default-value', el.value);
        colorel = document.getElementById("prompt-area")
        colorel.style.cssText = null;
        colorel.style.backgroundColor = null;

        el = document.getElementById("hover");
        el.value = row.getCell("hover").getValue();
        el.setAttribute('default-value', el.value);
        colorel = document.getElementById("hover-area");
        colorel.style.cssText = null;
        colorel.style.backgroundColor = null;

        el = document.getElementById("typename");
        el.value = row.getCell("typename").getValue();
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("required-1");
        el.checked = row.getCell("required").getValue() == "1";
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;

        el = document.getElementById("required-0");
        el.checked = row.getCell("required").getValue() != "1";
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("publish-1");
        el.checked = row.getCell("publish").getValue() == "1";
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;

        el = document.getElementById("publish-0");
        el.checked = row.getCell("publish").getValue() != "1";
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("privacy_user-1");
        el.checked = row.getCell("privacy_user").getValue() == "1";
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;

        el = document.getElementById("privacy_user-0");
        el.checked = row.getCell("privacy_user").getValue() != "1";
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("searchable-1")
        el.checked = row.getCell("searchable").getValue() == "1";
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;

        el = document.getElementById("searchable-0");
        el.checked = row.getCell("searchable").getValue() != "1";
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("ascending-1");
        el.checked = row.getCell("ascending").getValue() == "1";
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;

        el = document.getElementById("ascending-0");
        el.checked = row.getCell("ascending").getValue() != "1";
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("display_only-1");
        el.checked = row.getCell("display_only").getValue() == "1";
        el.setAttribute('default-value', el.checked);
        document.getElementById(el.name).style.backgroundColor = null;

        el = document.getElementById("display_only-0");
        el.checked = row.getCell("display_only").getValue() != "1";
        el.setAttribute('default-value', el.checked);

        el = document.getElementById("min_value");
        el.value = row.getCell("min_value").getValue();
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        el = document.getElementById("max_value");
        el.value = row.getCell("max_value").getValue();
        el.setAttribute('default-value', el.value);
        el.style.backgroundColor = null;

        options = row.getCell("options").getValue();

        questionoptions = [];
        if (options.length > 3)
            options = atob(options);
        if (options.length > 3)
            questionoptions = JSON.parse(options);
        optiontable = null;

        ////loop over options decoding every value, optionshortname and optionhover
        //for (i = 0; i < questionoptions.length; i++) {
        //    questionoptions[i].value = atob(questionoptions[i].value);
        //    questionoptions[i].optionshort = atob(questionoptions[i].optionshort);
        //    questionoptions[i].optionhover = atob(questionoptions[i].optionhover);
        //}
        //console.log(questionoptions);

        // now show the block
        document.getElementById("add-row").innerHTML = "Update Survey Table";
        document.getElementById("general-question-div").style.display = "block";
        edit_typechange(false);
        editor_init();
        RefreshPreview();
        questiondivshown = true;
    };

   function addnewoption(optiontable) {
        newoptionid = newoptionid - 1;
        optiontable.addRow({questionid: curid, ordinal: newoptionid }, false);
    }

    function edit_typechange(colorchange) {
        document.getElementById("message").style.display = 'none';

        var el = document.getElementById("typename");
        if (colorchange)
            qfChange(el, false);
        var typename = el.value;
        var show_asc_desc = false;
        var show_range = false;
        var range_text = "Value Range:";
        var show_options = false;
        var show_radios = true;
        var default_options = false;
        var show_prompt = true;
        document.getElementById("hover-title").innerHTML = (typename == "heading") ? "Text Lines:" : "Hover:";
        switch (typename) {
            case 'heading':
                show_radios = false;
                show_prompt = false;
                document.getElementById("display_only-1").checked = true;
                document.getElementById("display_only-0").checked = false;
                break;
            case 'hor-radio':
            case 'vert-radio':
            case 'single-pulldown':
            case 'multi-select list':
            case 'multi-checkbox list':
            case 'multi-display':
                show_asc_desc = true;
                show_options = true;
                break;
            case 'openend':
                show_range = true;
                range_text = "Allowable openend legnth:";
                break;
            case 'html-text':
            case 'text':
                show_range = true;
                range_text = "Allowable text legnth:";
                break;
            case 'numberselect':
                show_asc_desc = true;
                show_range = true;
                break;
            case 'number':
                show_range = true;
                break;
            case 'monthnum':
            case 'monthabv':
                show_asc_desc = true;
                default_options = true;
                break;
            case 'monthyear':
                show_asc_desc = true;
                show_range = true;
                default_options = true;
                break;
            case 'country':
            case 'states':
                show_asc_desc = true;
                show_options = true;
                default_options = true;
                break; 
        };
        document.getElementById("range-label").innerHTML = range_text;
        document.getElementById("asc_desc").style.display = show_asc_desc ? 'block' : 'none';
        document.getElementById("prompt-div").style.display = show_prompt ? 'block' : 'none';
        document.getElementById("value_range").style.display = show_range ? 'block' : 'none';
        document.getElementById("radio-div").style.display = show_radios ? 'block' : 'none';
        document.getElementById("optiontable-div").style.display = show_options ? 'block' : 'none';
        document.getElementById("add-option-div").style.display = show_options ? 'block' : 'none';
        document.getElementById("optlegend-div").style.display = show_options ? 'block' : 'none';
        if (default_options) {
            if ((questionoptions.length == 0) || colorchange) {
                defaults = defaultOptions[typename];
                defaultjson = atob(defaults);
                //console.log(defaultjson);
                try {
                    questionoptions = JSON.parse(defaultjson);
                    //console.log(questionoptions);
                } catch (error) {
                    console.log(error);
                }
                questionoptions.forEach(assign_questionid);
                //console.log("added default options");
            }
        }
        if (show_options) {
            optiontable = new Tabulator("#option-table", {
                maxHeight: "250px",
                movableRows: true,
                tooltips: false,
                headerSort: false,
                history: true,
                data: questionoptions,
                index: "ordinal",
                initialSort: [
                    { column: "display_order", dir: "asc" } //sort by this first
                ],
                layout: "fitDataTable",
                cellEdited: optionCellChanged,
                columns: [
                    { rowHandle: true, formatter: "handle", frozen: true, width: 30, minWidth: 30 },
                    { title: "ID", field: "questionid", visible: false },
                    { title: "Order", field: "display_order", visible: false },
                    { title: "Ordinal", field: "ordinal", visible: false },
                    {
                        title: "Value", field: "value", accessorData: escapeQuotesAccessor, width: 120,
                        editor: "input",
                        editorParams: { editorAttributes: { maxlength: 512 } },
                    },
                    {
                        title: "Label", field: "optionshort", accessorData: escapeQuotesAccessor, width: 200,
                        editor: "input",
                        editorParams: { editorAttributes: { maxlength: 64 } },
                    },
                    {
                        title: "Hover Text", field: "optionhover", accessorData: escapeQuotesAccessor, width: 300,
                        editor: "input", editorParams: { editorAttributes: { maxlength: 512 } }
                    },
                    {
                        title: "Other", field: "allowothertext", formatter: "tickCross",
                        editor: "select", editorParams: {
                            values: { 1: "Yes", 0: "No" },
                        }
                    },
                    {
                        title: "Delete", formatter: deleteicon, hozAlign: "center",
                        cellClick: function (e, cell) {
                            deleteQuestion(e, cell.getRow(), configtable);
                        },
                    },
                ],
                rowMoved: function (row) {
                    document.getElementById("message").style.display = 'none';
                    //console.log("Option Row: " + row.getData().optionshort + " has been moved, now row #" + row.getPosition());
                    if (this.getHistoryUndoSize() > 0) {
                        document.getElementById("optundo").disabled = false;
                    }
                },
                dataChanged: function (data) {
                    //data - the updated table data
                    if (this.getHistoryUndoSize() > 0) {
                        optionsdirty = true;
                        document.getElementById("optundo").disabled = false;
                    }
                    el = document.getElementById("add-row");
                    buttontext = el.innerHTML;
                    if (buttontext.substring(buttontext.length - 1) != '*') {
                        el.innerHTML = buttontext + '*';
                    }
                },
            });
        }
    }

    function assign_questionid(option) {
        option.questionid = curid;
    }

    function deleteicon(cell, formattParams, onRendered) {
        return "&#x1F5D1;";
    }
    function deleteQuestion(e, row, questiontable) {
        document.getElementById("message").style.display = 'none';
        row.delete();
    }

    this.initialize = function () {
       //called when EditSurvey page has loaded
        configtable = new Tabulator("#surveyconfig", {
        maxHeight: "250px",
        movableRows: true,
        tooltips: false,
        history: true,
        headerSort: false,
        initialSort: [
            { column: "display_order", dir: "asc" } //sort by this first
        ],
        data: survey,
        index: "questionid",
        layout: "fitDataTable",
        cellEdited: surveyCellChanged,
        columns: [
            { rowHandle: true, formatter: "handle", frozen: true, width: 30, minWidth: 30 },
            { title: "ID", field: "questionid", visible: false },
            { title: "Order", field: "display_order", visible: false },
            {
                title: "Name", field: "shortname", width: 120,
                cellClick: function (e, cell) {
                    if (!in_editconfig)
                        editconfig(cell.getRow());
                },
            },
            {
                title: "Description", field: "description", accessorData: escapeQuotesAccessor,
                formatter: "textarea", visible: false
            },
            {
                title: "Prompt", field: "prompt", accessorData: escapeQuotesAccessor, width: 180,
                editor: "input", editorParams: { editorAttributes: { maxlength: 512 } }
            },
            {
                title: "Hover Text", field: "hover", accessorData: escapeQuotesAccessor, width: 180,
                editor: "input", editorParams: { editorAttributes: { maxlength: 8192 } }
            },
            { title: "Type", field: "typename", width: 140 },
            { title: "Type-ID", field: "typeid", visible: false },
            {
                title: "Display Only", field: "display_only", formatter: "tickCross",
                editor: "select", editorParams: {
                    values: { 1: "Yes", 0: "No" },
                }
            },
            {
                title: "Required", field: "required", formatter: "tickCross",
                editor: "select", editorParams: {
                    values: { 1: "Yes", 0: "No" },
                }
            },
            {
                title: "Publish", field: "publish", formatter: "tickCross",
                editor: "select", editorParams: {
                    values: { 1: "Yes", 0: "No" },
                }
            },
            {
                title: "Privacy", field: "privacy_user", formatter: "tickCross",
                editor: "select", editorParams: {
                    values: { 1: "Yes", 0: "No" },
                }
            },
            {
                title: "Searchable", field: "searchable", formatter: "tickCross",
                editor: "select", editorParams: {
                    values: { 1: "Yes", 0: "No" },
                }
            },  
            {
                title: "Asc/Desc", field: "ascending",
                formatter: "lookup", formatterParams: {
                    1: "Ascending", 0: "Descending", "": "N/A"
                },
                editor: "select", editorParams: {
                    values: { 1: "Ascending", 0: "Descending" },
                }
            },
            { title: "Min", field: "min_value", editor: "number", minWidth: 50, hozAlign: "right" },
            { title: "Max", field: "max_value", editor: "number", minWidth: 50, hozAlign: "right" },
            { title: "Options", field: "options", width: 75, visible: false },
            {
                title: "Delete", formatter: deleteicon, hozAlign: "center",
                cellClick: function (e, cell) {
                    deleteQuestion(e, cell.getRow(), configtable);
                },
            },
        ],
        rowMoved: function (row) {
            document.getElementById("message").style.display = 'none';
            //console.log("Question Row: " + row.getData().shortname + " has been moved to #" + row.getPosition());
            if (this.getHistoryUndoSize() > 0) {
                document.getElementById("undo").disabled = false;
            }
        },
        tooltips: function (cell) {
            switch (cell.getField()) {
                case "shortname":
                    return cell.getData().description;
                case "hover":
                    return cell.getData().hover;
                case "prompt":
                    return cell.getData().prompt;
            }
            return false;
        },
        dataChanged: function (data) {
            //data - the updated table data
            document.getElementById("submitbtn").innerHTML = "Save*";
            document.getElementById("previewbtn").style.display = "none";
            if (this.getHistoryUndoSize() > 0) {
                questionsdirty = true;
                document.getElementById("undo").disabled = false;
            }
        },
    });
        var addnewrowbut = document.getElementById("add-row");
        addnewrowbut.addEventListener('click', function () { addupdaterow(configtable, optiontable); });
        var addnewbut = document.getElementById("add-question");
        addnewbut.addEventListener('click', function () { addnewquestion(configtable); });
        var addoptbut = document.getElementById("add-option");
        addoptbut.addEventListener('click', function () { addnewoption(optiontable); });
       document.getElementById("typename").onchange = function () { edit_typechange(true); };
       //console.log("Setting up options in table");
        for (option in survey_options) {
            //console.log("question: " + option + " = ");
            //console.log(survey_options[option]);
            //console.log(atob(survey_options[option]));
            configtable.updateOrAddData([{ questionid: option, options: survey_options[option] }]);
       };
       document.getElementById("submitbtn").innerHTML = "Save";
       document.getElementById("previewbtn").style.display = "block";
       configtable.clearHistory();
   };

};

var editSurvey = new EditSurvey();

function discardChanges() {
    $("#unsavedWarningModal").modal('hide');
    // first check for options displayed and row clicked
    if (nextrow) {
        editSurvey.processEditConfig(nextrow);
        nextrow = null;
    } else
        editSurvey.processNewQuestion();
}

function questionDirtyCheck(row) {
    if (questiondivshown) {
        var dirty = hoverdirty || promptdirty || optionsdirty;
        if (!dirty) {
            el = document.getElementById("shortname");
            dirty = el.value != el.getAttribute('default-value');
        }
        if (!dirty) {
            el = document.getElementById("description");
            dirty = el.value != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("typename");
            dirty = el.value != el.getAttribute('default-value');
        }
        // for radios, only need to check one of the two because both change if 1 is changed
        if (!dirty) {
            el = document.getElementById("required-1");
            dirty = el.checked.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("publish-1");
            dirty = el.checked.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("privacy_user-1");
            dirty = el.checked.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("searchable-1");
            dirty = el.checked.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("ascending-1");
            dirty = el.checked.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("display_only-1");
            dirty = el.checked.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("min_value");
            dirty = el.value.toString() != el.getAttribute('default-value');
        }

        if (!dirty) {
            el = document.getElementById("max_value");
            dirty = el.value.toString() != el.getAttribute('default-value');
        }

        if (dirty) {
            nextrow = row;
            $("#unsavedWarningModal").modal('show');
            return true;
        }
    }
    return false;
}

function optionCellChanged(cell) {
    optionsdirty = true;
    cell.getElement().style.backgroundColor = warningcolor;
}

function surveyCellChanged(cell) {
    questionsdirty = true;
    cell.getElement().style.backgroundColor = warningcolor;
}

function snChange(el, mce) {
    snValue = el.value;
    if (snValue.match("[\"'<>/#?\$]")) {
        alert("Invalid characters found in name, name cannot contain \" ' < > / # ? or $, restoring the original value");
        el.value = el.getAttribute("default-value");
        return;
    }
qfChange(el, mce);
}

function qfChange(el, mce) {
    if (mce) {
        el = document.getElementById(el);
    }
    var logmsg = "qfChange: " + el.id + ", type=" + el.type;
    var value = null;
    var default_value = el.getAttribute("default-value");
    var colorel = el;
    switch (el.type) {
        case 'radio':
            value = el.checked.toString();
            logmsg += ", checked=" + value;
            //console.log("name = " + el.name);
            colorel = document.getElementById(el.name);
            break;
        case 'text':
        case 'textarea':
            value = el.value;
            logmsg += ", text=" + value;
            colorel = document.getElementById(el.id + "-area");
            if (colorel === null)
                colorel = el;
            break;
        default:
            value = el.value;
            logmsg += ", value=" + value;
    }

    logmsg += ", default-value=" + default_value;
    //console.log(logmsg);
    //console.log("typeof default_value: " + typeof default_value + " typeof value: " + typeof value);
    if ((default_value != value) || mce) {
        //console.log("!=");
        if (mce) 
            colorel.style.cssText = "border-width: 4px; border-style: solid; border-color: " + warningcolor + ";";

        colorel.style.backgroundColor = warningcolor;
    }
    else {
        //console.log("==");
        //console.log(colorel);
        colorel.style.backgroundColor = null;
    }
}

function cleanupSave(message, classtext) {
    document.getElementById("saving_div").style.display = "none";
    el = document.getElementById("submitbtn");
    el.disabled = false;

    el = document.getElementById("message");
    if (message != "") {
        el.innerHTML = message;
        el.className = classtext;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
};

function saveComplete(data, textStatus, jqXHR) {
    message = "";
    //console.log(data);
    try {
        data_json = JSON.parse(data);
    } catch (error) {
        console.log(error);
    }

    //console.log(data_json);
    if (data_json.hasOwnProperty("message"))
        message = data_json.message;

    if (data_json.hasOwnProperty("survey")) {
        survey = atob(data_json.survey);
        //console.log("post atob");
        //console.log(survey);
        try {
            survey = JSON.parse(survey);
        } catch (error) {
            console.log(error);
        }
        configtable.replaceData(survey);
    }

    if (data_json.hasOwnProperty("survey_options")) {
        survey_options = data_json.survey_options;
        //console.log(survey_options);

        for (option in survey_options) {
            //console.log(option);
            //console.log(survey_options[option]);
            configtable.updateOrAddData([{ questionid: option, options: survey_options[option] }]);
        };
    }

    document.getElementById("previewbtn").style.display = "block";
    document.getElementById("redo").disabled = true;
    document.getElementById("undo").disabled = true;
    document.getElementById("optredo").disabled = true;
    document.getElementById("optundo").disabled = true;

    el = document.getElementById("submitbtn");
    el.innerHTML = "Save";

    cleanupSave(message, 'alert alert-success mt-4');
}

function saveError(xhdr, status, error) {
    message = "Save error: " + xhdr.status + ': ' + xhdr.statusText;
    cleanupSave(message, 'alert alert-danger mt-4');
}

function SaveSurvey() {
    //rows = configtable.getDataCount();
    //console.log("there are " + rows + " questions in the survey");
    //console.log("sorters: ");
    //console.log(configtable.getSorters());
    //for (r = 0; r < rows; r++) {
    //    row = configtable.getRowFromPosition(r);
    //    console.log("q[" + r + "] = " + row.getCell("shortname").getValue() + ": q" + row.getCell("questionid").getValue() +
    //        ", d" + row.getCell("display_order").getValue());
    //}
    //return false;

    document.getElementById("saving_div").style.display = "block";
    document.getElementById("submitbtn").disabled = true;
    document.getElementById("general-question-div").style.display = "none";
    document.getElementById("message").style.display = 'none';

    var postdata = {
        ajax_request_action: "update_survey",
        survey: btoa(JSON.stringify(configtable.getData()))
    };
    $.ajax({
        url: "SubmitEditSurvey.php",
        dataType: "html",
        data: postdata,
        success: saveComplete,
        error: saveError,
        type: "POST"
    }); 
};

function FetchSurvey() {
    var postdata = {
        ajax_request_action: "fetch_survey"
    };
    document.getElementById("general-question-div").style.display = "none";
    document.getElementById("message").style.display = 'none';
    $.ajax({
        url: "SubmitEditSurvey.php",
        dataType: "html",
        data: postdata,
        success: saveComplete,
        error: saveError,
        type: "POST"
    });
};
function PreviewSurvey() {
    window.open("PreviewSurvey.php", '_blank');
}
function Undo() {
    configtable.undo();

    var undoCount = configtable.getHistoryUndoSize();
    if (undoCount <= 0) {
        document.getElementById("undo").disabled = true;
        questionsdirty = false;
    }
    var redoCount = configtable.getHistoryRedoSize();
    if (redoCount > 0) {
        document.getElementById("redo").disabled = false;
    }
};

function Redo() {
    configtable.redo();

    var undoCount = configtable.getHistoryUndoSize();
    if (undoCount > 0) {
        questionsdirty = true;
        document.getElementById("undo").disabled = false;
    }
    var redoCount = configtable.getHistoryRedoSize();
    if (redoCount <= 0) {
        document.getElementById("redo").disabled = true;
    }
};

function OptUndo() {
    optiontable.undo();

    var undoCount = optiontable.getHistoryUndoSize();
    if (undoCount <= 0) {
        optionsdirty = false;
        document.getElementById("optundo").disabled = true;
    }
    var redoCount = optiontable.getHistoryRedoSize();
    if (redoCount > 0) {
        document.getElementById("optredo").disabled = false;
    }
};

function OptRedo() {
    optiontable.redo();

    var undoCount = optiontable.getHistoryUndoSize();
    if (undoCount > 0) {
        optionsdirty = true;
        document.getElementById("optundo").disabled = false;
    }
    var redoCount = optiontable.getHistoryRedoSize();
    if (redoCount <= 0) {
        document.getElementById("optredo").disabled = true;
    }
};

function RefreshComplete(data, textStatus, jqXHR) {
    var preview = "<br/><h4>Preview</h4>" + data;
    document.getElementById("preview").innerHTML = preview;
    fieldname = document.getElementById("shortname").value.replace(/ /g, '_'); 
    id = fieldname + "-prompt";
    hoverelement = document.getElementById(id);
    if (hoverelement != null) {
        hover = document.getElementById("hover").value;
        hover = '<span class="text-left" style="white-space: nowrap;">' + hover + '</span>';
        hoverelement.setAttribute('title', hover);
        $('#' + id).tooltip();
    }
    typename = document.getElementById("typename").value;
   
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

    if (typename == 'html-text') {
        maxlength = document.getElementById("max_value").value;
        if (maxlength == "" || maxlength <= 0) {
            maxlength = 8192;
        }

        if (previewmce == false) {
            //console.log("RefreshComplete: tinyMCE.init(textarea#" + fieldname + '-input)');
            tinyMCE.init({
                selector: 'textarea#' + fieldname + '-input',
                plugins: 'fullscreen lists advlist link preview searchreplace autolink charmap hr nonbreaking visualchars code ',
                browser_spellcheck: true,
                contextmenu: false,
                height: 200,
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
            previewmce = true;
        }
    }

    in_editconfig = false;
}

function RefreshError(xhdr, status, error) {
    var message = '<div class="alert alert-danger mt-4">Preview error: ' + xhdr.status + ': ' + xhdr.statusText + "</div>";
    var preview = "<br/><h4>Preview</h4>" + message;
    document.getElementById("preview").innerHTML = preview;

    in_editconfig = false;
}

function RefreshPreview() {
    //console.log("In RefreshPreview");
    //console.trace();
    if (previewmce) {
        fieldname = '#' + document.getElementById("shortname").value.replace(/ /g, '_') + '-input';
        tinyMCE.remove(fieldname);
        previewmce = false;
    }
    var questionid = document.getElementById("questionid").value
    //console.log(questionid);
    var shortname = document.getElementById("shortname").value;
    var typename = document.getElementById("typename").value;
    var prompt = document.getElementById("prompt").value;
    if (prompt.substring(0, 3) == '<p>') {
        prompt = prompt.substring(3, prompt.length - 4);
    }
    tinyMCE.triggerSave();
    var hover = document.getElementById("hover").value;
    var surveyData = [{
        questionid: questionid,
        shortname: shortname,
        prompt: prompt,
        hover: hover,
        typeid: document.getElementById("typename").selectedOptions.item(0).getAttribute("data-typeid"),
        typename: document.getElementById("typename").value,
        required: document.getElementById("required-1").checked ? 1 : 0,
        publish: document.getElementById("publish-1").checked ? 1 : 0,
        privacy_user: document.getElementById("privacy_user-1").checked ? 1 : 0,
        searchable: document.getElementById("searchable-1").checked ? 1 : 0,
        ascending: document.getElementById("ascending-1").checked ? 1 : 0,
        display_only: document.getElementById("display_only-1").checked ? 1 : 0,
        min_value: document.getElementById("min_value").value,
        max_value: document.getElementById("max_value").value
    }];
    var optionsjson;
    var questions;
    var json = "";
    if (optiontable) {
        //console.log("from optiontable");
        var options = optiontable.getData();
        var allowothertext = false;
        var i;
        for (i = 0; i < options.length; i++) {
            options[i].questionid = questionid;
            allowothertext = allowothertext || options[i].allowothertext;
        }
        surveyData[0].allowothertext = allowothertext;
        optionsjson = JSON.stringify(options);
        //console.log("optiontable:");
        //console.log(optionsjson);
    } else if (questionoptions.length > 0) {
        //console.log("from questionoptions");
        var i;
        for (i = 0; i < questionoptions.length; i++) {
            questionoptions[i].questionid = questionid;
        }
        optionsjson = JSON.stringify(questionoptions);
        //console.log("questionoptions:");
        //console.log(optionsjson);
    }
    //console.log("options");
    //console.log(options);
    
    //console.log("questions");
    //console.log(surveyData);
    json = JSON.stringify(surveyData);
    //console.log("json: '" + json + "'");
    questions = [{ id: questionid, data: btoa(json)}];
    //console.log(questions);

    var postdata = {
        ajax_request_action: "renderquestion",
        questions: JSON.stringify(questions),
        options: optionsjson
    };
    $.ajax({
        url: "RenderSurveyPreview.php",
        dataType: "html",
        data: postdata,
        success: RefreshComplete,
        error: RefreshError,
        type: "POST"
    });
};