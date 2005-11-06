<?php
    require ('db_functions.php');
    require ('data_functions.php');
    require ('RenderEditCreateSession.php');
    prepare_db();
    $message_error="";
    $message_warn="";
    set_session_defaults();
    $id=get_next_session_id();
    if (!$id) { exit(); }
    $session["sessionid"]=$id;
    $action="create";
    RenderEditCreateSession($action,$session,$message_warn,$message_error);
    exit();
?>
