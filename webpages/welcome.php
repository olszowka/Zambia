<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Welcome";
require('PartCommonCode.php');
if ($participant = retrieveFullParticipant($badgeid)) {
    require('renderWelcome.php');
    exit();
}
$message_error = $message2 . "<br />Error retrieving data from DB.  No further execution possible.";
RenderError($message_error);
exit();
?>
