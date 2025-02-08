<?php
// Copyright (c) 2015-2025 Peter Olszowka. All rights reserved. See copyright document for more details.

// staffBulkSendBatch - send the next batch of emails from the javascript code splitting it into smaller chunks

require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('StaffSendEmailCommonCode.php'); //actually do the email sends

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
if (!(isLoggedIn() && may_I('SendEmail'))) {
    exit();
}

$email = null;
$recipients = null;
if (!array_key_exists('data', $_POST)) {
    exit();
}

$data = $_POST['data'];
$data = base64_decode($data);
$data = urldecode($data);
$data = json_decode($data, true);
if (array_key_exists('email', $data)) {
    $email = $data['email'];
}
if (array_key_exists('recipientinfo', $data)) {
    $recipients = $data['recipientinfo'];
}

if ($email == null || $recipients == null) { // valid args not passed
    exit();
}

$json_return['emailsSent'] = sendEmails($email, $recipients, $startIndex = 0, count($recipients), true);
echo json_encode($json_return) . "\n";

?>
