<?php
// Created by Peter Olszowka on 2020-04-19;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Forgot Password";
require ('PartCommonCode.php');
participant_header($title, true, 'Login');
if (RESET_PASSWORD_SELF !== true) {
    echo "<p class='alert alert-error vert-sep-above'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
$params = array("USER_ID_PROMPT", USER_ID_PROMPT);
RenderXSLT('ForgotPassword.xsl', $params);
participant_footer();
?>


