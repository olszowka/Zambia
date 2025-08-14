<?php
// Created by Peter Olszowka on 2020-04-21;
// Copyright (c) 2020-2024 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Reset Password";
require ('PartCommonCode.php');
$selector = getString('selector');
$validator = getString('validator');
if (RESET_PASSWORD_SELF !== true) {
    http_response_code(403); // forbidden
    participant_header($title, true, 'Normal', 'bs5');
    echo <<<EOD
<div class="row">
    <div class="col-12 mt-4">
        <div class="alert alert-danger">You have reached this page in error.</div>
    </div>
</div>
EOD;
    participant_footer();
    exit;
}
participant_header($title, true, 'Normal', 'bs5');
if (empty($selector) || empty($validator)) {
    echo <<<EOD
<div class="row">
    <div class="col-12 mt-4">
        <div class="alert alert-danger">Reset password link was missing required parameters.</div>
    </div>
</div>
EOD;
    participant_footer();
    exit;
}
$selectorSQL = mysqli_real_escape_string($linki, $selector);
$query = <<<EOD
SELECT
        PPRR.badgeidentered, PPRR.token, P.pubsname, CD.badgename, CD.firstname, CD.lastname
    FROM
             ParticipantPasswordResetRequests PPRR
        JOIN Participants P ON PPRR.badgeidentered = P.badgeid
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
    // Report failure, but not specifically why
    RenderXSLT('ForgotPasswordBadLink.xsl');
    participant_footer();
    exit;
}
list($badgeid, $token, $pubsname, $badgename, $firstname, $lastname) = mysqli_fetch_array($result);
mysqli_free_result($result);
$calc = hash('sha256', hex2bin($validator));
if (!hash_equals($token, $calc)) {
    // Report failure, but not specifically why
    RenderXSLT('ForgotPasswordBadLink.xsl');
    participant_footer();
    exit;
}
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
    "user_name" => $username,
    "badgeid" => $badgeid
);
RenderXSLT('ForgotPasswordResetForm.xsl', $params);
participant_footer();
