<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_STAFF;

function staff_header($title, $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage;
    html_header($title, $is_report, $reportColumns, $reportAdditionalOptions);
    $isLoggedIn = isLoggedIn();
    if ($fullPage) {
?>
<body class="full-page">
    <div id="myhelper"></div><!-- used for drag-and-drop operations -->
    <div id="headerContainer">
<?php
    commonHeader('Staff', $isLoggedIn, false, 'Normal');
?>
    </div>
<?php
    } else { /* not full page */
?>
<body>
    <div class="container-fluid">
<?php
        commonHeader('Staff', $isLoggedIn, false, 'Normal');
    }
?>
<?php
    /* Render Staff Menu */
    $paramArray = array();
    $paramArray["title"] = $title;
    try {
        $reportMenuIncludeFilHand = fopen ( 'ReportMenuInclude.php' ,  'r');
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
    RenderXSLT('StaffMenu.xsl', $paramArray, $xmlDoc);
}