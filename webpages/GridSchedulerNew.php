<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Temporary page for the in-progress React rewrite of the Grid Scheduler (see webpages/StaffMaintainSchedule.php).
// Not linked from any menu -- reached directly by URL during development. Once feature-complete, this file's
// contents replace StaffMaintainSchedule.php and this file is deleted (see the grid scheduler rewrite plan).
global $fullPage, $title;
$title = "Grid Scheduler (New)";
$fullPage = true; // full-viewport app UI, same as the page this will eventually replace
require_once('StaffCommonCode.php');
require_once('gridScheduler_functions.php');

staff_header($title, 'bs5');

try {
    $bootstrapData = gridScheduler_buildData();
} catch (GridSchedulerConfigException $e) {
    echo "<div class=\"alert alert-danger m-3\">The Grid Scheduler can't be shown because of a configuration problem in db_name.php: ";
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES);
    echo "</div>\n</body>\n</html>\n";
    exit();
}
?>
<script id="grid-scheduler-data" type="application/json"><?php
    echo json_encode($bootstrapData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?></script>
<div id="grid-scheduler-root"></div>
</body>
</html>
