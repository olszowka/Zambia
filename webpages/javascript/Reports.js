//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
$(document).on("ready", function() {
    var $reportTable = $("#reportTable");
    if ($reportTable.length === 1) {
        var options = {
            "autoWidth": false,
            "pageLength": -1,
            "lengthMenu": [ [25, 50, 100, 200, -1], [25, 50, 100, 200, "All"] ]
        };
        var columns = $reportTable.data("columns");
        if (columns) {
            options.columns = columns;
        }
        $reportTable.DataTable(options);
    }
});



