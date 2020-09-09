<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_STAFF;

function staff_header($title, $bootstrap4 = false, $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage;
    html_header($title, $bootstrap4, $is_report, $reportColumns, $reportAdditionalOptions);
    $isLoggedIn = isLoggedIn();
    $bodyClass = "";
    if ($fullPage && $bootstrap4) {
        $bodyClass = 'class="full-page bs4"';
    } else if ($fullPage) {
        $bodyClass = 'class="full-page"';
    } else if ($bootstrap4) {
        $bodyClass = 'class="bs4"';
    }
    if ($fullPage) {
?>
<body <?php echo $bodyClass; ?>>
    <div id="myhelper"></div><!-- used for drag-and-drop operations -->
    <div id="headerContainer">
<?php
    commonHeader('Staff', $isLoggedIn, false, 'Normal', "", $bootstrap4);
?>
    </div>
<?php
    } else { /* not full page */
?>
<body <?php echo $bodyClass; ?>>
    <div class="container-fluid">
<?php
        commonHeader('Staff', $isLoggedIn, false, 'Normal', "", $bootstrap4);
    }
    /* Render Staff Menu */
    if ($isLoggedIn) {
        $paramArray = array();
        $paramArray["title"] = $title;
        try {
            $reportMenuIncludeFilName = $bootstrap4 ? 'ReportMenuBS4Include.php' : 'ReportMenuInclude.php';
            $reportMenuIncludeFilHand = fopen ($reportMenuIncludeFilName,  'r');
            if ($reportMenuIncludeFilHand === false) {
                $paramArray["reportMenuList"] = '';
            } else {
                $paramArray["reportMenuList"] = fread($reportMenuIncludeFilHand, 10000);
            }
        } catch(Exception $e) {
            $paramArray["reportMenuList"] = '';
        }
        $xmlDoc = GeneratePermissionSetXML();
        // echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xmlDoc->saveXML(), "i"));
        $filename = $bootstrap4 ? 'StaffMenu_BS4.xsl' : 'StaffMenu.xsl';
        RenderXSLT($filename, $paramArray, $xmlDoc);
    } else {
        staff_footer();
        exit();
    }
}
?>