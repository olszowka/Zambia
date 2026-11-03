// Copyright (c) 2015-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
var panelInterests = new PanelInterests;

function PanelInterests() {
    this.checkDirty = checkDirty;
    this.dismissAutosaveWarn = dismissAutosaveWarn;
    this.doAutosave = doAutosave;
    this.initialize = initialize;
    this.onClickAdd = onClickAdd;
    this.setDirty = setDirty;
    this.showAutosaveDialog = showAutosaveDialog;

    this.pageDirty = false;
    this.timeoutID = null;
    this.addButDirtyModal = null;
    this.autosaveModal = null;

    function checkDirty(event) {
        if (panelInterests.pageDirty)
            return true;
        var target = $(event.target);
        if (target.attr("type") == "checkbox") {
            if (target.prop("checked") != target.prop("defaultChecked"))
                panelInterests.setDirty(false); // regular timeout
            return true;
        }
        if (target.prop("defaultValue") != target.val())
            panelInterests.setDirty(false); // regular timeout
        return true;
    }

    function dismissAutosaveWarn() {
        panelInterests.setDirty(true); // short timeout
        panelInterests.autosaveModal.hide();
    }

    function doAutosave() {
        $("#autosaveHID").val("1");
        $("#sessionFRM").get(0).submit();
    }

    function initialize() {
        // on page load, i.e. $(document).ready()
        panelInterests.addButDirtyModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("addButDirtyMOD"));
        panelInterests.autosaveModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("autosaveMOD"));
        $("div.interest-edit-row :checkbox").on("click", panelInterests.checkDirty);
        $("div.interest-edit-row input[type='text']").on("keyup", panelInterests.checkDirty);
        $("div.interest-edit-row textarea").on("keyup", panelInterests.checkDirty);
        if ($("#pageIsDirty").val() == "true")
            panelInterests.setDirty(true); // short timeout
    }

    function onClickAdd() {
        if (panelInterests.pageDirty)
            panelInterests.addButDirtyModal.show();
        else
            $("#addFRM").get(0).submit();
    }

    function setDirty(short) {
        panelInterests.pageDirty = true;
        if (short)
            var timeout = 90000; // 1:30 for short
        else
            var timeout = 600000; // 10:00 for long
        panelInterests.timeoutID = window.setTimeout(panelInterests.showAutosaveDialog, timeout);
    }

    function showAutosaveDialog() {
        panelInterests.autosaveModal.show();
    }

}
