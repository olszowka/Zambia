<?php
    global $participant,$message_error,$message,$congoinfo;
    $title="Vendor View";
    require_once ('VendorCommonCode.php'); 
    if (retrieve_participant_from_db($badgeid)==0) {
        require ('renderVendorWelcome.php');
        exit();
        }
    $message_error=$message_error."<BR>Error retrieving data from DB.  No further execution possible.";
    RenderError($title,$message_error);
    exit();
?>
