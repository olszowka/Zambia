<?php
    require ('db_functions.php');
    require ('data_functions.php');
    require ('RenderSearchSession.php');
    prepare_db();
    $message_error="";
    $message_warn="";
    $action="search";
    RenderSearchSession($action,$session,$message_warn,$message_error);
    exit();
?>
