<?php
    function RenderSearchSessionResults($trackidlist,$statusidlist,$typeidlist,$sessionid) {
    global $link;
    global $result,$message2;
    require_once ('retrieve.php');
    require_once ('render_functions.php');
    $title='Precis Search Results';
    if (retrieve_select_from_db($trackidlist,$statusidlist,$typeidlist,$sessionid)==0) {
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