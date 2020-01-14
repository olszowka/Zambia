<?php
//	Copyright (c) 2019-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function html_header($title, $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage;
    require_once ("javascript_functions.php");
?>
<!DOCTYPE html>
<html lang="en" <?php if ($fullPage) echo "class =\"fullPage\""; ?> >
<head>
    <meta charset="utf-8">
    <title>Zambia &ndash; <?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="external/jqueryui1.8.16/jquery-ui-1.8.16.custom.css" type="text/css">
    <link rel="stylesheet" href="external/bootstrap2.3.2/bootstrap.css" type="text/css" >
    <link rel="stylesheet" href="external/bootstrap2.3.2/bootstrap-responsive.css" type="text/css" >
    <link rel="stylesheet" href="external/choices9.0.0/choices.min.css" type="text/css" >
    <link rel="stylesheet" href="css/zambia.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/staffMaintainSchedule.css" type="text/css" media="screen" />
<?php if ($is_report) {
    echo "<link rel=\"stylesheet\" href=\"external/dataTables1.10.16/dataTables.css\" type=\"text/css\" />\n";
    if ($reportColumns) {
        echo "<meta id=\"reportColumns\" data-report-columns=\"";
        echo htmlentities(json_encode($reportColumns));
        echo "\">";
    }
    if ($reportAdditionalOptions) {
        echo "<meta id=\"reportAdditionalOptions\" data-report-additional-options=\"";
        echo htmlentities(json_encode($reportAdditionalOptions));
        echo "\">";
    }
} ?>
    <link rel="shortcut icon" href="images/favicon.ico">
    <script type="text/javascript">
        var thisPage="<?php echo $title; ?>";
        var conStartDateTime = new Date("<?php echo CON_START_DATIM; ?>".replace(/-/g,"/"));
        var STANDARD_BLOCK_LENGTH = "<?php echo STANDARD_BLOCK_LENGTH; ?>";
    </script>
<?php
    /* "external" means 3rd party library */
    load_external_javascript($is_report);
    load_internal_javascript($title, $is_report);
?>
</head>
<?php } ?>