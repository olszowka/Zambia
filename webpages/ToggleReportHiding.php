<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title, $pageBootstrapVersion;
$title = "Toggle Report Hiding";
$pageBootstrapVersion = 'bs5';
require_once('StaffCommonCode.php');
if (!may_I('ConfigureReports')) {
    $message_error = "You do not currently have permission to perform this action.<br>\n";
    StaffRenderErrorPage($title, $message_error, 'bs5');
    exit();
}
$enabling = empty($_SESSION['report_hiding_enabled']);
if ($enabling) {
    // Hiding a report writes to reportsConOverrides/, so make sure that's actually possible before
    // turning the feature on -- otherwise every hide attempt would silently fail later.
    $conOverrideDir = 'reportsConOverrides';
    if (!is_dir($conOverrideDir)) {
        $message_error = "Cannot enable report hiding: the directory <code>reportsConOverrides</code> does not exist.<br>\n";
        StaffRenderErrorPage($title, $message_error, 'bs5');
        exit();
    }
    if (!is_writable($conOverrideDir)) {
        $message_error = "Cannot enable report hiding: the directory <code>reportsConOverrides</code> is not writable by the web server. Fix its permissions and try again.<br>\n";
        StaffRenderErrorPage($title, $message_error, 'bs5');
        exit();
    }
}
$_SESSION['report_hiding_enabled'] = $enabling;

$location = 'StaffOverview.php';
if (isset($_SERVER['HTTP_REFERER'])) {
    $refererHost = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
    if ($refererHost !== null && isset($_SERVER['HTTP_HOST']) && $refererHost === $_SERVER['HTTP_HOST']) {
        $location = $_SERVER['HTTP_REFERER'];
    }
}
header("Location: $location");
exit();
