<?php
//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('CommonCode.php');
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    $_SESSION['role'] = "Participant";
    $badgeid=$_SESSION['badgeid'];
    if (!(may_I("Participant"))) {
        $message = "You are not authorized to access this page.";
        require('login.php');
        exit();
    };
?>
