<?php
// Copyright (c) 2019-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Build Report Menus";


function process_all_files_in_directory($basePath, $subDirectoryName, &$allReports) {
    $path = $basePath . "/" . $subDirectoryName;
    $dirHandle = opendir($path);
    if (!$dirHandle) {
        throw new Exception("Cannot open directory: $path");
    }
    try {
        while (false !== ($reportFileName = readdir($dirHandle))) {
            if ($reportFileName == "." || $reportFileName == "..") {
                // do nothing
            } else if (is_dir("$path/$reportFileName")) {
                try {
                    $nextDirectory = $subDirectoryName == "" ? $reportFileName : ($subDirectoryName . "/" . $reportFileName);
                    process_all_files_in_directory($basePath, $nextDirectory, $allReports);
                } catch (Exception $e) {
                    // ignore errors opening subdirectories? Is this really what we want, here?
                    // It's closest to the existing behaviour.
                }
            } else if (!mb_ereg_match(".*\\.php$", $reportFileName)) {
                // do nothing
            } else {
                include ("$path/$reportFileName");
                if (isset($report)) {
                    // preserve only data needed for menu generation
                    $key = $subDirectoryName == "" ? $reportFileName : ($subDirectoryName . '/' . $reportFileName);
                    $allReports[$key] = array('name' => $report['name'], 'description' => $report['description'], 'categories' => $report['categories']);
                    unset($report);
                } else {
                    error_log("cannot worh with : $path/$reportFileName");
                }
            }
        }
    } finally {
        closedir($dirHandle);
    }
}

function build_report_menus($path) {
    $allReports = array();
    process_all_files_in_directory($path, "", $allReports);
    $reportCategories = array();
    foreach ($allReports as $reportName => $reportData) {
        if (isset($reportData['categories'])) {
            foreach ($reportData['categories'] as $category => $sortOrder) {
                if (!isset($reportCategories[$category])) {
                    $reportCategories[$category] = array();
                }
                $reportCategories[$category][$reportName] = $sortOrder;
            }
        }
    }
    ksort($reportCategories, SORT_NATURAL);
    $reportMenuFilHand = fopen('ReportMenuInclude.php', 'wb');
    $reportMenuBS4FilHand = fopen('ReportMenuBS4Include.php', 'wb');
    $staffReportsICIFilHand = fopen('staffReportsInCategoryInclude.php', 'wb');
    if ($reportMenuFilHand === false || $reportMenuBS4FilHand === false || $staffReportsICIFilHand === false) {
        throw new Exception("One of the files we're trying to generate cannot be opened.");
    }
    fwrite($staffReportsICIFilHand, "<?php\n");
    fwrite($staffReportsICIFilHand, "\$reportCategories = array();\n");
    foreach ($reportCategories as $reportCategory => $reportCategoryArray) {
        $encodedReportCategory = htmlentities(urlencode($reportCategory));
        fwrite($reportMenuFilHand, "<li><a href='staffReportsInCategory.php?reportcategory=$encodedReportCategory'>$reportCategory</a></li>\n");
        fwrite($reportMenuBS4FilHand, "<a class='dropdown-item' href='staffReportsInCategory.php?reportcategory=$encodedReportCategory'>$reportCategory</a>\n");
        fwrite($staffReportsICIFilHand, "\$reportCategories['$reportCategory'] = array(");
        asort($reportCategoryArray, SORT_NUMERIC);
        $notFirst = false;
        foreach ($reportCategoryArray as $reportName => $sortOrder) {
            if ($notFirst) {
                fwrite($staffReportsICIFilHand, ',');
            }
            fwrite($staffReportsICIFilHand, "'$reportName'");
            $notFirst = true;
        }
        fwrite($staffReportsICIFilHand, ");\n");
    }
    fwrite($staffReportsICIFilHand, "\$reportNames = array();\n");
    foreach($allReports as $reportName => $reportArray) {
        fwrite($staffReportsICIFilHand, "\$reportNames['$reportName'] = '{$reportArray['name']}';\n");
    }
    fwrite($staffReportsICIFilHand, "\$reportDescriptions = array();\n");
    foreach($allReports as $reportName => $reportArray) {
        $description = addslashes($reportArray['description']);
        fwrite($staffReportsICIFilHand, "\$reportDescriptions['$reportName'] = \"{$description}\";\n");
    }
    fclose($reportMenuFilHand);
    fclose($reportMenuBS4FilHand);
    fclose($staffReportsICIFilHand);

    return $allReports;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    require_once('StaffCommonCode.php'); // Checks for staff permission among other things
    if (!may_I('ConfigureReports')) {
        $message_error = "You do not currently have permission to view this page.<br>\n";
        StaffRenderErrorPage($title, $message_error, true);
        exit();
    }
    $areYouSure = getInt("areYouSure");
    if ($areYouSure !== 1) {
        staff_header($title, true);
    ?>
    <div class="container">
        <form name="confform" method="POST" action="BuildReportMenus.php">
            <div class="card mt-3">
                <div class="card-header">
                    <h2>Rebuild Report Menus</h2>
                </div>
                <div class="card-body">
                    <p class="text-danger">This Admin tool will overwrite your current list of reports and generate a new list.</p>
                    <p>Please be sure that you know what you're doing before you hit 'Continue'.</p>
                </div>
                <div class="card-footer text-right">
                    <input type="hidden" name="areYouSure" value="1" />
                    <a class="btn btn-link text-muted" href="StaffPage.php">Cancel</a>
                    <button id="build-report-btn" type="button" class="btn btn-danger mr-3"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span> Continue</button>
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript" src="./javascript/BuildReportMenus.js"></script>
        <?php
        staff_footer();
        exit();
    }
} else {
    $returnAjaxErrors = true;
    require_once('StaffCommonCode.php'); // Checks for staff permission among other things
    if (!may_I('ConfigureReports')) {
        $result = array("severity" => "danger", "text" => "You do not have access to perform this function. Perhaps your session timed out?");
        http_response_code(401);
        header('Content-type: application/json');
        echo "\n\n";
        echo json_encode($result);
    } else {

        $path = './reports';
        try {
            $allReports = build_report_menus($path);
            $reportCount = count($allReports);
            header('Content-type: application/json');
            echo "\n\n";
            $result = array("severity" => "success", "text" => "Done. $reportCount report(s) processed.");
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(409);
            header('Content-type: application/json');
            echo "\n\n";
            $result = array("severity" => "danger", "text" => "We've encountered an error.Check the file system permissions on your server?");
            echo json_encode($result);
        }
    }
}
?>