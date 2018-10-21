<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$title = "Generate Report Files";
require_once('StaffCommonCode.php');
$timeLimitSuccess = set_time_limit(600);
if (!$timeLimitSuccess) {
    RenderError("Error extending time limit.");
    exit();
}
$query =<<<EOD
SELECT
        reporttypeid, title, description, filename, xsl, display_order
    FROM
        ReportTypes
    WHERE
        oldmechanism = 0;
EOD;
if (!$result = mysqli_query_with_error_handling($query, true)) {
    exit(); // should have exited already
};
$reportTypes = [];
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $reportTypes[$row['reporttypeid']] = [
        'name' => $row['title'],
        'description' => $row['description'],
        'filename' => $row['filename'],
        'xsl' => $row['xsl'],
        'display_order' => is_null($row['display_order']) ? 1 : $row['display_order'],
        'queries' => [],
        'categories' => [],
    ];
}
mysqli_free_result($result);
$query =<<<EOD
SELECT
        RQ.reporttypeid, RQ.queryname, RQ.query
    FROM
             ReportTypes RT
        JOIN ReportQueries RQ USING (reporttypeid)
    WHERE
        RT.oldmechanism = 0;
EOD;
if (!$result = mysqli_query_with_error_handling($query, true)) {
    exit(); // should have exited already
};
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $reportTypes[$row['reporttypeid']]['queries'][$row['queryname']] = $row['query'];
}
mysqli_free_result($result);
$query =<<<EOD
SELECT
        CHR.reporttypeid, RC.description
    FROM
             ReportTypes RT
        JOIN CategoryHasReport CHR USING(reporttypeid)
        JOIN ReportCategories RC USING(reportcategoryid)
    WHERE
        RT.oldmechanism = 0;
EOD;
if (!$result = mysqli_query_with_error_handling($query, true)) {
    exit(); // should have exited already
};
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $reportTypes[$row['reporttypeid']]['categories'][$row['description']] = $reportTypes[$row['reporttypeid']]['display_order'];
}
mysqli_free_result($result);
foreach ($reportTypes as $report) {
    $filename = 'reports/' . preg_replace('/report/i', '', $report['filename']);
    if (!$file = fopen($filename, 'w')) {
        RenderError("Error opening output file.");
        exit(0);
    }
    fwrite($file, "<?php\n");
    fwrite($file, "// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.\n");
    fwrite($file, "\$report = [];\n");
    fwrite($file, "\$report['name'] = '{$report['name']}';\n");
    fwrite($file, "\$report['description'] = '{$report['description']}';\n");
    fwrite($file, "\$report['categories'] = array(\n");
    foreach ($report['categories'] as $category => $display_order) {
        fwrite($file, "    '$category' => $display_order,\n");
    }
    fwrite($file, ");\n");
    fwrite($file, "\$report['queries'] = [];\n");
    foreach ($report['queries'] as $queryName => $query) {
        fwrite($file, "\$report['queries']['$queryName'] =<<<'EOD'\n");
        if (substr($query, -1) !== "\n") {
            $query .= "\n";
        }
        fwrite($file, $query);
        fwrite($file, "EOD;\n");
    }
    fwrite($file, "\$report['xsl'] =<<<'EOD'\n");
    if (substr($report['xsl'], -1) !== "\n") {
        $report['xsl'] .= "\n";
    }
    fwrite($file, $report['xsl']);
    fwrite($file, "EOD;\n");
    fclose($file);
}
echo "Success.\n";