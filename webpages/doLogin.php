<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $headerErrorMessage, $link, $linki, $title;
require_once('CommonCode.php');
require_once('login_functions.php');
$userIdPrompt = get_user_id_prompt();
if (!isset($_SESSION['badgeid'])) {
    $title = "Submit Password";
    $badgeid = getString('badgeid');
    $password = getString('passwd');
    $query_param_arr = array($badgeid);
    $query = "SELECT password, data_retention, badgeid FROM Participants WHERE badgeid = ?;";
    $query_definition = 's';
    if (is_email_login_supported()) {
        $query = <<<EOD
SELECT 
       P.password, P.data_retention, P.badgeid 
  FROM 
       Participants P 
  JOIN CongoDump C USING (badgeid)
 WHERE 
        P.badgeid = ?
     OR 
        C.email = ?;
EOD;
        $query_param_arr = array($badgeid, $badgeid);
        $query_definition = 'ss';
    }
    if (!$result = mysqli_query_with_prepare_and_exit_on_error($query, $query_definition, $query_param_arr)) {
        exit(); // Should have exited already
    }
    if (mysqli_num_rows($result) != 1) {
        $headerErrorMessage = "Incorrect $userIdPrompt or password.";
        require('login.php');
        exit(0);
    }
    $dbobject = mysqli_fetch_object($result);
    mysqli_free_result($result);
    $query_param_arr = array($dbobject->badgeid);
    $badgeid = $dbobject->badgeid;
    $dbpassword = $dbobject->password;
    $_SESSION['data_consent'] = $dbobject->data_retention;
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
