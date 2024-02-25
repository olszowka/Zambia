<?php
// Copyright (c) 2019-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
function html_header($title, $bootstrapVersion = 'bs2', $isDataTables = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage;
    $isBs4or5 = $bootstrapVersion == 'bs4' || $bootstrapVersion == 'bs5';
    require_once ("javascript_functions.php");
?>
<!DOCTYPE html>
<html lang="en" <?php if ($fullPage) echo "class =\"full-page\""; ?> >
<head>
    <meta charset="utf-8">
    <title>Zambia &ndash; <?php echo $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<?php
    switch ($bootstrapVersion) {
        case 'bs4':
?>
    <link rel="stylesheet" href="external/bootstrap4.5.0/bootstrap.min.css" type="text/css" >
<?php
            break;
        case 'bs5':
?>
    <link rel="stylesheet" href="external/bootstrap5.3.3/bootstrap.min.css" type="text/css" >
<?php
            break;
        case 'bs2':
        default:
?>
    <link rel="stylesheet" href="external/jqueryui1.8.16/jquery-ui-1.8.16.custom.css" type="text/css">
    <link rel="stylesheet" href="external/bootstrap2.3.2/bootstrap.css" type="text/css" >
    <link rel="stylesheet" href="external/bootstrap2.3.2/bootstrap-responsive.css" type="text/css" >
<?php } ?>
    <link rel="stylesheet" href="external/choices9.0.0/choices.min.css" type="text/css" >
    <link rel="stylesheet" href="external/tabulator-4.9.1/css/tabulator.min.css" type="text/css" >
    <link rel="stylesheet" href="css/zambia_common.css" type="text/css" media="screen" />
<?php if ($isBs4or5) { ?>
    <link rel="stylesheet" href="css/zambia_bs4.css" type="text/css" media="screen" />
<?php } else { ?>
    <link rel="stylesheet" href="css/zambia.css" type="text/css" media="screen" />
<?php } ?>
    <link rel="stylesheet" href="css/staffMaintainSchedule.css" type="text/css" media="screen" />
<?php if ($isDataTables) {
    echo "    <link rel=\"stylesheet\" href=\"external/dataTables1.10.16/dataTables.css\" type=\"text/css\" />\n";
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
}
if (PARTICIPANT_PHOTOS === TRUE) {
    echo "    <link rel=\"stylesheet\" href=\"external/croppie.2.6.5/croppie.css\" type=\"text/css\" />\n";
}
?>
    <link rel="shortcut icon" href="images/favicon.ico">
    <script type="text/javascript">
        var thisPage="<?php echo $title; ?>";
        var conStartDateTime = new Date("<?php echo CON_START_DATIM; ?>".replace(/-/g,"/"));
        var STANDARD_BLOCK_LENGTH = "<?php echo STANDARD_BLOCK_LENGTH; ?>";
    </script>
<?php
    $isRecaptcha = $title == 'Forgot Password';
    /* "external" means 3rd party library */
    load_external_javascript($isDataTables, $isRecaptcha, $bootstrapVersion);
    load_internal_javascript($title, $isDataTables);
?>
</head>
<?php } ?>
