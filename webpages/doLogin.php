<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $headerErrorMessage, $link, $linki, $title;
if (!isset($_SESSION['badgeid'])) {
    require_once('CommonCode.php');
    $userIdPrompt = USER_ID_PROMPT;
    $title = "Submit Password";
    $badgeid = mysqli_real_escape_string($linki, getString('badgeid'));
    $password = getString('passwd');
    $query = "Select password from Participants where badgeid='$badgeid';";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    if (mysqli_num_rows($result) != 1) {
        $headerErrorMessage = "Incorrect $userIdPrompt or password.";
        require('login.php');
        exit(0);
    }
    $dbobject = mysqli_fetch_object($result);
    mysqli_free_result($result);
    $dbpassword = $dbobject->password;
    if (password_verify($password, $dbobject->password)) {
        $headerErrorMessage = "Incorrect $userIdPrompt or password.";
        require('login.php');
        exit(0);
    }
    $query = "Select badgename from CongoDump where badgeid='$badgeid';";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    if (mysqli_num_rows($result) == 1) {
        $dbobject = mysqli_fetch_object($result);
        $badgename = $dbobject->badgename;
        $_SESSION['badgename'] = $badgename;
    }
    mysqli_free_result($result);
    $pubsname = "";
    $query = "Select pubsname from Participants where badgeid='$badgeid';";
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $dbobject = mysqli_fetch_object($result);
    $pubsname = $dbobject->pubsname;
    mysqli_free_result($result);
    if (!($pubsname == "")) {
        $_SESSION['badgename'] = $pubsname;
    }
    $_SESSION['badgeid'] = $badgeid;
    $_SESSION['hashedPassword'] = $dbpassword;
    set_permission_set($badgeid);
} else {
    $badgeid = $_SESSION['badgeid'];
}
$message2 = "";
if (may_I('Staff')) {
    require('StaffPage.php');
} elseif (may_I('Participant')) {
    if (!$participant_array = retrieveFullParticipant($badgeid)) {
        $message_error = $message2 . "<br />Error retrieving data from DB.  No further execution possible.";
        RenderError($message_error);
    } else {
        require('renderWelcome.php');
    }
} elseif (may_I('public_login')) {
    require('renderBrainstormWelcome.php');
} else {
    unset($_SESSION['badgeid']);
    $message_error = "There is a problem with your $userIdPrompt's permission configuration:  It doesn't have ";
    $message_error .= "permission to access any welcome page.  Please contact Zambia staff.";
    RenderError($message_error);
}
exit();
?>
