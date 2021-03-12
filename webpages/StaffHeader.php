<?php
//	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_STAFF;

function staff_header($title, $bootstrap4 = false, $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage;
    $isLoggedIn = isLoggedIn();
    if ($isLoggedIn && REQUIRE_CONSENT && (empty($_SESSION['data_consent']) || $_SESSION['data_consent'] !== 1)) {
        require_once('ParticipantHeader.php');
        require_once('ParticipantFooter.php');
        participant_header(''); // force data consent page
        exit();
    }
    html_header($title, $bootstrap4, $is_report, $reportColumns, $reportAdditionalOptions);
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
        $paramArray["adduser"] = !USE_REG_SYSTEM;
        $paramArray["PARTICIPANT_PHOTOS"] = PARTICIPANT_PHOTOS === TRUE ? 1 : 0;
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
