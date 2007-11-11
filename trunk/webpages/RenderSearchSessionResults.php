<?php
    function RenderSearchSessionResults($track,$status,$type) {
    global $link;
    global $result,$message2;
    require_once ('StaffCommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once ('retrieve.php');
    require_once ('render_functions.php');
    $title='Precis Search Results';
    $statusname=0;
    if (retrieve_select_from_db($track,$status,$statusname,$type)==0) {
        staff_header($title);
        $showlinks=true; // Show links to edit sessions
        RenderPrecis($result,$showlinks);
        staff_footer();
        exit();
        }
    $message_error="Error retrieving from database. ".$message2;
    RenderError($title,$message_error); 
    }
?>
