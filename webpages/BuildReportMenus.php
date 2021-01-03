<?php
// Copyright (c) 2019-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Build Report Menus";
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
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            Rebuild all report menus.  Are you sure?
        </div>
    </div>
</div>
<form class="form-inline" name="confform" method="GET" action="BuildReportMenus.php">
    <input type="hidden" name="areYouSure" value="1" />
    <button type="submit" class="btn btn-primary mr-3">Continue</button>
    <a class="btn btn-secondary" href="StaffPage.php">Cancel</a>
</form>
    <?php
    staff_footer();
    exit();
}
?>
<?php
$path = './reports';
$dirHandle = opendir($path);
if (!$dirHandle) {
    $message_error = "Directory $path not found.";
    RenderError($message_error);
    exit();
}
$allReports = array();
while (false !== ($reportFileName = readdir($dirHandle))) {
    if ($reportFileName == "." || $reportFileName == ".." ||
        is_dir("./reports/$reportFileName") ||
        !mb_ereg_match(".*\\.php$", $reportFileName)
    ) {
        continue;
    }
    include ("./reports/$reportFileName");
    if (isset($report)) {
        // preserve only data needed for menu generation
        $allReports[$reportFileName] = array('name' => $report['name'], 'description' => $report['description'], 'categories' => $report['categories']);
        unset($report);
    }
}
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
$reportCount = count($allReports);
staff_header($title, true);
?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-success" role="alert">
            Done.<br />
            <?php echo $reportCount; ?> report(s) processed.
        </div>
    </div>
</div>
<?php
staff_footer();
