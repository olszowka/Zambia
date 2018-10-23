<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
// The current version of the Welcome Page does not include a form for doing this, but this
// code for SubmitWelcome supports having the user change his password directly on the
// Welcome page.  A previous version prompted the user to change his password if it was
// still the initial password.
global $linki, $title;
require('PartCommonCode.php');
$title = "Welcome";
$interested = getString('interested');
$password = getString('password');
$cpassword = getString('cpassword');
if ($password == "" and $cpassword == "") {
    $update_password = false;
} elseif ($password == $cpassword) {
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
if ($update_password == true) {
    $query = $query . "password=\"" . md5($password) . "\", ";
}
$query .= "interested=" . $interested;
$query .= " WHERE badgeid=\"" . $badgeid . "\"";
if (!mysqli_query($linki, $query)) {
    $message = $query . "<br>Error updating database.  Database not updated.";
    RenderError($message);
    exit();
}
$message = "Database updated successfully.";
if ($update_password == true) {
    $_SESSION['password'] = md5($password);
}
if ($participant = retrieveFullParticipant($badgeid)) {
    require('renderWelcome.php');
    exit();
} else {
    $message = $message2 . "<br>Failure to re-retrieve data for Participant.";
    RenderError($message);
    exit();
}
$result = mysqli_query($linki, "SELECT password FROM Participants WHERE badgeid='';");
if (!$result) {
    $message = "Incorrect badgeid or password.";
    require('login.php');
    exit();
}
$dbobject = mysqli_fetch_object($result);
$dbpassword = $dbobject->password;
if (md5($password) != $dbpassword) {
    $message = "Incorrect badgeid or password.";
    require('login.php');
    exit(0);
}
require('ParticipantHome.php');
exit();
?>
