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

// Collects the .php filenames (non-recursive) directly inside $path.
function reportFileNamesIn($path) {
    $fileNames = array();
    $dirHandle = opendir($path);
    if (!$dirHandle) {
        return $fileNames;
    }
    while (false !== ($fileName = readdir($dirHandle))) {
        if ($fileName == "." || $fileName == ".." ||
            is_dir("$path/$fileName") ||
            !mb_ereg_match(".*\\.php$", $fileName)
        ) {
            continue;
        }
        $fileNames[$fileName] = true;
    }
    closedir($dirHandle);
    return $fileNames;
}

// Rebuilds ReportMenuBS4Include.php and staffReportsInCategoryInclude.php from the current set of
// report definitions (reports/, the configured system override directory, and reportsConOverrides/).
// $warning is set (as a string) if the system report override layer isn't configured/available; this
// isn't fatal. Returns the number of reports processed, or false on failure (with $errorMessage set).
function rebuildReportMenus(&$warning, &$errorMessage) {
    global $report;
    $warning = null;
    $errorMessage = null;
    $path = 'reports';
    if (!is_dir($path)) {
        $errorMessage = "Directory $path not found.";
        return false;
    }
    $systemOverrideDir = getReportSystemOverrideDir($warning);
    $reportFileNames = reportFileNamesIn($path);
    if ($systemOverrideDir !== null) {
        $reportFileNames += reportFileNamesIn($systemOverrideDir);
    }
    $reportFileNames += reportFileNamesIn('reportsConOverrides');
    $allReports = array();
    foreach (array_keys($reportFileNames) as $reportFileName) {
        requireReportDefinition($reportFileName, $systemOverrideDir);
        if (isset($report)) {
            // preserve only data needed for menu generation
            $allReports[$reportFileName] = array('name' => $report['name'], 'description' => $report['description'], 'categories' => $report['categories']);
        }
        // Reassign (not unset()) so the "global $report;" binding above survives into the next
        // iteration -- unset() on an imported global only drops the local reference, so isset($report)
        // would silently read as undefined for every iteration after the first.
        $report = null;
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
    $reportMenuBS4FilHand = fopen('ReportMenuBS4Include.php', 'wb');
    $staffReportsICIFilHand = fopen('staffReportsInCategoryInclude.php', 'wb');
    if ($reportMenuBS4FilHand === false || $staffReportsICIFilHand === false) {
        $errorMessage = "Build Reports Failed: invalid file or directory permissions, check installation of Zambia.";
        return false;
    }
    fwrite($staffReportsICIFilHand, "<?php\n");
    fwrite($staffReportsICIFilHand, "\$reportCategories = array();\n");
    foreach ($reportCategories as $reportCategory => $reportCategoryArray) {
        $encodedReportCategory = urlencode($reportCategory);
        $htmlReportCategory = htmlspecialchars($reportCategory, ENT_NOQUOTES);
        fwrite($reportMenuBS4FilHand, "<a class='dropdown-item' href='staffReportsInCategory.php?reportcategory=$encodedReportCategory'>$htmlReportCategory</a>\n");
        fwrite($staffReportsICIFilHand, "\$reportCategories[" . var_export($reportCategory, true) . "] = array(");
        asort($reportCategoryArray, SORT_NUMERIC);
        $notFirst = false;
        foreach ($reportCategoryArray as $reportName => $sortOrder) {
            if ($notFirst) {
                fwrite($staffReportsICIFilHand, ',');
            }
            fwrite($staffReportsICIFilHand, var_export($reportName, true));
            $notFirst = true;
        }
        fwrite($staffReportsICIFilHand, ");\n");
    }
    fwrite($staffReportsICIFilHand, "\$reportNames = array();\n");
    foreach ($allReports as $reportName => $reportArray) {
        fwrite($staffReportsICIFilHand, "\$reportNames[" . var_export($reportName, true) . "] = " . var_export($reportArray['name'], true) . ";\n");
    }
    fwrite($staffReportsICIFilHand, "\$reportDescriptions = array();\n");
    foreach ($allReports as $reportName => $reportArray) {
        fwrite($staffReportsICIFilHand, "\$reportDescriptions[" . var_export($reportName, true) . "] = " . var_export($reportArray['description'], true) . ";\n");
    }
    fclose($reportMenuBS4FilHand);
    fclose($staffReportsICIFilHand);
    return count($allReports);
}

// Whether the "Report Hiding" admin feature is currently enabled for this session. Gated on the
// ConfigureReports permission atom regardless of the session flag, so the buttons/menu can't be left
// showing for a user whose permissions changed mid-session.
function isReportHidingEnabled() {
    return may_I('ConfigureReports') && !empty($_SESSION['report_hiding_enabled']);
}

// Marks the boundaries of the block within a reportsConOverrides/<reportFileName> file that is
// managed automatically by the "Report Hiding" admin feature. Content outside the markers is left
// alone, so a con override file can freely mix hand-written overrides with hidden-report bookkeeping.
const REPORT_HIDING_BLOCK_BEGIN = '// REPORT-HIDING-MANAGED-BEGIN (rewritten automatically; edit freely outside these markers)';
const REPORT_HIDING_BLOCK_END = '// REPORT-HIDING-MANAGED-END';

// Hides $reportFileName from a single category ($category, a category name) or from all categories
// ($category === null), by creating (if needed) or editing its reportsConOverrides/ file. Returns true
// on success, or false with $error set to a message on failure.
function hideReportFromCategory($reportFileName, $category, &$error) {
    $error = null;
    $conOverrideDir = 'reportsConOverrides';
    if (!is_dir($conOverrideDir)) {
        $error = "Directory $conOverrideDir not found.";
        return false;
    }
    $path = "$conOverrideDir/$reportFileName";
    $existingContent = file_exists($path) ? file_get_contents($path) : "<?php\n";
    if ($existingContent === false) {
        $error = "Unable to read $path.";
        return false;
    }
    $hideAllLine = "\$report['categories'] = array();";
    $hideCategoryLine = $category === null ? null : "unset(\$report['categories'][" . var_export($category, true) . "]);";
    $beginPos = mb_strpos($existingContent, REPORT_HIDING_BLOCK_BEGIN);
    $endPos = $beginPos === false ? false : mb_strpos($existingContent, REPORT_HIDING_BLOCK_END, $beginPos);
    $lines = array();
    if ($beginPos !== false && $endPos !== false) {
        $blockInterior = mb_substr($existingContent, $beginPos + mb_strlen(REPORT_HIDING_BLOCK_BEGIN), $endPos - ($beginPos + mb_strlen(REPORT_HIDING_BLOCK_BEGIN)));
        foreach (explode("\n", $blockInterior) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $lines[] = $line;
            }
        }
    }
    if ($category === null) {
        // Hiding from all categories supersedes any single-category hides already recorded.
        $lines = array($hideAllLine);
    } elseif (!in_array($hideAllLine, $lines, true) && !in_array($hideCategoryLine, $lines, true)) {
        $lines[] = $hideCategoryLine;
    }
    $blockText = REPORT_HIDING_BLOCK_BEGIN . "\n" . implode("\n", $lines) . "\n" . REPORT_HIDING_BLOCK_END;
    if ($beginPos !== false && $endPos !== false) {
        $newContent = mb_substr($existingContent, 0, $beginPos) . $blockText . mb_substr($existingContent, $endPos + mb_strlen(REPORT_HIDING_BLOCK_END));
    } else {
        $newContent = rtrim($existingContent) . "\n" . $blockText . "\n";
    }
    if (file_put_contents($path, $newContent) === false) {
        $error = "Unable to write $path.";
        return false;
    }
    return true;
}
