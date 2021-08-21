<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $headerErrorMessage, $link, $linki, $title, $reg_link;
require_once('CommonCode.php');
$userIdPrompt = USER_ID_PROMPT;
if (!isset($_SESSION['badgeid'])) {
    $title = "Submit Password";
    $email = getstring('badgeid');
    $password = getstring('passwd');
    if (substr($email, -12) == "@programming") {
        // need to review this to see if it still works. 8.20.2021. lmv
        $result = mysqli_query($linki, "SELECT CD.badgeid, P.password FROM CongoDump CD JOIN Participants P ON CD.badgeid = P.badgeid WHERE CD.email = '" . 
            mysqli_real_escape_string($linki, $email) . "'");
        if (mysqli_num_rows($result) == 0) {
            $message = "Incorrect email or password.";
            require('login.php');
            exit();
        }
        $dbobject = mysqli_fetch_object($result);
        $badgeid = $dbobject->badgeid;
        $dbpassword = $dbobject->password;
        if ($dbpassword != $password) {
            $message = "Incorrect email or password.";
            require('login.php');
            exit();
        }
        mysqli_free_result($result);
    }
    else {
        //$result = mysqli_query($reg_link, "SELECT PeopleID, Password FROM People WHERE Email = '" . mysqli_real_escape_string($reg_link, $email) . "'");
        $result = mysqli_query($reg_link, "SELECT PeopleID, Password FROM People WHERE Email = '" . $email . "'");
        if (mysqli_num_rows($result) == 0) {
            $message = "Incorrect $userIdPrompt or password. People table.";
            require ('login.php');
            exit();
        }
        $dbobject = mysqli_fetch_object($result);
        mysqli_free_result($result);
        $badgeid = $dbobject->PeopleID;
        $dbpassword = $dbobject->Password;
          //echo "badge: " . $badgeid . "<BR>dbpass: " . $dbpassword . "<BR>pass: " . $password . "<BR>email: " . $email;
          //echo "<pre>" . print_r($dbobject,1) . "</pre>";
          //exit(0);
        if (!password_verify($password, $dbpassword)) {
            $headerErrorMessage = "Incorrect $userIdPrompt or password. Password compare.";
            require ('login.php');
            exit(0);
        }
       
        // Ensure user exists in Zambia, and add them as a participant if they're not.
        configureNewUser($badgeid);
        // Update user data in Zambia.
        updateUser($badgeid);
    }

    $result=mysqli_query($linki, "SELECT badgename, data_retention FROM CongoDump CD JOIN Participants P ON CD.badgeid = P.badgeid WHERE CD.badgeid='" . $badgeid . "'");
    if ($result) {
        $dbobject = mysqli_fetch_object($result);
        $badgename = $dbobject->badgename;
        $_SESSION['badgename'] = $badgename;
        $_SESSION['data_consent'] = intval($dbobject->data_retention);
    }
    else {
        $_SESSION['badgename'] = "";
    }
    mysqli_free_result($result);
    $pubsname = "";
    $result = mysqli_query($linki, "SELECT pubsname FROM Participants WHERE badgeid='" . $badgeid . "'");
    if ($result) {
        $dbobject = mysqli_fetch_object($result);
        $pubsname = $dbobject->pubsname;
    }
    if (!($pubsname == "")) {
        $_SESSION['badgename'] = $pubsname;
    }
    mysqli_free_result($result);
    $_SESSION['badgeid'] = $badgeid;
    $_SESSION['hashedPassword'] = $dbpassword;
    set_permission_set($badgeid);
    //echo '<pre>',print_r($_SESSION['permission_set'],1),'</pre>';
    //echo '<pre>',print_r($_SESSION,1),'</pre>';
    //error_log("debug: Completed set_permission_set.\n");
    $result=mysqli_query($linki, "UPDATE CongoDump SET last_login=NOW() WHERE badgeid='" . $badgeid . "'");
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
    $message_error = "There is a problem with your account's permission configuration:  It doesn't have ";
    $message_error .= "permission to access any welcome page.  Please contact convention staff.";
    RenderError($message_error);
}
exit();
?>
