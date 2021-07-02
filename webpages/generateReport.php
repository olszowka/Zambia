<?php
// Copyright (c) 2018-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
$title = "Run Report";

function render_report_to_html($report, $resultXML) {
    //echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
    $xsl = new DomDocument;
    $xsl->loadXML($report['xsl']);
    $xslt = new XsltProcessor();
    $xslt->importStylesheet($xsl);
    if (isset($report['params'])) {
        foreach ($report['params'] as $paramName => $paramValue) {
            $xslt->setParameter('', $paramName, $paramValue);
        }
    }
    return $xslt->transformToXML($resultXML);
}



require_once('StaffCommonCode.php');
$reportName = getString("reportName");
if ($reportName == '') {
    $message_error = "Required parameter reportName missing or invalid.";
    RenderError($message_error);
    exit();
}
require("reports/$reportName");
foreach ($report['queries'] as $queryName => $query) {
    $report['queries'][$queryName] = str_replace('$ConStartDatim$',CON_START_DATIM, $query);
}
if (isset($report['csv_output']) && $report['csv_output'] == true) {
    if (isset($report['group_concat_expand']) && $report['group_concat_expand'] == true) {
        $query = "SET group_concat_max_len=25000";
        if (!$result = mysqli_query_exit_on_error($query)) {
            exit(); // should have exited already
        }
    }
    require_once('csv_report_functions.php');
    global $title;
    $title = $report['name'];
    if (!$result = mysqli_query_exit_on_error($report['queries']['master'])) {
        exit(); // should have exited already
    }
    echo_if_zero_rows_and_exit($result);
    header("Content-disposition: attachment; filename={$report['output_filename']}");
    header('Content-type: text/csv');
    echo $report['column_headings']."\n";
    render_query_result_as_csv($result);
} else {
    if (($resultXML = mysql_query_XML($report['queries'])) === false) {
        RenderError($message_error);
        exit();
    }
    $is_multi_report = array_key_exists("multi", $report);
    $_SESSION['return_to_page'] = "generateReport.php?reportName=$reportName";
    $reportColumns = isset($report['columns']) ? $report['columns'] : false;
    $reportAdditionalOptions = isset($report['additionalOptions']) ? $report['additionalOptions'] : false;
    if ($is_multi_report) {
        $download = $is_multi_report ? getString("download") == 'csv' : false;
        $html = render_report_to_html($report, $resultXML);
        if (!$download) {
            staff_header("Reports", true, true, $reportColumns, $reportAdditionalOptions);
            $reportDescription = htmlspecialchars(str_replace('$CON_NAME', CON_NAME, $report['description']), ENT_NOQUOTES);
?>
    <div class="card mt-2">
        <div class="card-header">
            <div class="row">
                <div class="col-md-10">
                    <h5 class="mb-0"><?php echo $report['name'] ?></h5>
                    <div>
                        <small class="text-muted">Generated: <?php echo date("D M j G:i:s T Y"); ?></small>
                    </div>
                </div>
                <div class="col-md-2">
                    <p class="text-right"><a class="btn btn-secondary btn-sm" href="<?php echo "generateReport.php?reportName=".$reportName."&download=csv"; ?>">Download CSV</a></p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <p><?php echo $reportDescription; ?></p>
<?php
            // some browsers do not support empty div, iframe, script and textarea tags
            echo(mb_ereg_replace("<(div|span|b|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
?>

        </div>
    </div>
<?php
            staff_footer();
        } else {
            require_once('csv_report_functions.php');
            $filename = array_key_exists('output_filename', $report) ? $report['output_filename'] : 'report';
            if (strpos($filename, ".csv") !== false) {
                $filename = str_replace(".csv", "", $filename);
            }
            header("Content-disposition: attachment; filename={$filename}-".date(DATE_ISO8601).".csv");
            header('Content-type: text/csv');
        
            render_html_table_as_csv($html);
        }
    } else {
        staff_header($report['name'], false, true, $reportColumns, $reportAdditionalOptions);
        if (!$download) {
            $reportDescription = htmlspecialchars(str_replace('$CON_NAME', CON_NAME, $report['description']), ENT_NOQUOTES);
            echo "<div class=\"alert alert-info\">$reportDescription</div>\n";
            echo "<p class=\"text-success center\"> Generated: " . date("D M j G:i:s T Y") . "</p>\n";
        }
        $html = render_report_to_html($report, $resultXML);
        // some browsers do not support empty div, iframe, script and textarea tags
        echo(mb_ereg_replace("<(div|span|b|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
        // echo "<div pbo=\"{$report['columns']}\"></div>\n";
        staff_footer();
    }
}
?>
