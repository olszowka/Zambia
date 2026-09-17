<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_STAFF;

function staff_header($title, $bootstrapVersion, $is_data_tables = false, $reportColumns = false, $reportAdditionalOptions = false, $isJqueryUI = false) {
    global $fullPage;
    $isLoggedIn = isLoggedIn();
    if ($isLoggedIn && REQUIRE_CONSENT && (empty($_SESSION['data_consent']) || $_SESSION['data_consent'] !== 1)) {
        require_once('ParticipantHeader.php');
        require_once('ParticipantFooter.php');
        participant_header(''); // force data consent page
        exit();
    }
    html_header($title, $bootstrapVersion, $is_data_tables, $reportColumns, $reportAdditionalOptions, $isJqueryUI);
    $bodyClass = $fullPage ? 'class="full-page bs4"' : 'class="bs4"';
    $topSectionBehavior = $isLoggedIn ? 'NORMAL' : 'SESSION_EXPIRED';
    if ($fullPage) {
?>
<body <?php echo $bodyClass; ?>>
    <div id="myhelper"></div><!-- used for drag-and-drop operations -->
    <div id="headerContainer">
<?php
    commonHeader('Staff', $topSectionBehavior, $bootstrapVersion);
?>
    </div>
<?php
    } else { /* not full page */
?>
<body <?php echo $bodyClass; ?>>
    <div class="container-fluid">
<?php
        commonHeader('Staff', $topSectionBehavior, $bootstrapVersion);
    }
    /* Render Staff Menu */
    if ($isLoggedIn) {
        $paramArray = array();
        $paramArray["title"] = $title;
        $paramArray["PARTICIPANT_PHOTOS"] = PARTICIPANT_PHOTOS === TRUE ? 1 : 0;
        try {
            $reportMenuIncludeFilName = 'ReportMenuBS4Include.php';
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
        $filename = $bootstrapVersion == 'bs4' ? 'StaffMenu_BS4.xsl' : 'StaffMenu_BS5.xsl';
        RenderXSLT($filename, $paramArray, $xmlDoc);
    } else {
        staff_footer();
        exit();
    }
}
