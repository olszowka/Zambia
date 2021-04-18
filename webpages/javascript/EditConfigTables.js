// Created by Syd Weinstein on 2021-01-04;
// Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var table = null;
var tablename = '';
var message = "";
var indexcol = 'display_order';
var selectlist = null;
var newid = -99;
var fetch_json = {};
var tableschema = null;
var curcell = null;
var dirty = false;
var nexttab = null;

var EditConfigTable = function () {

    function tabshown(newtabname) {
        var tabname = newtabname;

       // if top level tab clicked, find which sub tab is open
        if (newtabname.match(/-top$/i)) {
            $("a.active").each(function () {
                attr = $(this).attr("data-top");
                if (attr === newtabname) {
                    //console.log(", top=" + $(this).attr("data-top"));
                    tabname = this.id;
                }
            });
        }

        // now with the subtab (clicked or refed by top), see if it needs a table and data fetched
        if (tabname.substring(0, 2) !== 't-')
            document.getElementById("table-div").style.display = "none";
        else {
            document.getElementById("table-div").style.display = "block";
            tablename = tabname.substring(2);
            //console.log('new table: ' + tablename);
            FetchTable();
        }
    }

    // show unsaved data modal popup if dirty
    function tabprehide(tabname, newtab) {
        //console.log('prehide:' + tabname + ", " + newtab + ", dirty: " + dirty);
        if (!dirty)
            return true;
        nexttab = newtab;
        $("#unsavedWarningModal").modal('show');
        return false;
    }

    // clear table of any tab being closed
    function tabhide() {
        if (table) {
            table = null;
        }
    }

    this.initialize = function () {
        var $tab = $('.nav-tabs a');
        $tab.on('shown.bs.tab', function (event) {
            var x = event.target.id;         // active tab
            tabshown(x);
        });
        $tab.on('hide.bs.tab', function (event) {
            var x = event.target.id;        // to be hidden tab
            var n = event.relatedTarget.id;    // to be shown tab
            //console.log('act = ' + x);
            return tabprehide(x, n);
        });
        $tab.on('hidden.bs.tab', function (event) {
            var x = event.target.id;        // active tab
            //console.log('act = ' + x);
            tabhide(x);
            $("#unsavedWarningModal").modal({show: false});
        });
    }
};

var editConfigTable = new EditConfigTable();

function discardChanges() {
    //console.log("in discardChanges(), nexttab = '" + nexttab + "'");
    $("#unsavedWarningModal").modal('hide');
    dirty = false;
    if (nexttab) {
        //console.log("going to tab: " + nexttab);
        $('#' + nexttab).tab('show');
        //tabshown(nexttab);
    }
    return true;
}

function savetceEdit(display) {
    if (curcell) {
        tinyMCE.triggerSave();
        newval = txtel.value;
        newval = newval.replace(/<\/p>[ \r\n]*<p>/gi, "\n");
        newval = newval.replace(/<br *\/*>/gi, "\n");
        newval = newval.replace(/^<p> */i, "");
        newval = newval.replace(/ *<\/p> *$/i, "");
        curcell.setValue(newval);
        tinyMCE.remove();
        curcell = null;
        if (display) {
            document.getElementById("tceedit-div").style.display = "none";
            document.getElementById("add-row").disabled = false;
            document.getElementById("resetbtn").disabled = false;
            document.getElementById("submitbtn").disabled = false;
            var undoCount = table.getHistoryUndoSize();
            if (undoCount > 0) {
                document.getElementById("undo").disabled = false;
            }
            var redoCount = table.getHistoryRedoSize();
            if (redoCount > 0) {
                document.getElementById("redo").disabled = false;
            }
        }
    }
}

function tceEditor(e, cell) {
    txtel = document.getElementById("tceedit-textarea");
    if (cell !== curcell) {
        savetceEdit(false);
    }
    cellname = cell.getField();        
    // initialize the starting value from the current value of the cell
    curcell = cell;
    txtel.value = cell.getValue().replace(/\n/g, "<br/>");

    el = document.getElementById("tceedit-div");
    el.style.display = "block";
    tinyMCE.init({
        setup: function (editor) {
            editor.ui.registry.addButton('customSaveButton', {
                icon: 'save',
                tooltip: 'Save contents back to table',
                onAction: function () {
                    savetceEdit(true);
                }
            })
        },
        selector: 'textarea#tceedit-textarea',
        plugins: 'fullscreen searchreplace charmap nonbreaking visualchars',
        browser_spellcheck: true,
        contextmenu: false,
        height: 400,
        min_height: 200,
        menubar: false,
        toolbar: [
            ' customSaveButton | undo redo | searchreplace |visualchars nonbreaking charmap | fullscreen'
        ],
        toolbar_mode: 'wrap',
        content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
        placeholder: 'Type custom content here...'
    });
    document.getElementById("add-row").disabled = true;
    document.getElementById("resetbtn").disabled = true;
    document.getElementById("submitbtn").disabled = true; 
    document.getElementById("undo").disabled = true;
    document.getElementById("redo").disabled = true;
}

function deleteicon(cell, formattParams, onRendered) {
    var value = cell.getValue();
    return value === '0' ? '&#x1F5D1;' : value;
}

function deleterow(e, row, questiontable) {
    document.getElementById('message').classList.add('hidden');
    var count = row.getCell("Usage_Count").getValue();
    if (count === '0') {
        row.delete();
    }
}

function addnewrow(table) {
    newid = newid - 1;
    var rowtxt = "row = { " + indexcol + ": " + newid + ", display_order: 99999, Usage_Count: 0 };";
    //console.log(rowtxt);
    eval(rowtxt);
    table.addRow(row, false);
}

function cellChanged(cell) {
    dirty = true;
    cell.getElement().style.backgroundColor = "#fff3cd";
}

function opentable(tabledata) {
    // get table information from tableschema
    //console.log(tableschema);
    columns = [];
    indexcol = 'display_order';
    displayorder_found = false;
    initialsort = [];
    columns.push({ rowHandle: true, formatter: "handle", frozen: true, width: 30, minWidth: 30, maxWidth:30 });
    tableschema.forEach(function (column) {
        if (column.COLUMN_KEY === 'PRI') {
            indexcol = column.COLUMN_NAME;
            initialsort.push({ column: indexcol, dir: "asc" });
            columns.push({
                title: indexcol, field: indexcol,
                visible: false
            });
        } else if (column.COLUMN_NAME === 'display_order') {
            columns.push({ title: "Order", field: "display_order", visible: false });
            display_order = true;
        } else if (fetch_json.hasOwnProperty(column.COLUMN_NAME + "_select")) {
            selectlistname = column.COLUMN_NAME + "_select";
            editor_type = 'select';
            selectlist = [];
            fetch_json[column.COLUMN_NAME + "_select"].forEach(function (entry) { selectlist[entry.id] = entry.name; });
            editor_params = { values: selectlist };
            columns.push({
                title: column.COLUMN_NAME, field: column.COLUMN_NAME,
                visible: true,
                editor: editor_type,
                editorParams: editor_params,
                formatter: "lookup",
                formatterParams: selectlist,
                minWidth: 200
            });
        } else if (column.DATA_TYPE === 'int')
            columns.push({
                title: column.COLUMN_NAME, field: column.COLUMN_NAME,
                editor: "number", minWidth: 50, hozAlign: "right", 
            });
        else if (column.DATA_TYPE === 'text') {
            width = 8 * column.CHARACTER_MAXIMUM_LENGTH;
            if (width < 80) width = 80;
            if (width > 500) width = 500;
            columns.push({
                title: column.COLUMN_NAME, field: column.COLUMN_NAME, width: width,
                cellClick: tceEditor
            });
        } else {
            width = 8 * column.CHARACTER_MAXIMUM_LENGTH;
            if (width < 80) width = 80;
            if (width > 500) width = 500;
            columns.push({
                title: column.COLUMN_NAME, field: column.COLUMN_NAME, editor: "input", width: width,
                editorParams: { editorAttributes: { maxlength: column.CHARACTER_MAXIMUM_LENGTH } },
            });
        }
    });
    columns.push({
        title: "Delete", field: "Usage_Count", formatter: deleteicon, hozAlign: "center",
        cellClick: function (e, cell) {
            deleterow(e, cell.getRow(), table);
        }
    });
    //console.log(columns);
    
    if (display_order) {
        initialsort = [];
        initialsort.push({ column: "display_order", dir: "asc" });
    }
    document.getElementById("table-div").style.display = "block";
    table = new Tabulator("#table", {
        maxHeight: "400px",
        movableRows: true,
        tooltips: false,
        history: true,
        headerSort: false,
        initialSort: initialsort,  
        data: tabledata,
        index: indexcol,
        layout: "fitDataTable",
        cellEdited: cellChanged,
        //autoColumns: true,
        columns: columns,
        rowMoved: function (row) {
            document.getElementById('message').classList.add('hidden');
            //console.log("Question Row: " + row.getData().shortname + " has been moved to #" + row.getPosition());
            if (this.getHistoryUndoSize() > 0) {
                dirty = true;
                document.getElementById("undo").disabled = false;
            }
        },
        dataChanged: function (data) {
            //data - the updated table data
            dirty = true;
            document.getElementById("submitbtn").innerHTML = "Save*";
            if (this.getHistoryUndoSize() > 0) {
                document.getElementById("undo").disabled = false;
            }
        },
    });
    var addnewrowbut = document.getElementById("add-row");
    addnewrowbut.addEventListener('click', function () { addnewrow(table); });
    //console.log("Setting up options in table");
    document.getElementById("submitbtn").innerHTML = "Save";
    table.clearHistory();
}

function saveComplete(data, textStatus, jqXHR) {
    message = "";
    errorMessage = "";
    //console.log(data);
    try {
        fetch_json = JSON.parse(data);
    } catch (error) {
        fetch_json = {};
    }
    //console.log(fetch_json);
    if (fetch_json.hasOwnProperty('message')) {
        message = fetch_json.message;
    }
    if (fetch_json.hasOwnProperty('errorMessage')) {
        errorMessage = fetch_json.errorMessage;
    }


    if (fetch_json.hasOwnProperty('tableschema')) {
        tableschema = fetch_json.tableschema;
    } else {
        tableschema = null;
        message += '<br/>Error: no schema returned for this table';
    }

    $message = document.getElementById('message');
    if (!$message) {
        return;
    }
    if (errorMessage) {
        $message.innerHTML = errorMessage + (message ? '<br/>' : '') + message;
        $message.classList.remove('alert-success', 'hidden');
        $message.classList.add('alert-danger');
        return;
    }
    if (message) {
        $message.innerHTML = message;
        $message.classList.remove('alert-danger', 'hidden');
        $message.classList.add('alert-success');
    } else {
        $message.classList.add('hidden');
    }
    if (fetch_json.hasOwnProperty('tabledata')) {
        if (table) {
            table.replaceData(fetch_json.tabledata);
        }
        else {
            opentable(fetch_json.tabledata);
        }
    }
   
    document.getElementById("saving_div").style.display = "none";
    el = document.getElementById("submitbtn");
    el.disabled = false;
    el.innerHTML = "Save";
    dirty = false;
    document.getElementById("redo").disabled = true;
    document.getElementById("undo").disabled = true;
}

function SaveTable() {
    document.getElementById("saving_div").style.display = "block";
    document.getElementById("submitbtn").disabled = true;
    document.getElementById('message').classList.add('hidden');
    //console.log(table.getData());
    // table.getData() provides data in the order it was loaded, not order it is currently displayed.
    var postdata = {
        ajax_request_action: "updatetable",
        tabledata: btoa(JSON.stringify(table.getData())),
        tablename: tablename,
        indexcol: indexcol
    };
    $.ajax({
        url: "SubmitEditConfigTable.php",
        dataType: "html",
        data: postdata,
        success: saveComplete,
        error: showAjaxError,
        type: "POST"
    }); 
}

function FetchTable() {
    var postdata = {
        ajax_request_action: "fetchtable",
        tablename: tablename
    };
    $.ajax({
        url: "SubmitEditConfigTable.php",
        dataType: "html",
        data: postdata,
        success: saveComplete,
        error: showAjaxError,
        type: "POST"
    });
}

function Undo() {
    table.undo();

    var undoCount = table.getHistoryUndoSize();
    if (undoCount <= 0) {
        document.getElementById("undo").disabled = true;
        dirty = false;
    }
    var redoCount = table.getHistoryRedoSize();
    if (redoCount > 0) {
        document.getElementById("redo").disabled = false;
    }
}

function Redo() {
    table.redo();

    var undoCount = table.getHistoryUndoSize();
    if (undoCount > 0) {
        document.getElementById("undo").disabled = false;
        dirty = true;
    }
    var redoCount = table.getHistoryRedoSize();
    if (redoCount <= 0) {
        document.getElementById("redo").disabled = true;
    }
}

function showAjaxError(data, textStatus, jqXHR) {
    var $mesageDIV = document.getElementById('message');
    if (!$mesageDIV) {
        return;
    }
    if (data && data.responseText) {
        $mesageDIV.innerHTML = data.responseText;
    } else {
        $mesageDIV.innerHTML = 'An error occurred on the server.';
    }
    $mesageDIV.classList.remove('hidden', 'alert-success');
    $mesageDIV.classList.add('alert-danger');
    window.scrollTo(0, 0);
}

