<?php
    function RenderSearchSessionResults($track,$status) {
    global $link;
    global $result;
    require_once ('retrieve.php');
    require_once ('RenderSessionReport.php');
    retrieve_select_from_db($track,$status);
    RenderSessionReport();
    exit();
    }
?>
