<?php
    require ('db_functions.php');
    require ('data_functions.php');
    require ('BrainstormRenderCreateSession.php');
    require ('BrainstormCommonCode.php');
    $message_error="";
    $message_warn="";
    set_session_defaults();
    $id=get_next_session_id();
    if (!$id) { exit(); }
    $session["sessionid"]=$id;
    $action="create";
    BrainstormRenderCreateSession($action,$session,$message_warn,$message_error);
    exit();
?>
