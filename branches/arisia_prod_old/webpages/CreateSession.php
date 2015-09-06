<?php
    require_once ('StaffCommonCode.php');
    require ('RenderEditCreateSession.php');
    global $name, $email, $badgeid;
    if (!get_name_and_email($name, $email)) {
        error_log("get_name_and_email failed in CreateSession.  ");
        }
    //error_log("Did create session get name: $name and email: $email");
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
