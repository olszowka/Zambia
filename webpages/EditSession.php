<?php
//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('StaffCommonCode.php');
    require('RenderEditCreateSession.php');
    global $name, $email, $message_error;
    $message_error = "";
    get_name_and_email($name, $email);
    $error = false;
    $id = getInt("id");
    if ($id == "") {
        $message_error = "The id parameter is required. ".$message_error;
        $error = true;
    }
    if ($id < 1) {
        $message_error = "The id parameter must be a valid row index. " . $message_error;
        $error = true;
    }
    $session = retrieve_session_from_db($id);
    if ($session === FALSE) {
        $error = true;
    }
    $message_warn = "";
    $action = "edit";
    RenderEditCreateSession($action, $session, $message_warn, $message_error);
    exit();
?>
