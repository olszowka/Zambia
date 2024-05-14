<?php
// Copyright (c) 2005-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title, $message2;
require('PartCommonCode.php');
$title = "Welcome";
$interested = getString('interested');
$password = getString('password');
$cpassword = getString('cpassword');
$message2 = '';
if (($password === "" or $password === null) and ($cpassword === "" or $cpassword === null)) {
    $update_password = false;
} elseif ($password === $cpassword) {
    $update_password = true;
} else {
    $message_error = "Passwords do not match each other.  Database not updated.";
    if ($participant = retrieveFullParticipant($badgeid)) {
        require('renderWelcome.php');
        exit();
    } else {
        $message = $message2 . "<br>Failure to re-retrieve data for Participant.";
        RenderError($message);
        exit();
    }
}
$query = "UPDATE Participants SET ";
if ($update_password === true) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = $query . "password=\"" . $hashedPassword . "\", ";
}
$query .= "interested=" . $interested;
$query .= " WHERE badgeid=\"" . $badgeid . "\"";
if (!mysqli_query($linki, $query)) {
    $message = $query . "<br>Error updating database.  Database not updated.";
    RenderError($message);
    exit();
}
$message = "Database updated successfully.";
if ($update_password === true) {
    $_SESSION['hashedPassword'] = $hashedPassword;
}
if ($participant_array = retrieveFullParticipant($badgeid)) {
    require('renderWelcome.php');
    exit();
} else {
    $message = $message2 . "<br>Failure to re-retrieve data for Participant.";
    RenderError($message);
    exit();
}
?>
