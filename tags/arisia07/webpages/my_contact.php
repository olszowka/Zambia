<?php
    global $participant,$message,$message_error,$message2,$congoinfo;
    $title="My Profile";
    require ('db_functions.php'); //define database functions
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (getCongoData($badgeid)==0) {
        require ('renderMyContact.php');
        exit();
        }
    RenderError($title,$message_error);
    exit();
?>
