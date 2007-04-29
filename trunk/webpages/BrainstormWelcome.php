<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Welcome";
    require ('db_functions.php'); 
    require ('BrainstormCommonCode.php'); 
    if (retrieve_participant_from_db($badgeid)==0) {
        require ('renderBrainstormWelcome.php');
        exit();
        }
    $message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
    BrainstormRenderError($title,$message_error);
    exit();
?>
