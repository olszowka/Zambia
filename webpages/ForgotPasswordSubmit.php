<?php
// Created by Peter Olszowka on 2020-04-19;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Send Reset Password Link";
require ('PartCommonCode.php');
require_once(SWIFT_DIRECTORY."swift_required.php");
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
$conName = CON_NAME;
$subjectLine = "Zambia Password Reset for $conName";
$fromAddress = PASSWORD_RESET_FROM_EMAIL;
$responseParams = array("subject_line" => $subjectLine, "from_address" => $fromAddress);

$badgeid = mysqli_real_escape_string($linki, $badgeid);
$emailSQL = mysqli_real_escape_string($linki, $email);
$query = <<<EOD
SELECT P.pubsname, CD.badgename, CD.firstname, CD.lastname
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
if (mysqli_num_rows($result) !== 1) {
    // don't tell user anything went wrong -- just give regular response.
    RenderXSLT('ForgotPasswordResponse.xsl', $responseParams);
    participant_footer();
    exit;
}
list($pubsname, $badgename, $firstname, $lastname) = mysqli_fetch_array($result);
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
if (empty(SMTP_PROTOCOL)) {
    $transport = (new Swift_SmtpTransport(SMTP_ADDRESS, SMTP_PORT));
} else {
    $transport = (new Swift_SmtpTransport(SMTP_ADDRESS, SMTP_PORT, SMTP_PROTOCOL));
}
if (!empty(SMTP_USER)) {
    $transport->setUsername(SMTP_USER);
}
if (!empty(SMTP_PASSWORD)) {
    $transport->setPassword(SMTP_PASSWORD);
}

//Create the Mailer using your created Transport
$mailer = new Swift_Mailer($transport);

//Create the message and set subject
$message = (new Swift_Message($subjectLine));

//Define from address
$message->setFrom($fromAddress);

//Define body
$urlLink = sprintf('<a href="%s">%s</a>', $url, $url);
if (!empty($badgename)) {
    $username = $badgename;
} elseif (!empty($pubsname)) {
    $username = $pubsname;
} else {
    $comboname = "$firstname $lastname";
    if (!empty($comboname)) {
        $username = $comboname;
    } else {
        $username = "unknown";
    }
}
$emailBody = <<<EOD
<p>
    Hello $username,
</p>
<p>
    We received a request to reset your password for the Zambia scheduling system for $conName.
    If you did not make this request, you can ignore this email.
</p>
<p>
    Here is your password reset link:
</p>
<p>
    $urlLink
</p>
<p>
    Thanks!
</p>
EOD;
$message->setBody($emailBody,'text/html');
$ok = true;
try {
    $message->addTo($email);
} catch (Swift_SwiftException $e) {
    $ok = FALSE;
    error_log("Email address $email failed.");
}
if ($ok) {
    try {
        $sendMailResult = $mailer->send($message);
    } catch (Swift_TransportException $e) {
        $ok = FALSE;
        error_log("Swift transport exception: send email failed.");
    } catch (Swift_SwiftException $e) {
        $ok = FALSE;
        error_log("Swift exception: send email failed.");
    }
}

// regular response is name as error response above
RenderXSLT('ForgotPasswordResponse.xsl', $responseParams);
participant_footer();
?>
