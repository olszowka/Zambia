<?php
// Copyright (c) 2015-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
$title = "Reports in Category";
require_once('StaffCommonCode.php');
$CON_NAME = CON_NAME;
$reportcategoryid = getString("reportcategory");
if ($reportcategoryid === null)
    $reportcategoryid = "";

$prevErrorLevel = error_reporting();
$tempErrorLevel = $prevErrorLevel & ~ E_WARNING;
error_reporting($tempErrorLevel);
$includeFile = 'staffReportsInCategoryInclude.php';
if (!include $includeFile) {
    $message_error = "Report menus not built.  File $includeFile not found.";
    RenderError($message_error);
    exit();
}
error_reporting($prevErrorLevel);
if ($reportcategoryid !== "" && !isset($reportCategories[$reportcategoryid])) {
    $message_error = "Report category $reportcategoryid not found or category has no reports.";
    RenderError($message_error);
    exit();
}
staff_header($title);
echo "<dl>\n";
if ($reportcategoryid === "") {
    foreach ($reportNames as $reportFileName => $reportName) {
        echo "<dt><a href='generateReport.php?reportName=$reportFileName'>$reportName</a></dt>\n";
        echo "<dd>{$reportDescriptions[$reportFileName]}</dd>";
    }
} else {
    foreach ($reportCategories[$reportcategoryid] as $reportFileName) {
        echo "<dt><a href='generateReport.php?reportName=$reportFileName'>$reportNames[$reportFileName]</a></dt>\n";
        echo "<dd>{$reportDescriptions[$reportFileName]}</dd>";
    }
}
echo "</dl>\n";
staff_footer();
?>
