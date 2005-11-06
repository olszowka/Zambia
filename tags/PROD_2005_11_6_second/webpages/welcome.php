<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Welcome";
    require ('db_functions.php'); //define database functions
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (retrieve_participant_from_db($badgeid)==0) {
        require ('renderWelcome.php');
        exit();
        }
    $message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
    RenderError($title,$message_error);
    exit();
?>
