//  Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.


function loadDataTables() {
    const reportTable = document.getElementById('reportTable');
    if (reportTable) {
        const options = {
            "autoWidth": false,
            "pageLength": -1,
            "lengthMenu": [ [25, 50, 100, 200, -1], [25, 50, 100, 200, "All"] ]
        };
        const columnsJson = document.getElementById('reportColumns')?.dataset?.reportColumns;
        if (columnsJson) {
            options.columns = JSON.parse(columnsJson);
        }
        const additionalOptionsJson = document.getElementById('reportAdditionalOptions')?.dataset?.reportAdditionalOptions;
        if (additionalOptionsJson) {
            Object.assign(options, JSON.parse(additionalOptionsJson));
        }
        new DataTable(reportTable, options);
    }
}

function ready(fn) {
    if (document.readyState !== "loading") {
        fn();
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

ready(loadDataTables);
