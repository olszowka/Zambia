<?php
// Created by Peter Olszowka on 2020-04-21;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Submit Reset Password";
require ('PartCommonCode.php');
// participant_header($title, true, 'Login');
$selector = getString('selector');
$validator = getString('validator');
$badgeid = getString('badgeid');
$password = getString('password');
$cpassword = getString('cpassword');
if (RESET_PASSWORD_SELF !== true) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
if (empty($selector) || empty($validator) || empty($badgeid)) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Reset password form was missing required parameters.</p>";
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
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Authentication error resetting password.</p>";
    participant_footer();
    exit;
}
list($oldBadgeid, $token) = mysqli_fetch_array($result);
mysqli_free_result($result);
$calc = hash('sha256', hex2bin($validator));
if (!hash_equals($token, $calc) || $badgeid !== $oldBadgeid) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Authentication error resetting password.</p>";
    participant_footer();
    exit;
}
if (empty($password) || $password !== $cpassword) {
    participant_header($title, true, 'Login');
    $params = array(
        "selector" => $selector,
        "validator" => $validator,
        "badgeid" => $badgeid,
        "error_message" => "Passwords do not match or are blank.  Try again."
    );
    RenderXSLT('ForgotPasswordResetForm.xsl', $params);
    participant_footer();
}
$badgeidSQL = mysqli_real_escape_string($linki, $badgeid);
$query = <<<EOD
UPDATE ParticipantPasswordResetRequests
    SET cancelled = 1
    WHERE badgeid = '$badgeidSQL';
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}
$passwordHash = md5($password);
$query = <<<EOD
UPDATE Participants
    SET password = '$passwordHash'
    WHERE badgeid = '$badgeidSQL';
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}
// Show login page with password reset confirmation
$title = "Login";
participant_header($title, false, 'Password_Reset');
participant_footer();
