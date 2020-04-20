<?php
// Created by Peter Olszowka on 2020-04-19;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Send Reset Password Link";
require ('PartCommonCode.php');
participant_header($title, true, 'Login');
if (RESET_PASSWORD_SELF !== true) {
    echo "<p class='alert alert-error vert-sep-above'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
$badgeid = getString('badgeid');
$email = getString('emailAddress');
if (empty($badgeid) || empty($email)) {
    $params = array();
    $params["USER_ID_PROMPT"] = USER_ID_PROMPT;
    $params["error_message"] = "Both ${params['USER_ID_PROMPT']} and email address are required.";
    RenderXSLT('ForgotPassword.xsl', $params);
    participant_footer();
    exit;
}
$badgeid = mysqli_real_escape_string($linki, $badgeid);
$emailSQL = mysqli_real_escape_string($linki, $email);
$query = <<<EOD
SELECT count(P.*)
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
    WHERE
            P.badgeid = '$badgeid'
        AND CD.email = '$emailSQL';
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}
if (mysqli_fetch_assoc($result)[0] != "1") {
    // don't tell user anything went wrong -- just give regular response.
    RenderXSLT('ForgotPasswordResponse.xsl');
    participant_footer();
    exit;
}
mysqli_free_result($result);
// Create tokens
$selector = bin2hex(random_bytes(8));
$token = random_bytes(32);

$url = sprintf('%sForgotPasswordLink.php?%s', ROOT_URL, http_build_query([
    'selector' => $selector,
    'validator' => bin2hex($token)
]));

// Token expiration
$expires = new DateTime('NOW');
$expires->add(new DateInterval(PASSWORD_RESET_LINK_TIMEOUT));
$expirationSQL = date_format($expires,'Y-m-d H:i:s');
$tokenSQL = hash('sha256', $token);
$query = <<<EOD
UPDATE ParticipantPasswordResetRequests
    SET cancelled = 1
    WHERE badgeid = '$badgeid';
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}
$query = <<<EOD
INSERT INTO ParticipantPasswordResetRequests
    (badgeid, expirationdatetime, selector, token)
    VALUES ('$badgeid', '$expirationSQL', '$selector', '$tokenSQL');
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}

//Create the Transport
$transport = (new Swift_SmtpTransport(SMTP_ADDRESS, 2525));

//Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

//Create the message and set subject
$message = (new Swift_Message("Zambia Password Reset for " . CON_NAME));

//Define from address
$message->setFrom(PASSWORD_RESET_FROM_EMAIL);
//Define body
$message->setBody($emailverify['body'],'text/plain');


/*
 * List of stuff to be done here
 * 1) Confirm userid and email submitted. -- Respond with error if not.
 * 2) Confirm userid and email address match db
 * 3) Generate hash
 * 4) Record hash in db
 * 5) Invalidate all other entries in db for this user
 * 6) Send email
 * 7) Write response page
 */
$params = array("USER_ID_PROMPT", USER_ID_PROMPT);
RenderXSLT('ForgotPasswordResponse.xsl', $params);
participant_footer();
?>


