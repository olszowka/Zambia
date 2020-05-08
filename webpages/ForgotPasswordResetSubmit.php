<?php
// Created by Peter Olszowka on 2020-04-21;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Submit Reset Password";
require ('PartCommonCode.php');
if (RESET_PASSWORD_SELF !== true) {
    http_response_code(403); // forbidden
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
$control = getString('control');
$controliv = getString('controliv');
$password = getString('password');
$cpassword = getString('cpassword');
$controlParams = interpretControlString(array('controliv' => $controliv, 'control' => $control));
if (!$controlParams || empty($controlParams['selector']) || empty($controlParams['validator']) || empty($controlParams['badgeid'])) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Reset password form was missing required parameters.</p>";
    participant_footer();
    exit;
}
$selectorSQL = mysqli_real_escape_string($linki, $controlParams['selector']);
$query = <<<EOD
SELECT
        PPRR.badgeid, PPRR.token, P.pubsname, CD.badgename, CD.firstname, CD.lastname
    FROM
             ParticipantPasswordResetRequests PPRR
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
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
list($badgeid, $token, $pubsname, $badgename, $firstname, $lastname) = mysqli_fetch_array($result);
mysqli_free_result($result);
$calc = hash('sha256', hex2bin($controlParams['validator']));
if (!hash_equals($token, $calc) || $controlParams['badgeid'] !== $badgeid) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Authentication error resetting password.</p>";
    participant_footer();
    exit;
}
if (empty($password) || $password !== $cpassword) {
    participant_header($title, true, 'Login');
    $controlParams = array(
        "selector" => $selector,
        "validator" => $validator,
        "badgeid" => $badgeid
    );
    $controlArray = generateControlString($controlParams);
    if (!empty($badgename)) {
        $username = $badgename;
    } elseif (!empty($pubsname)) {
        $username = $pubsname;
    } else {
        $comboname = "$firstname $lastname";
        if (!empty($comboname)) {
            $username = $comboname;
        } else {
            $username = "";
        }
    }
    $params = array(
        "control" => $controlArray['control'],
        "controliv" => $controlArray['controliv'],
        "badgeid" => $badgeid,
        "user_name" => $username,
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
