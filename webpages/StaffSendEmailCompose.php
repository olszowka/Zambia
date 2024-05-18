<?php
// Copyright (c) 2011-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// This page has two completely different entry points from a user flow standpoint:
//   1) Beginning of send email flow -- start to specify parameters
//   2) After verify -- 'back' can change parameters -- 'send' fire off email sending code
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('email_functions.php');
require_once('external/swiftmailer-5.4.8/lib/swift_required.php');
global $title, $message, $link;
if (!(isLoggedIn() && may_I("SendEmail"))) {
    exit(0);
}
if (isset($_POST['sendto'])) { // page has been visited before
// restore previous values to form
    $email = get_email_from_post();
} else { // page hasn't just been visited
    $email = set_email_defaults();
}
$message_warning = "";
if (empty($_POST['navigate']) || $_POST['navigate']!='send') {
    render_send_email($email,$message_warning);
    exit(0);
}
// put code to send email here.
// render_send_email_engine($email,$message_warning);
$title = "Staff Send Email";
$timeLimitSuccess = set_time_limit(600);
if (SMTP_QUEUEONLY === TRUE) {
    staff_header($title, 'bs4');
}
if (!$timeLimitSuccess) {
    RenderError("Error extending time limit.");
    exit(0);
}
$subst_list = array("\$BADGEID\$", "\$FIRSTNAME\$", "\$LASTNAME\$", "\$EMAILADDR\$", "\$PUBNAME\$", "\$BADGENAME\$");
$email = get_email_from_post();

$mailer = get_swift_mailer();

$query = "SELECT emailtoquery FROM EmailTo where emailtoid=".$email['sendto'];
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$emailto = mysqli_fetch_array($result,MYSQLI_ASSOC);
mysqli_free_result($result);
$query = $emailto['emailtoquery'];
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$i = 0;
while ($recipientinfo[$i]=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    $i++;
}
mysqli_free_result($result);
$recipient_count = $i;
$query = "SELECT emailfromaddress FROM EmailFrom where emailfromid = {$email['sendfrom']};";
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$emailfrom = $row['emailfromaddress'];
mysqli_free_result($result);
$query="SELECT emailaddress FROM EmailCC where emailccid = {$email['sendcc']};";
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$emailcc = $row['emailaddress'];
mysqli_free_result($result);
$status = checkForShowSchedule($email['body']); // "0" don't show schedule; "1" show events schedule; "2" show full schedule; "3" error condition
if ($status === "1" || $status === "2") {
    $scheduleInfoArray = generateSchedules($status, $recipientinfo);
}
for ($i=0; $i<$recipient_count; $i++) {
    $ok=TRUE;
    //Create the message and set subject
    $message = (new Swift_Message($email['subject']));

    $repl_list = array($recipientinfo[$i]['badgeid'], $recipientinfo[$i]['firstname'], $recipientinfo[$i]['lastname']);
    $repl_list = array_merge($repl_list, array($recipientinfo[$i]['email'], $recipientinfo[$i]['pubsname'], $recipientinfo[$i]['badgename']));
    $emailverify['body'] = str_replace($subst_list, $repl_list, $email['body']);
    if ($status === "1" || $status === "2") {
        if ($status === "1") {
            $scheduleTag = '$EVENTS_SCHEDULE$';
        } else {
            $scheduleTag = '$FULL_SCHEDULE$';
        }
        if (isset($scheduleInfoArray[$recipientinfo[$i]['badgeid']])) {
            $scheduleInfo = " Start Time      Duration            Room Name          Session ID                      Title\n";
            $scheduleInfo .= implode("\n", $scheduleInfoArray[$recipientinfo[$i]['badgeid']]);
        } else {
            $scheduleInfo = "No scheduled items for you were found.";
        }
        $emailverify['body'] = str_replace($scheduleTag, $scheduleInfo, $emailverify['body']);
    }
    //Define from address
    $message->setFrom($emailfrom);
    //Define body
    $message->setBody($emailverify['body'],'text/plain');
    //$message =& new Swift_Message($email['subject'],$emailverify['body']);
    echo ($recipientinfo[$i]['pubsname']." - ".$recipientinfo[$i]['email'].": ");
    try {
        $message->addTo($recipientinfo[$i]['email']);
    } catch (Swift_SwiftException $e) {
        echo $e->getMessage()."<br>\n";
        $ok=FALSE;
    }
    if ($emailcc != "") {
        $message->addCc($emailcc);
    }
    if (SMTP_QUEUEONLY === TRUE) {
        $sql = "INSERT INTO EmailQueue(emailto, emailfrom, emailcc, emailsubject, body, status) VALUES(?, ?, ?, ?, ?, ?);";
        $param_arr = array($recipientinfo[$i]['email'] , $emailfrom, $emailcc, $email['subject'], $emailverify['body'], 0);
        $types = "sssssi";
        $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
        if ($rows == 1)
            echo "Queued<br>";
        else
            echo "Queue failed<br>";
    } else {
        try {
            $code = 0;
            $mailer->send($message);
        }
        catch (Swift_SwiftException $e) {
            $code = $e->getCode();
            if ($code < 500) {
                echo $e->getMessage() . ", adding to queue<br>\n";
            } else {
                echo $e->getMessage() . ", not able to be retried.<br>\n";
            }

            $ok = FALSE;
            if ($code < 500) {
                $sql = "INSERT INTO EmailQueue(emailto, emailfrom, emailcc, emailsubject, body, status) VALUES(?, ?, ?, ?, ?, ?);";
                $param_arr = array($recipientinfo[$i]['email'] , $emailfrom, $emailcc, $email['subject'], $emailverify['body'], $e->getCode());
                $types = "sssssi";
                $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
            }
        }
        if ($ok == TRUE) {
            echo "Sent<br>";
        }
    }
    $sql = "INSERT INTO EmailHistory(emailto, emailfrom, emailcc, emailsubject, status) VALUES(?, ?, ?, ?, ?);";
    $param_arr = array($recipientinfo[$i]['email'] , $emailfrom, $emailcc, $email['subject'], $code);
    $types = "ssssi";
    $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
}
//$log =& Swift_LogContainer::getLog();
//echo $log->dump(true);
if (SMTP_QUEUEONLY === TRUE) {
    staff_footer();
}
?>
