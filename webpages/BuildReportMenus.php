<?php
// Copyright (c) 2019-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title, $pageBootstrapVersion;
$title = "Build Report Menus";
$pageBootstrapVersion = 'bs4';
require_once('StaffCommonCode.php'); // Checks for staff permission among other things
if (!may_I('ConfigureReports')) {
    $message_error = "You do not currently have permission to view this page.<br>\n";
    StaffRenderErrorPage($title, $message_error, 'bs5');
    exit();
}
$areYouSure = getInt("areYouSure");
if ($areYouSure !== 1) {
    staff_header($title, 'bs4');
?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            Rebuild all report menus.  Are you sure?
        </div>
    </div>
</div>
<form class="form-inline" name="confform" method="GET" action="BuildReportMenus.php">
    <input type="hidden" name="areYouSure" value="1" />
    <button type="submit" class="btn btn-primary mr-3">Continue</button>
    <a class="btn btn-secondary" href="StaffOverview.php">Cancel</a>
</form>
    <?php
    staff_footer();
    exit();
}
?>
<?php
require_once('report_functions.php');

$reportCount = rebuildReportMenus($reportOverrideWarning, $rebuildErrorMessage);
if ($reportCount === false) {
    staff_header($title, 'bs4');
?>
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($rebuildErrorMessage); ?>
            </div>
        </div>
    </div>
<?php
    staff_footer();
    return;
}
staff_header($title, 'bs4');
?>
<?php if ($reportOverrideWarning !== null) { ?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-warning" role="alert">
            <?php echo htmlspecialchars($reportOverrideWarning); ?>
        </div>
    </div>
</div>
<?php } ?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-success" role="alert">
            Done.<br />
            <?php echo $reportCount; ?> report(s) processed.
        </div>
    </div>
</div>
<?php
staff_footer();
