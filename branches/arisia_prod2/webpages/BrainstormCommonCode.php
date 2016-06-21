<?php
    require_once('CommonCode.php');
    require_once('error_functions.php');
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    global $badgeid;
    $badgeid=$_SESSION['badgeid'];
    $_SESSION['role']="Brainstorm";
    if (!(may_I("public_login") || may_I("Participant") || may_I("Staff"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>
