<?php
    global $participant,$message,$message_error,$message2,$congoinfo;
    $title="My Profile";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (getCongoData($badgeid)==0) {
        require ('renderMyContact.php');
        exit();
        }
    RenderError($title,$message_error);
    exit();
?>
