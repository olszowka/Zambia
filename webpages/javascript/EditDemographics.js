// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
var configtable;

var EditDemographics = function () {
    var newid = -1;
    var curid = -99999;

    function addupdaterow(table) {
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
        var minvalue = document.getElementById("min_value").value;
        var maxvalue = document.getElementById("max_value").value;

        if (curid == -99999) {
            curid = newid;
        }
        table.updateOrAddData([{
            demographicid: curid, shortname: shortname, description: description, prompt: prompt, hover: hover,
            typeid: typeid, typename: typename, required: required, publish: publish, privacy_user: privacy_user, searchable: searchable,
            ascending: ascending, min_value: minvalue, max_value: maxvalue
           }, 
        ]);
        newid = newid - 1;
        curid = -99999;
        document.getElementById("general-demo-div").style.display = "none";
    };

    function addnewdemo(demotable) {
        curid = -99999;
        document.getElementById("general-header").innerHTML = '<h3 class="col-auto">New Demographic - General Configuration</h3>';
        document.getElementById("add-row").innerHTML = "Add Demographic";
        // Default values
        document.getElementById("shortname").value = "";
        document.getElementById("description").value = "";
        document.getElementById("prompt").value = "";
        document.getElementById("hover").value = "";
        document.getElementById("typename").value = 'single-radio';
        document.getElementById("required-1").checked = true;
        document.getElementById("required-0").checked = false;
        document.getElementById("publish-1").checked = false;
        document.getElementById("publish-0").checked = true;
        document.getElementById("privacy_user-1").checked = false;
        document.getElementById("privacy_user-0").checked = true;
        document.getElementById("searchable-1").checked = false;
        document.getElementById("searchable-0").checked = true;
        document.getElementById("ascending-1").checked = true;
        document.getElementById("ascending-0").checked = false;
        document.getElementById("min_value").value = "";
        document.getElementById("min_value").value = "";
        // now display it, hiding ones not used by single-radio
        document.getElementById("asc_desc").style.display = "none";
        document.getElementById("value_range").style.display = "none";
        document.getElementById("general-demo-div").style.display = "block";
    }

    function editconfig(e, row, demotable) {
        var name = row.getCell("shortname").getValue();
        document.getElementById("general-header").innerHTML = '<h3 class="col-auto">' + name + ' - General Configuration</h3>';

        // Set up current value for all row items
        curid = row.getCell("demographicid").getValue();
        document.getElementById("shortname").value = name;
        document.getElementById("description").value = row.getCell("description").getValue();
        document.getElementById("prompt").value = row.getCell("prompt").getValue();
        document.getElementById("hover").value = row.getCell("hover").getValue();
        document.getElementById("typename").value = row.getCell("typename").getValue();
        document.getElementById("required-1").checked = row.getCell("required").getValue() == "1";
        document.getElementById("required-0").checked = row.getCell("required").getValue() != "1";
        document.getElementById("publish-1").checked = row.getCell("publish").getValue() == "1";
        document.getElementById("publish-0").checked = row.getCell("publish").getValue() != "1";
        document.getElementById("privacy_user-1").checked = row.getCell("privacy_user").getValue() == "1";
        document.getElementById("privacy_user-0").checked = row.getCell("privacy_user").getValue() != "1";
        document.getElementById("searchable-1").checked = row.getCell("searchable").getValue() == "1";
        document.getElementById("searchable-0").checked = row.getCell("searchable").getValue() != "1";
        document.getElementById("ascending-1").checked = row.getCell("ascending").getValue() == "1";
        document.getElementById("ascending-0").checked = row.getCell("ascending").getValue() != "1";
        document.getElementById("min_value").value = row.getCell("min_value").getValue();
        document.getElementById("min_value").value = row.getCell("max_value").getValue();

        // now show the block
        edit_typechange(demotable);
        document.getElementById("add-row").innerHTML = "Update Demographic";
        document.getElementById("general-demo-div").style.display = "block";
    }

    function edit_typechange(datatable) {
        var typename = document.getElementById("typename").value;
        var show_asc_desc = false;
        var show_range = false;
        switch (typename) {
            case 'numberselect':
                show_asc_desc = true;
                show_range = true;
                break;
            case 'number':
                show_range = true;
                break;
            case 'monthnum':
                show_asc_desc = true;
                break;
            case 'monthyear':
                show_asc_desc = true;
                show_range = true;
                break;
            case 'country':
                show_asc_desc = true;
                break;
            case 'states':
                show_asc_desc = true;
                break;         
        };
        document.getElementById("asc_desc").style.display = show_asc_desc ? 'block' : 'none';
        document.getElementById("value_range").style.display = show_range ? 'block' : 'none';
    }

    function deleteicon(cell, formattParams, onRendered) {
        return "&#x1F5D1;";
    }
    function deleteDemographic(e, row, demotable) {
        row.delete();
    }

   this.initialize = function () {
        //called when EditDemographics page has loaded

        configtable = new Tabulator("#demographicsconfig", {
            maxHeight: "250px",
            movableRows: true,
            tooltips: false,
            headerSort: false, 
            data: demographics,
            index: "demographicid",
            layout: "fitDataTable",
            //rowClick: function (e, row) {
            //    if e.
            //    editconfig(e, row, configtable);
            //},
            columns: [
                { rowHandle: true, formatter: "handle", frozen: true, width: 30, minWidth: 30 },
                { title: "ID", field: "demographicid" },
                {
                    title: "Name", field: "shortname",
                    editor: "input",
                    editorParams: { editorAttributes: { maxlength: 100 } },
                    cellClick: function (e, cell) {
                        editconfig(e, cell.getRow(), configtable);
                    },
                },
                { title: "Description", field: "description", formatter:"textarea"},
                { title: "Prompt", field: "prompt", editor: "input", editorParams: { editorAttributes: { maxlength: 512 } } },
                { title: "Hover Text", field: "hover", editor: "input", editorParams: { editorAttributes: { maxlength: 8192 } }},
                { title: "Type", field: "typename" },
                { title: "Type-ID", field: "typeid" },
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
                { title: "Min Value", field: "min_value", editor:"number" },
                { title: "Max Value", field: "max_value", editor: "number" },
                {
                    title: "Delete", formatter: deleteicon, hozAlign: "center",
                    cellClick: function (e, cell) {
                        deleteDemographic(e, cell.getRow(), configtable);
                    },
                },
            ],
            rowMoved: function (row) {
                console.log("Row: " + row.getData().name + " has been moved");
            },
            tooltips: function (cell) {
                //cell - cell component

                //function should return a string for the tooltip of false to hide the tooltip
                //console.log(cell.getField());
                if (cell.getField() != "shortname") { return false };
                return cell.getData().description;
            },
        });
        configtable.hideColumn("demographicid");
        configtable.hideColumn("typeid");
        configtable.hideColumn("description");
        var addnewrowbut = document.getElementById("add-row");
        addnewrowbut.addEventListener('click', function () { addupdaterow(configtable); });
        var addnewbut = document.getElementById("add-demo");
        addnewbut.addEventListener('click', function () { addnewdemo(configtable); });
        document.getElementById("typename").onchange = function () { edit_typechange(configtable); };
        };

};

var editDemographics = new EditDemographics();

function saveComplete(data, textStatus, jqXHR) {
    configtable.replaceData(demographics);
    document.getElementById("saving_div").style.display = "none";
    document.getElementById("submitbtn").disabled = false;
};

function SaveDemographics() {
    document.getElementById("saving_div").style.display = "block";
    document.getElementById("submitbtn").disabled = true;
    var postdata = {
        ajax_request_action: "update_demographics",
        demographics: JSON.stringify(configtable.getData())
    };
    $.ajax({
        url: "SubmitEditDemographics.php",
        dataType: "html",
        data: postdata,
        success: saveComplete,
        type: "POST"
    }); 
};

function FetchDemographics() {
    var postdata = {
        ajax_request_action: "fetch_demographics"
    };
    document.getElementById("general-demo-div").style.display = "none";
    $.ajax({
        url: "SubmitEditDemographics.php",
        dataType: "html",
        data: postdata,
        success: saveComplete,
        type: "POST"
    });
};