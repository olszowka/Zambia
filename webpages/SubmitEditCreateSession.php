<?php
    require_once ('db_functions.php');
    require_once ('StaffCommonCode.php');
    require_once ('RenderEditCreateSession.php');
    require_once ('BrainstormRenderCreateSession.php');
    //session_start();
    $message_error="";
    $message_warn="";
    $action=$_POST["action"]; // "create" or "edit" or "brainstorm"
    get_session_from_post(); // store in global $session array
    prepare_db();
    $status=validate_session(); /* return true if OK.  Store error messages in
        global $messages */
    if ($status==false) {
        $message_warn=$messages; // warning message
        $message_warn.="<BR>The data you entered was incorrect.  Database not updated.";
        //error_log($message_warn);
        if ($action=='brainstorm') {
                BrainstormRenderCreateSession($action,$session,$message_warn,$message_error);
                }
            else {
                RenderEditCreateSession($action,$session,$message_warn,$message_error);
                }
        exit();
        }
    if ($action=="edit") {
        $status=update_session();
        if (!$status) {
                $message_warn=$message2; // warning message
                $message_warn.="<BR>Unknown error updating record.  Database not updated successfully.";
                RenderEditCreateSession($action,$session,$message_warn,$message_error);
                exit();
                }
            else {
                $session_started=true;
                if (isset($_SESSION['return_to_page'])) {
                        header("Location: ".$_SESSION['return_to_page']); /* Redirect browser */
                        }
                    else {
                        header("Location: ViewAllSessions.php"); /* Redirect browser */
                        }
                exit();
                }
        }
    // action = create or brainstorm
    $id=insert_session();
    if (!$id) {
        $message_warn=""; // warning message
        $message_warn.="<BR>".$query."\nUnknown error creating record.  Database not updated successfully.";
        if ($action=='brainstorm') {
                BrainstormRenderCreateSession($action,$session,$message_warn,$message_error);
                }
            else {
                RenderEditCreateSession($action,$session,$message_warn,$message_error);
                }
        exit();
        }
    if ($id!=$session["sessionid"]) {
            $message_warn="Due to problem with database or concurrent editing, the session ";
            $message_warn.="created was actually id: ".$id.".";
            }
        else {
            $message_error="";
            }
    $message_warn="Session record created.  Database updated successfully.";
    set_session_defaults();
    $id=get_next_session_id();
    if (!$id)
       exit();
    $session["sessionid"]=$id;
    if ($action=='brainstorm') {
            BrainstormRenderCreateSession($action,$session,$message_warn,$message_error);
            }
        else {
            RenderEditCreateSession($action,$session,$message_warn,$message_error);
            }
    exit();
?>


