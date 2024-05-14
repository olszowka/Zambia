<?php
//  Copyright (c) 2011-2023 The Zambia Group. All rights reserved. See copyright document for more details.
$action = isset($_POST["action"]) ? $_POST["action"] : ""; // "create" or "edit" or "brainstorm"
if ($action == "brainstorm") {
    require_once('BrainstormCommonCode.php');
} else {
    require_once('StaffCommonCode.php');
}
require_once('RenderEditCreateSession.php');
require_once('BrainstormRenderCreateSession.php');
//session_start();
global $name, $email, $messages, $session, $message2, $badgeid, $query;
$messages = '';
$message_error = '';
$messageSuccess = '';
$messageWarn = '';
$messageFatal = '';
get_nameemail_from_post($name, $email); //store in arguments and SESSION variables!
$email_status = validate_name_email($name, $email);; /* return true if OK.  Store error messages in
        global $messages */
get_session_from_post(); // store in global $session array
prepare_db_and_more();
$status = validate_session(); /* return true if OK.  Store error messages in
        global $messages */
if ($status == false || $email_status == false) {
    $messageWarn = $messages; // warning message
    $messageWarn .= "<br>The data you entered was incorrect.  Database not updated.";
    //error_log($message_warn);
    if ($action == 'brainstorm') {
        BrainstormRenderCreateSession($session, $messageWarn, $message_error);
    } else {
        RenderEditCreateSession($action, $session, $messageWarn, $messageFatal);
    }
    exit();
}
if ($action == "edit") {
    $status = update_session();
    if (!$status) {
        $messageWarn = $message2; // warning message
        $messageWarn .= "<br>Unknown error updating record.  Database not updated successfully.";
        RenderEditCreateSession($action, $session, $messageWarn, $messageFatal);
        exit();
    } else {
        if (!record_session_history($session['sessionid'], $badgeid, $name, $email, 3, $session['status'])) {
            // 3 is code for unknown edit
            error_log("Error recording session history. " . $message_error);
        }
        $session_started = true;
        if (isset($_SESSION['return_to_page'])) {
            header("Location: " . $_SESSION['return_to_page']); /* Redirect browser */
        } else {
            header("Location: ViewAllSessions.php"); /* Redirect browser */
        }
        exit();
    }
}
// action = create or brainstorm
$id = insert_session();
if (!$id) {
    $messageWarn = ""; // warning message
    $messageWarn .= "<BR>" . $query . "\nUnknown error creating record.  Database not updated successfully.";
    if ($action == 'brainstorm') {
        BrainstormRenderCreateSession($session, $messageWarn, $message_error);
    } else {
        RenderEditCreateSession($action, $session, $messageWarn, $messageFatal);
    }
    exit();
}
if ($id != $session["sessionid"]) {
    $messageWarn = "Due to problem with database or concurrent editing, the session ";
    $messageWarn .= "created was actually id: " . $id . ".";
} else {
    $message_error = "";
}
if ($action == 'brainstorm') {
    $messageWarn = "Session record created.  Database updated successfully.";
} else {
    $messageSuccess = "Session record created.  Database updated successfully.";
}
record_session_history($id, $badgeid, $name, $email, ($action == 'brainstorm' ? 1 : 2), // 1 is brainstorm; 2 is normal create
    $session['status']);
set_session_defaults();
$id = get_next_session_id();
if (!$id)
    exit();
$session["sessionid"] = $id;
if ($action == 'brainstorm') {
    set_brainstorm_session_defaults();
    BrainstormRenderCreateSession($session, $messageWarn, $message_error);
} else {
    RenderEditCreateSession($action, $session, $messageWarn, $messageFatal, $messageSuccess);
}
exit();
?>
