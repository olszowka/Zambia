<?php
// Copyright (c) 2015-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title, $pageBootstrapVersion;
$title = "Reports in Category";
$pageBootstrapVersion = 'bs5';
require_once('StaffCommonCode.php');
require_once('report_functions.php');
$CON_NAME = CON_NAME;
$reportHidingEnabled = isReportHidingEnabled();
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
staff_header($title, 'bs5');
$xml = new DomDocument('1.0', 'UTF-8');
$doc = $xml -> createElement('doc');
$doc = $xml -> appendChild($doc);
$queryNode = $xml -> createElement('query');
$queryNode = $doc -> appendChild($queryNode);
$queryNode -> setAttribute('queryName', 'reports');
if ($reportcategoryid === "") {
    foreach ($reportNames as $reportFileName => $reportName) {
        $row = $xml -> createElement('row');
        $row = $queryNode -> appendChild($row);
        $row -> setAttribute('reportfilename', $reportFileName);
        $row -> setAttribute('reportname', $reportName);
        $row -> setAttribute('reportdescription', $reportDescriptions[$reportFileName]);
    }
} else {
    foreach ($reportCategories[$reportcategoryid] as $reportFileName) {
        $row = $xml -> createElement('row');
        $row = $queryNode -> appendChild($row);
        $row -> setAttribute('reportfilename', $reportFileName);
        $row -> setAttribute('reportname', $reportNames[$reportFileName]);
        $row -> setAttribute('reportdescription', $reportDescriptions[$reportFileName]);
    }
}
$paramArray = array();
$paramArray["reportcategoryid"] = $reportcategoryid;
$paramArray["reporthidingenabled"] = $reportHidingEnabled;
RenderXSLT('staffReportsInCategory.xsl', $paramArray, $xml);
staff_footer();
?>
