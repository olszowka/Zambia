<?php
// Copyright (c) 2011-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section;
$header_section = HEADER_STAFF;

function staff_header($title, $bootstrapVersion = 'bs2', $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage;
    $isBs4or5 = $bootstrapVersion == 'bs4' || $bootstrapVersion == 'bs5';
    $isLoggedIn = isLoggedIn();
    if ($isLoggedIn && REQUIRE_CONSENT && (empty($_SESSION['data_consent']) || $_SESSION['data_consent'] !== 1)) {
        require_once('ParticipantHeader.php');
        require_once('ParticipantFooter.php');
        participant_header(''); // force data consent page
        exit();
    }
    html_header($title, $bootstrapVersion, $is_report, $reportColumns, $reportAdditionalOptions);
    $bodyClass = "";
    if ($fullPage && $isBs4or5) {
        $bodyClass = 'class="full-page bs4"';
    } else if ($fullPage) {
        $bodyClass = 'class="full-page"';
    } else if ($isBs4or5) {
        $bodyClass = 'class="bs4"';
    }
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
            $reportMenuIncludeFilName = $isBs4or5 ? 'ReportMenuBS4Include.php' : 'ReportMenuInclude.php';
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
        switch ($bootstrapVersion) {
            case 'bs5':
                $filename = 'StaffMenu_BS5.xsl';
                break;
            case 'bs4':
                $filename = 'StaffMenu_BS4.xsl';
                break;
            case 'bs2':
            default:
            $filename = 'StaffMenu.xsl';
        }
        RenderXSLT($filename, $paramArray, $xmlDoc);
    } else {
        staff_footer();
        exit();
    }
}
