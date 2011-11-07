<?php
    require_once('CommonCode.php');
    global $badgeid;
    $badgeid=$_SESSION['badgeid'];
    $_SESSION['role']="Brainstorm";
    if (!(may_I("public_login"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
?>
