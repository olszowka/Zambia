<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $headerErrorMessage, $link, $linki, $title;
if (!isset($_SESSION['badgeid'])) {
    require_once('CommonCode.php');
    $userIdPrompt = USER_ID_PROMPT;
    $title = "Submit Password";
    $badgeid = getString('badgeid');
    $password = getString('passwd');
    $query = "SELECT password, data_retention FROM Participants WHERE badgeid = ?;";
    $query_param_arr = array($badgeid);
    if (!$result = mysqli_query_with_prepare_and_exit_on_error($query, 's', $query_param_arr)) {
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
    $data_consent = $dbobject->data_retention;
    if (!password_verify($password, $dbpassword)) {
        $headerErrorMessage = "Incorrect $userIdPrompt or password.";
        require('login.php');
        exit(0);
    }
    $query = "SELECT badgename FROM CongoDump WHERE badgeid = ?;";
    if (!$result = mysqli_query_with_prepare_and_exit_on_error($query, 's', $query_param_arr)) {
        exit(); // Should have exited already
    }
    if (mysqli_num_rows($result) == 1) {
        $dbobject = mysqli_fetch_object($result);
        $badgename = $dbobject->badgename;
        $_SESSION['badgename'] = $badgename;
    }
    mysqli_free_result($result);
    $pubsname = "";
    $query = "SELECT pubsname FROM Participants WHERE badgeid = ?;";
    if (!$result = mysqli_query_with_prepare_and_exit_on_error($query, 's', $query_param_arr)) {
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
    $query = "SELECT data_retention FROM Participants WHERE badgeid = ?;";
    $query_param_arr = array($badgeid);
    if (!$result = mysqli_query_with_prepare_and_exit_on_error($query, 's', $query_param_arr)) {
        exit(); // Should have exited already
    }
    if (mysqli_num_rows($result) != 1) {
        $headerErrorMessage = "Incorrect $userIdPrompt or password.";
        require('logout.php');
        exit(0);
    }
    $dbobject = mysqli_fetch_object($result);
    mysqli_free_result($result);
    $data_consent = $dbobject->data_retention;
}
$message2 = "";
if (may_I('Staff')) {
     if (!$participant_array = retrieveFullParticipant($badgeid)) {
        $message_error = $message2 . "<br />Error retrieving data from DB.  No further execution possible.";
        RenderError($message_error);
    } else {
        if ($data_consent == 0 && REQUIRE_CONSENT == true) {
            require('dataConsent.php');
        } else {
            require('StaffPage.php');
        }
    }
} elseif (may_I('Participant')) {
    if (!$participant_array = retrieveFullParticipant($badgeid)) {
        $message_error = $message2 . "<br />Error retrieving data from DB.  No further execution possible.";
        RenderError($message_error);
    } else {
        if ($data_consent == 0 && REQUIRE_CONSENT == true) {
            require('dataConsent.php');
        } else {
            require('renderWelcome.php');
        }
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
