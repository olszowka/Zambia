<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Welcome";
    require ('PartCommonCode.php');
    if (retrieve_participant_from_db($badgeid)==0) {
        require ('renderWelcome.php');
        exit();
        }
    $message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
    RenderError($title,$message_error);
    exit();
?>
