<?php
    require ('BrainstormCommonCode.php');
    require ('BrainstormRenderCreateSession.php');
    global $email, $name, $badgeid;
    get_name_and_email($name, $email);
    // error_log("badgeid: $badgeid; name: $name; email: $email"); // for debugging only
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
