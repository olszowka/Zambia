<?php
// Copyright (c) 2015-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
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
staff_header($title, true);
?>
<div class="container">
    <div class="row mt-2">
        <div class=" col-md-9">
            <div class="list-group">
<?php 
if ($reportcategoryid === "") {
    foreach ($reportNames as $reportFileName => $reportName) {
        echo "<div class='list-group-item flex-column align-items-start'>\n<h5><a  href='generateReport.php?reportName=$reportFileName'>$reportName</a></h5>\n";
        echo "<div>{$reportDescriptions[$reportFileName]}</div>";
        echo "</div>";
    }
} else {
    foreach ($reportCategories[$reportcategoryid] as $reportFileName) {
        echo "<div class='list-group-item flex-column align-items-start'>\n<h5><a href='generateReport.php?reportName=$reportFileName'>$reportNames[$reportFileName]</a></h5>\n";
        echo "<div>{$reportDescriptions[$reportFileName]}</div>";
        echo "</div>";
    }
}
?>
            </div>
        </div>
    </div>
</div>
<?php 
staff_footer();
?>
