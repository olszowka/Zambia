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
    <div class="card mt-2">
        <div class="card-header">
            <h2>
<?php
            if ($reportcategoryid === "") {
?>
                All Reports
<?php
            } else {
                echo $reportcategoryid;
            }
?>
            </h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class=" col-md-9">
                    <div class="list-group">
<?php 
if ($reportcategoryid === "") {
    foreach ($reportNames as $reportFileName => $reportName) {
        echo "<a class='list-group-item list-group-item-action flex-column align-items-start' href='generateReport.php?reportName=$reportFileName'>\n<h5>$reportName</h5>\n";
        echo "<div>{$reportDescriptions[$reportFileName]}</div>";
        echo "</a>";
    }
} else {
    foreach ($reportCategories[$reportcategoryid] as $reportFileName) {
        echo "<a class='list-group-item list-group-item-action flex-column align-items-start' href='generateReport.php?reportName=$reportFileName'>\n<h5>$reportNames[$reportFileName]</h5>\n";
        echo "<div>{$reportDescriptions[$reportFileName]}</div>";
        echo "</a>";
    }
}
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
staff_footer();
?>
