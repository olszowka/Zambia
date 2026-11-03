<?php
// Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once ('StaffCommonCode.php');
    require_once ('StaffSearchPreviousSessions_FNC.php');
    global $SessionSearchParameters, $message_error, $message, $title;
    $title="Show Previous Sessions";
    staff_header($title, 'bs5');
    if (!HandleSearchParameters()) {    // Grab the parameters and validate them
        RenderSearchPreviousSessions(); // Will display error message and redisplay form
        staff_footer();
        exit();
    }
    $resultXML = PerformPrevSessionSearch();  // Build query and get result
    if ($resultXML === false) {
        RenderSearchPreviousSessions(); // Will display error message and redisplay form
        staff_footer();
        exit();
    }
    RenderSearchPreviousSessions();
    echo "<hr>\n";
    RenderSearchPrevSessionResults($resultXML);
    staff_footer();
?>
