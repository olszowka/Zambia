// Created by Syd Weinstein on 2021-01-04;
// Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var table = null;
var tablename = '';
var message = "";
var previewmce = false;
var indexcol = 'display_order';
var selectlist = null;
var newid = -99;
var newrow = null;
var fetch_json = {};
var tableschema = null;
var curcell = null;

var EditConfigTable = function () {

    function tabshown(tabname) {   
        if (tabname.substring(0, 2) != 't-')
            document.getElementById("table-div").style.display = "none";
        else {
            document.getElementById("table-div").style.display = "block";
            tablename = tabname.substring(2);
            //console.log('new table: ' + tablename);
            FetchTable();
        }
    }

    function tabhide(tabname) {
        if (table) {
            table = null;
        }

        if (tabname == '')
            return;
    }

    this.initialize = function () {
       $('.nav-tabs a').on('shown.bs.tab', function (event) {
           var x = event.target.id;         // active tab
            tabshown(x);
        });
        $('.nav-tabs a').on('hidden.bs.tab', function (event) {
            var x = event.target.id         // active tab
            //console.log('act = ' + x);
            tabhide(x);
        });
    }
};

var editConfigTable = new EditConfigTable();

function savetceEdit(display) {
    if (curcell) {
        tinyMCE.triggerSave();
        newval = txtel.value;
        newval = newval.replace(/\<\/p\>[ \r\n]*\<p\>/gi, "\n");
        newval = newval.replace(/\<br *\/*\>/gi, "\n");
        newval = newval.replace(/^\<p\> */i, "");
        newval = newval.replace(/ *\<\/p\> *$/i, "");
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
    if (cell != curcell) {
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
};

function deleteicon(cell, formattParams, onRendered) {
    var value = cell.getValue();
    if (value == 0)
        return "&#x1F5D1;";
    return value;
}
function deleterow(e, row, questiontable) {
    document.getElementById("message").style.display = 'none';
    var count = row.getCell("Usage_Count").getValue();
    if (count == 0)
        row.delete();
}

function addnewrow(table) {
    newid = newid - 1;
    var rowtxt = "row = { " + indexcol + ": " + newid + ", display_order: 99999, Usage_Count: 0 };";
    //console.log(rowtxt);
    eval(rowtxt);
    table.addRow(row, false);
}

function cellChanged(cell) {
    console.log(cell.getValue());
    cell.getElement().style.backgroundColor = "#c0c0ff";
}

function opentable(tabledata) {
    // get table information from tableschema
    //console.log(tableschema);
    columns = new Array();
    indexcol = 'display_order';
    displayorder_found = false;
    initialsort = new Array();
    columns.push({ rowHandle: true, formatter: "handle", frozen: true, width: 30, minWidth: 30 });
    tableschema.forEach(function (column) {
        if (column.COLUMN_KEY == 'PRI') {
            indexcol = column.COLUMN_NAME;
            initialsort.push({ column: column.COLUMN_NAME, dir: "asc" });
            columns.push({
                title: column.COLUMN_NAME, field: column.COLUMN_NAME,
                visible: false
            });
        } else if (column.COLUMN_NAME == 'display_order') {
            columns.push({ title: "Order", field: "display_order", visible: false });
            display_order = true;
        } else if (fetch_json.hasOwnProperty(column.COLUMN_NAME + "_select")) {
            selectlistname = column.COLUMN_NAME + "_select";
            editor_type = 'select';
            selectlist = new Array();
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
        } else if (column.DATA_TYPE == 'int')
            columns.push({
                title: column.COLUMN_NAME, field: column.COLUMN_NAME,
                editor: "number", minWidth: 50, hozAlign: "right",
                headerSort: true
            });
        else if (column.DATA_TYPE == 'text') {
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
                headerSort: true
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
    
    if (displayorder_found) {
        initialsort = new Array();
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
            document.getElementById("message").style.display = 'none';
            //console.log("Question Row: " + row.getData().shortname + " has been moved to #" + row.getPosition());
            if (this.getHistoryUndoSize() > 0) {
                document.getElementById("undo").disabled = false;
            }
        },
        dataChanged: function (data) {
            //data - the updated table data
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
};

function saveComplete(data, textStatus, jqXHR) {
    message = "";
    //console.log(data);
    try {
        fetch_json = JSON.parse(data);
    } catch (error) {
        //console.log(error);
        fetch_json = {};
    }
    //console.log(fetch_json);
    if (fetch_json.hasOwnProperty('message')) {
        message = fetch_json.message;
        //console.log(message);
    }       

    if (fetch_json.hasOwnProperty('tableschema')) {
        tableschema = fetch_json.tableschema;
    } else {
        tableschema = null;
        message += '<br/>Error: no schema returned for this table';
    }

    proceed = true;
    if (message != "") {
        el = document.getElementById("message");
        if (message.indexOf("Error:") >= 0) {
            el.class = "alert alert-danger mt-4";
            proceed = false;
        } else if (message.indexOf("Warning:") >= 0) {
            el.class = "alert alert-danger mt-4";
            proceed = false;
        } else {
            el.class = "alert alert-success mt-4";
        }
        el.innerHTML = fetch_json.message;;
        el.style.display = 'block';
    }

    if (proceed && fetch_json.hasOwnProperty('tabledata')) {
        //console.log(tabledata);
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
    document.getElementById("redo").disabled = true;
    document.getElementById("undo").disabled = true;
};

function SaveTable() {
    //rows = table.getDataCount();
    //console.log("there are " + rows + " questions in the survey");
    //console.log("sorters: ");
    //console.log(table.getSorters());
    //for (r = 0; r < rows; r++) {
    //    row = table.getRowFromPosition(r);
    //    console.log("q[" + r + "] = " + row.getCell("shortname").getValue() + ": q" + row.getCell("questionid").getValue() +
    //        ", d" + row.getCell("display_order").getValue());
    //}
    //return false;
    document.getElementById("saving_div").style.display = "block";
    document.getElementById("submitbtn").disabled = true;
    document.getElementById("message").style.display = 'none';

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
        type: "POST"
    }); 
};

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
        type: "POST"
    });
};

function Undo() {
    table.undo();

    var undoCount = table.getHistoryUndoSize();
    if (undoCount <= 0) {
        document.getElementById("undo").disabled = true;
    }
    var redoCount = table.getHistoryRedoSize();
    if (redoCount > 0) {
        document.getElementById("redo").disabled = false;
    }
};

function Redo() {
    table.redo();

    var undoCount = table.getHistoryUndoSize();
    if (undoCount > 0) {
        document.getElementById("undo").disabled = false;
    }
    var redoCount = table.getHistoryRedoSize();
    if (redoCount <= 0) {
        document.getElementById("redo").disabled = true;
    }
};