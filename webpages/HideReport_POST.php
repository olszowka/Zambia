<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title, $pageBootstrapVersion;
$title = "Hide Report";
$pageBootstrapVersion = 'bs5';
require_once('StaffCommonCode.php');
require_once('report_functions.php');

if (!isReportHidingEnabled()) {
    $message_error = "You do not currently have permission to perform this action, or report hiding is not enabled.<br>\n";
    RenderError($message_error);
    exit();
}

$reportFileName = getString("reportFileName");
$scope = getString("scope");
$category = getString("category");
$redirectCategory = getString("reportcategory");

if ($reportFileName === null || !mb_ereg_match('^[A-Za-z0-9_]+\\.php$', $reportFileName)) {
    $message_error = "Invalid report specified.<br>\n";
    RenderError($message_error);
    exit();
}

$systemOverrideDir = getReportSystemOverrideDir($overrideWarning);
if (!requireReportDefinition($reportFileName, $systemOverrideDir)) {
    $message_error = "Report $reportFileName not found.<br>\n";
    RenderError($message_error);
    exit();
}

if ($scope === 'all') {
    $hideCategory = null;
} elseif ($scope === 'category') {
    // Not validated against the report's current category set: it may already have been hidden from
    // this category (e.g. a page reloaded from cache after a prior hide) -- unsetting an
    // already-absent category is harmless, so that's just a no-op rather than an error.
    if ($category === null || $category === '') {
        $message_error = "Category not specified.<br>\n";
        RenderError($message_error);
        exit();
    }
    $hideCategory = $category;
} else {
    $message_error = "Invalid scope specified.<br>\n";
    RenderError($message_error);
    exit();
}

if (!hideReportFromCategory($reportFileName, $hideCategory, $hideError)) {
    $message_error = htmlspecialchars($hideError) . "<br>\n";
    RenderError($message_error);
    exit();
}

if (rebuildReportMenus($rebuildWarning, $rebuildErrorMessage) === false) {
    $message_error = htmlspecialchars($rebuildErrorMessage) . "<br>\n";
    RenderError($message_error);
    exit();
}

$location = 'staffReportsInCategory.php';
if ($redirectCategory !== null && $redirectCategory !== '') {
    $location .= '?reportcategory=' . urlencode($redirectCategory);
}
header("Location: $location");
exit();
