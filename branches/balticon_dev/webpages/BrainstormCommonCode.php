<?php
    require_once('CommonCode.php');
    require_once('error_functions.php');
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    global $badgeid;
    $badgeid=$_SESSION['badgeid'];
    $_SESSION['role']="Brainstorm";
    if (!(may_I("public_login") or may_I("Participant") or may_I("Staff") or may_I("Administrator"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>
