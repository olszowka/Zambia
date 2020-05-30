<?php
// Created by Peter Olszowka on 2020-04-19;
// Copyright (c) 2020 The Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Send Reset Password Link";
require ('PartCommonCode.php');
require_once('email_functions.php');
require_once('external/swiftmailer-5.4.8/lib/swift_required.php');
require_once('external/guzzlehttp-guzzle-6.5.3/vendor/autoload.php');
if (RESET_PASSWORD_SELF !== true) {
    http_response_code(403); // forbidden
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>You have reached this page in error.</p>";
    participant_footer();
    exit;
}
$recaptchaResponse = getString('g-recaptcha-response');
if (empty($recaptchaResponse)) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Error with reCAPTCHA.</p>";
    participant_footer();
    exit;
}
$userIP = $_SERVER['REMOTE_ADDR'];
use GuzzleHttp\Client;
$client = new Client([
    'base_uri' => 'https://www.google.com',
    'timeout'  => 7.5,
]);
$guzzleRepsonse = $client->request('PUT', '/recaptcha/api/siteverify', [
    'form_params' => [
        'secret' => RECAPTCHA_SERVER_KEY,
        'response' => $recaptchaResponse,
        'remoteip' => $userIP
    ]
]);
$recaptchaConf = json_decode($guzzleRepsonse->getBody()->getContents(), true);
if (!$recaptchaConf["success"]) {
    participant_header($title, true, 'Login');
    echo "<p class='alert alert-error vert-sep-above'>Error with reCAPTCHA.</p>";
    participant_footer();
    exit;
}
participant_header($title, true, 'Login');
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
$ipaddressSQL = mysqli_real_escape_string($linki, $userIP);
if (mysqli_num_rows($result) !== 1) {
    // record a non-valid request to help track issues
    $query = <<<EOD
INSERT INTO ParticipantPasswordResetRequests
    (badgeidentered, email, ipaddress, cancelled)
    VALUES ('$badgeid', '$emailSQL', '$ipaddressSQL', 2);
EOD;
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit;
    }
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
    WHERE badgeidentered = '$badgeid';
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}
$query = <<<EOD
INSERT INTO ParticipantPasswordResetRequests
    (badgeidentered, email, ipaddress, expirationdatetime, selector, token)
    VALUES ('$badgeid', '$emailSQL', '$ipaddressSQL', '$expirationSQL', '$selector', '$tokenSQL');
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit;
}

$mailer = get_swift_mailer();

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
$link_lifetime = PASSWORD_RESET_LINK_TIMEOUT_DISPLAY;
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
    The link is good for $link_lifetime from when you originally requested it and can be used to change
    your password only once.  If it has expired just request another link.
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
