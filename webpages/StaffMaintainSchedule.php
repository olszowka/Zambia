<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Grid Scheduler: the React app in react-apps/GridScheduler (built to javascript/GridScheduler/dist/, loaded by
// load_internal_javascript()), bootstrapped from the JSON below and backed by GridSchedulerAjax.php.
global $fullPage, $title;
$title = "Grid Scheduler";
$fullPage = true; // full-viewport app UI: changes body class so all content is restricted to screen size
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
