<?php
// Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
// Not sure if there is any need to support post/been here before
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('email_functions.php');
global $message, $link, $title;
$subst_list = array('$BADGEID$', '$FIRSTNAME$', "\$LASTNAME\$", "\$EMAILADDR\$", "\$PUBNAME\$", "\$BADGENAME\$");
$title = "Send Email (Step 2 - verify)";
if (!isset($_POST['sendto'])) { // page has not been visited before
    $message_error = "Expected POST data was missing.  This page is intended to be reached via a form.";
    $message_error .= " It will not work if you link to it directly.\n";
    StaffRenderErrorPage($title, $message_error);
    exit(0);
}
$email = get_email_from_post();
if (!validate_email($email)) {
    render_send_email($email, $message); // $message came from validate_email
    exit(0);
}
$query = "SELECT emailtoquery FROM EmailTo WHERE emailtoid = {$email['sendto']};";
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$emailto = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);
$query = $emailto['emailtoquery'];
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$i = 0;
while ($recipientinfo[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $i++;
}
mysqli_free_result($result);
$recipient_count = $i;
$emailverify = array();
$emailverify['recipient_list'] = "";
for ($i = 0; $i < $recipient_count; $i++) {
    $emailverify['recipient_list'] .= $recipientinfo[$i]['pubsname'] . " - ";
    $emailverify['recipient_list'] .= htmlspecialchars($recipientinfo[$i]['email'], ENT_NOQUOTES) . "\n";
}
$query = "SELECT emailfromaddress FROM EmailFrom WHERE emailfromid=" . $email['sendfrom'];
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$emailverify['emailfrom'] = (mysqli_fetch_array($result, MYSQLI_ASSOC))["emailfromaddress"];
mysqli_free_result($result);
$repl_list = array($recipientinfo[0]['badgeid'], $recipientinfo[0]['firstname'], $recipientinfo[0]['lastname']);
$repl_list = array_merge($repl_list, array($recipientinfo[0]['email'], $recipientinfo[0]['pubsname'], $recipientinfo[0]['badgename']));
$status = checkForShowSchedule($email['body']); // "0" don't show schedule; "1" show events schedule; "2" show full schedule; "3" error condition
if ($status === "3") {
    render_send_email($email, $message); // $message came from checkForShowSchedule
    exit(0);
} else if ($status === "1" || $status === "2") {
    $shortRecipientInfo = array();
    $shortRecipientInfo[] = $recipientinfo[0];
    $scheduleInfoArray = generateSchedules($status, $shortRecipientInfo);
}
$emailverify['body'] = str_replace($subst_list, $repl_list, $email['body']);
if ($status === "1" || $status === "2") {
    if ($status === "1") {
        $scheduleTag = '$EVENTS_SCHEDULE$';
    } else {
        $scheduleTag = '$FULL_SCHEDULE$';
    }
    if (isset($scheduleInfoArray[$recipientinfo[0]['badgeid']])) {
        $scheduleInfo = " Start Time      Duration            Room Name          Session ID                      Title\n";
        $scheduleInfo .= implode("\n", $scheduleInfoArray[$recipientinfo[0]['badgeid']]);
    } else {
        $scheduleInfo = "No schedule items for you were found.";
    }
    $emailverify['body'] = str_replace($scheduleTag, $scheduleInfo, $emailverify['body']);
}
render_verify_email($email, $emailverify, $message_warning = "");
?>
