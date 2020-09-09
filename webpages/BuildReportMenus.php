<?php
// Copyright (c) 2019 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Build Report Menus";
require_once('StaffCommonCode.php');
if (!may_I('ConfigureReports')) {
    $message_error = "You do not currently have permission to view this page.<br>\n";
    RenderError($message_error);
    exit();
}
$areYouSure = getInt("areYouSure");
if ($areYouSure !== 1) {
    staff_header($title);
?>
<p class="alert alert-error">
    Rebuild all report menus.  Are you sure?
</p>
<form class="form-inline" name="confform" method="GET" action="BuildReportMenus.php">
    <input type="hidden" name="areYouSure" value="1" />
    <button type="submit" class="btn btn-success">Continue</button>
    <a class="btn btn-default" href="StaffPage.php">Cancel</a>
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
$staffReportsICIFilHand = fopen('staffReportsInCategoryInclude.php', 'wb');
fwrite($staffReportsICIFilHand, "<?php\n");
fwrite($staffReportsICIFilHand, "\$reportCategories = array();\n");
foreach ($reportCategories as $reportCategory => $reportCategoryArray) {
    $encodedReportCategory = htmlentities(urlencode($reportCategory));
    fwrite($reportMenuFilHand, "<li><a href='staffReportsInCategory.php?reportcategory=$encodedReportCategory'>$reportCategory</a></li>\n");
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
fclose($staffReportsICIFilHand);
$reportCount = count($allReports);
staff_header($title);
echo "<p class='alert alert-success'>Done.<br>$reportCount report(s) processed.</p>";
staff_footer();
