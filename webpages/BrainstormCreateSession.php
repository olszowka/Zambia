<?php
// $Header$
//	Copyright (c) 2011-2017 The Zambia Group. All rights reserved. See copyright document for more details.
    require ('BrainstormCommonCode.php');
    require ('BrainstormRenderCreateSession.php');
    global $email, $name, $badgeid, $session;
    get_name_and_email($name, $email);
    // error_log("badgeid: $badgeid; name: $name; email: $email"); // for debugging only
    $message_error="";
    $message_warn="";
    set_session_defaults();
    set_brainstorm_session_defaults();
    $id=get_next_session_id();
    if (!$id) { exit(); }
    $session["sessionid"]=$id;
    BrainstormRenderCreateSession($session,$message_warn,$message_error);
    exit();
?>
