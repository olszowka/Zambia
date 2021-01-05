// Created by Syd Weinstein on 2021-01-04;
// Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
var table = null;
var tablename = '';
var message = "";
var previewmce = false;
var indexcol = 'display_order';

var EditConfigTable = function () {
    var newid = -1;
    var curid = -99999;

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

    function addupdaterow(table) {
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

        table.updateOrAddData([{
            questionid: curid, shortname: shortname, description: description, prompt: prompt, hover: hover,
            typeid: typeid, typename: typename, required: required, publish: publish, privacy_user: privacy_user,
            searchable: searchable, ascending: ascending, display_only: display_only, min_value: minvalue, max_value: maxvalue, options: btoa(option)
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
    };

    function tabshown(tabname) {   
        if (tabname.substring(0, 2) != 't-')
            document.getElementById("table-div").style.display = "none";
        else {
            document.getElementById("table-div").style.display = "block";
            tablename = tabname.substring(2);
            console.log('new table: ' + tablename);
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

function deleteicon(cell, formattParams, onRendered) {
    return "&#x1F5D1;";
}
function deleterow(e, row, questiontable) {
    document.getElementById("message").style.display = 'none';
    row.delete();
}

function addnewrow(table) {
    newid = newid - 1;
    table.addRow({ ordinal: newid }, false);
}

function opentable() {
    // get table information from tableschema
    if (!tableschema) {
        message = message + '<br/>Error: no schema returned for this table';
        return false;
    }
    console.log(tableschema);
    columns = new Array();
    indexcol = 'display_order';
    displayorder_found = false;
    initialsort = new Array();
    columns.push({ rowHandle: true, formatter: "handle", frozen: true, width: 30, minWidth: 30 });
    tableschema.forEach(function (column)  {
        if (column.COLUMN_KEY == 'PRI') {
            indexcol = column.COLUMN_NAME;
            initialsort.push({ column: column.COLUMN_NAME, dir: "asc" });
            visible = (!(column.COLUMN_NAME.match(/^id/i) || column.COLUMN_NAME.match(/id$/i)) || tablename == "RoomHasSet");
            if (visible) {
                columns.push({
                    title: column.COLUMN_NAME, field: column.COLUMN_NAME,
                    visible: true,
                    editor: "input",
                    editorParams: { editorAttributes: { maxlength: column.CHARACTER_MAXIMUM_LENGTH } }
                });
            } else {
                columns.push({
                    title: column.COLUMN_NAME, field: column.COLUMN_NAME,
                    visible: false
                });
            }
        } else if (column.COLUMN_NAME == 'display_order') {
            columns.push({ title: "Order", field: "display_order", visible: false });
            display_order = true;
        }
        else {
            if (column.DATA_TYPE == 'int')
                columns.push({ title: column.COLUMN_NAME, field: column.COLUMN_NAME, editor: "number", minWidth: 50, hozAlign: "right" });
            else {
                width = 8 * column.CHARACTER_MAXIMUM_LENGTH;
                if (width < 80) width = 80;
                if (width > 500) width = 500;
                columns.push({
                    title: column.COLUMN_NAME, field: column.COLUMN_NAME, editor: "input", width: width,
                    editorParams: { editorAttributes: { maxlength: column.CHARACTER_MAXIMUM_LENGTH } }
                });
            }
        }
    });
    columns.push({
        title: "Delete", formatter: deleteicon, hozAlign: "center",
        cellClick: function (e, cell) {
            deleterow(e, cell.getRow(), table);
        }
    });
    console.log(columns);
    
    if (displayorder_found) {
        initialsort = new Array();
        initialsort.push({ column: "display_order", dir: "asc" });
    }
    document.getElementById("table-div").style.display = "block";
    table = new Tabulator("#table", {
        maxHeight: "250px",
        movableRows: true,
        tooltips: false,
        history: true,
        headerSort: false,
        initialSort: initialsort,  
        data: tabledata,
        index: indexcol,
        layout: "fitDataTable",
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
    //var addnewrowbut = document.getElementById("add-row");
    //addnewrowbut.addEventListener('click', function () { addnewrow(table); });
    //console.log("Setting up options in table");
    document.getElementById("submitbtn").innerHTML = "Save";
    table.clearHistory();
};

function saveComplete(data, textStatus, jqXHR) {
    var match = "tabledata = ";
    var match2 = "message = ";
    message = "";
    //console.log(data);
    if (data.substring(0, match.length) == match || data.substring(0, match2.length) == match2) {
        eval(data);
        //console.log(tabledata);
        if (table) {
            table.replaceData(tabledata);
        }
        else {
            opentable();
        }
    }
    document.getElementById("saving_div").style.display = "none";
    el = document.getElementById("submitbtn");
    el.disabled = false;
    el.innerHTML = "Save";
    document.getElementById("redo").disabled = true;
    document.getElementById("undo").disabled = true;
    if (message != "") {
        document.getElementById("message").innerHTML = message;
        document.getElementById("message").style.display = 'block';
    }
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
        tablename: tablename
    };
    $.ajax({
        url: "SubmitEditSurvey.php",
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