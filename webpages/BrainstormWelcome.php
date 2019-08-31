<?php
//	Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $message2, $congoinfo, $title;
$title = "Brainstorm View";
require_once('BrainstormCommonCode.php');
if ($participant = retrieveParticipant($badgeid)) {
    require('renderBrainstormWelcome.php');
    exit();
}
$message_error = $message2 . "<br>Error retrieving data from DB.  No further execution possible.";
RenderError($message_error);
exit();
?>
