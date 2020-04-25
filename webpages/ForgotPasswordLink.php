<?php
// Created by Peter Olszowka on 2020-04-21;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Reset Password";
require ('PartCommonCode.php');
participant_header($title, true, 'Login');
$selector = getString('selector');
$validator = getString('validator');
if (RESET_PASSWORD_SELF !== true) {
    echo "<p class='alert alert-error vert-sep-above'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
if (empty($selector) || empty($validator)) {
    echo "<p class='alert alert-error vert-sep-above'>Reset password link was missing required parameters.</p>";
    participant_footer();
    exit;
}
$selectorSQL = mysqli_real_escape_string($linki, $selector);
$query = <<<EOD
SELECT
        badgeid, token
    FROM
        ParticipantPasswordResetRequests
    WHERE
            selector = '$selectorSQL'
        AND cancelled = 0
        AND NOW() < expirationdatetime;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}
if (mysqli_num_rows($result) !== 1) {
    // Report failure, but not specifically why
    RenderXSLT('ForgotPasswordBadLink.xsl');
    participant_footer();
    exit;
}
list($badgeid, $token) = mysqli_fetch_array($result);
mysqli_free_result($result);
$calc = hash('sha256', hex2bin($validator));
if (!hash_equals($token, $calc)) {
    // Report failure, but not specifically why
    RenderXSLT('ForgotPasswordBadLink.xsl');
    participant_footer();
    exit;
}
$params = array(
    "selector" => $selector,
    "validator" => $validator,
    "badgeid" => $badgeid
);
RenderXSLT('ForgotPasswordResetForm.xsl', $params);
participant_footer();
