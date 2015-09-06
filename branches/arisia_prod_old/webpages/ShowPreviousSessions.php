<?php
    require_once ('StaffCommonCode.php');
    require_once ('StaffSearchPreviousSessions_FNC.php');
    global $SessionSearchParameters, $message_error,$message;
    $title="Show Previous Sessions";
    staff_header($title);
    if (!HandleSearchParameters()) {    // Grab the parameters and validate them
        RenderSearchPreviousSessions(); // Will display error message and redisplay form
        staff_footer();
        exit();
        }
    if (!PerformPrevSessionSearch()) {  // Build query and get result
        RenderSearchPreviousSessions(); // Will display error message and redisplay form
        staff_footer();
        exit();
        }
    RenderSearchPreviousSessions();
    echo "<HR>\n";
    RenderSearchPrevSessionResults();
    staff_footer();
?>
