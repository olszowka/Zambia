<?php
// Copyright (c) 2011-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
global $fullPage, $title;
$title="Grid Scheduler";
$fullPage = true; // changes body class to support all content restricted to screen size
require_once('StaffCommonCode.php');
require_once('StaffMaintainSchedule_FNC.php');

staff_header($title, 'bs5');
?>
<div id="zambia-grid-scheduler" class="zambia-grid-scheduler mainContentContainerFlex flex-column-container"
     data-zgs-rooms="<?php echo getRoomsForScheduler(); ?>">
</div>
</div><!-- closes #fullPageContainer -->
</body>
</html>
