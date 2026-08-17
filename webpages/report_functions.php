<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.

// A report's definition ($report) can be split across up to 3 files of the same name:
//   1) reports/<reportFileName>                                   (base definition)
//   2) reportsSystemOverrides/<REPORT_SYSTEM_OVERRIDE_SUBDIR>/<reportFileName>  (system-level override, optional)
//   3) reportsConOverrides/<reportFileName>                       (con-specific override, optional)
// Each existing file is require()d in that order into the same $report variable, so a later
// file can override all or part of an earlier definition (or define a report that doesn't exist
// in an earlier location) simply by setting whichever $report[...] keys it wants to change.

// Determines the system report override directory from REPORT_SYSTEM_OVERRIDE_SUBDIR.
// Returns the directory path, or null if that override layer isn't configured/available.
// If $warning is not null on return, the constant was undefined/empty or the directory was missing.
function getReportSystemOverrideDir(&$warning) {
    $warning = null;
    $subdir = defined('REPORT_SYSTEM_OVERRIDE_SUBDIR') ? REPORT_SYSTEM_OVERRIDE_SUBDIR : '';
    if ($subdir === '') {
        $warning = "REPORT_SYSTEM_OVERRIDE_SUBDIR is not defined (or empty) in db_name.php; no system-level report overrides will be applied.";
        return null;
    }
    $dir = "reportsSystemOverrides/$subdir";
    if (!is_dir($dir)) {
        $warning = "Configured system report override directory ($dir) does not exist; no system-level report overrides will be applied.";
        return null;
    }
    return $dir;
}

// Builds the $report global for $reportFileName by require()ing the base report file (if any),
// then the system override file (if any), then the con override file (if any), in that order.
// $systemOverrideDir should come from getReportSystemOverrideDir() (may be null).
// Returns true if $report was defined by at least one of the 3 files.
function requireReportDefinition($reportFileName, $systemOverrideDir) {
    global $report;
    // Reassign rather than unset(): unset() on a global-imported variable breaks the
    // reference to $GLOBALS['report'], so later requires would only ever set a local copy.
    $report = null;
    $baseFile = "reports/$reportFileName";
    if (file_exists($baseFile)) {
        require($baseFile);
    }
    if ($systemOverrideDir !== null) {
        $systemOverrideFile = "$systemOverrideDir/$reportFileName";
        if (file_exists($systemOverrideFile)) {
            require($systemOverrideFile);
        }
    }
    $conOverrideFile = "reportsConOverrides/$reportFileName";
    if (file_exists($conOverrideFile)) {
        require($conOverrideFile);
    }
    return isset($report);
}
