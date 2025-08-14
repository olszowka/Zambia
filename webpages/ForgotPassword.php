<?php
// Created by Peter Olszowka on 2020-04-19;
// Copyright (c) 2020-2024 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Forgot Password";
require ('PartCommonCode.php');
if (RESET_PASSWORD_SELF !== true) {
    http_response_code(403); // forbidden
    participant_header($title, true, 'Login', 'bs5');
    echo "<p class='alert alert-error mt-4'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
participant_header($title, true, 'No_Menu', 'bs5');
$params = array("USER_ID_PROMPT" => USER_ID_PROMPT, "RECAPTCHA_SITE_KEY" => RECAPTCHA_SITE_KEY);
RenderXSLT('ForgotPassword.xsl', $params);
participant_footer();
?>
