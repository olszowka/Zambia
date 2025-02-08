<?php
// Copyright (c) 2011-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// This page has two completely different entry points from a user flow standpoint:
//   1) Common functions in email flow - these functions loop and send/queue the email
require_once('email_functions.php');
require_once('external/swiftmailer-5.4.8/lib/swift_required.php');

function sendEmails($email, $recipients, $startIndex = 0, $batchSize = 100000, $ajax = false)
{
    $subst_list = array("\$BADGEID\$", "\$FIRSTNAME\$", "\$LASTNAME\$", "\$EMAILADDR\$", "\$PUBNAME\$", "\$BADGENAME\$");

    $mailer = get_swift_mailer();
    $emailfrom = $email['emailfrom'];
    $emailcc = $email['emailcc'];
    $recipientinfo = array_slice($recipients, $startIndex, $batchSize);

    $emailsSent = '';

    $status = checkForShowSchedule($email['body']); // "0" don't show schedule; "1" show events schedule; "2" show full schedule; "3" error condition
    if ($status === "1" || $status === "2") {
        $scheduleInfoArray = generateSchedules($status, $recipientinfo);
    }
    $recipient_count = count($recipientinfo);

    for ($i = 0; $i < $recipient_count; $i++) {
        $ok = TRUE;
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
        $message->setBody($emailverify['body'], 'text/plain');
        //$message =& new Swift_Message($email['subject'],$emailverify['body']);
        if ($ajax) {
            $emailsSent .= $recipientinfo[$i]['pubsname'] . ' - ' . $recipientinfo[$i]['email']  . ': ';
        } else {
            echo $recipientinfo[$i]['pubsname'] . " - " . $recipientinfo[$i]['email'] . ": ";
        }
        try {
            $message->addTo($recipientinfo[$i]['email']);
        } catch (Swift_SwiftException $e) {
            if ($ajax) {
                $emailsSent .=  $e->getMessage() . "<br>\n";
            } else {
                echo $e->getMessage() . "<br>\n";
            }
            $ok = FALSE;
        }
        if ($emailcc != "") {
            $message->addCc($emailcc);
        }
        if (SMTP_QUEUEONLY === TRUE) {
            $sql = "INSERT INTO EmailQueue(emailto, emailfrom, emailcc, emailsubject, body, status) VALUES(?, ?, ?, ?, ?, ?);";
            $param_arr = array($recipientinfo[$i]['email'], $emailfrom, $emailcc, $email['subject'], $emailverify['body'], 0);
            $types = "sssssi";
            $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
            if ($rows == 1) {
                echo "Queued<br>\n";
                $code = 200;
            } else {
                echo "Queue failed<br>\n";
                $code = 599;
            }
        } else {
            try {
                $code = 0;
                $mailer->send($message);
            } catch (Swift_SwiftException $e) {
                $code = $e->getCode();
                if ($code < 500) {
                    if ($ajax) {
                        $emailsSent .=  $e->getMessage() . ", adding to queue<br>\n";
                    } else {
                        echo $e->getMessage() . ", adding to queue<br>\n";
                    }
                } else {
                    if ($ajax) {
                        $emailsSent .= $e->getMessage() . ", not able to be retried.<br>\n";
                    } else {
                        echo $e->getMessage() . ", not able to be retried.<br>\n";
                    }
                }

                $ok = FALSE;
                if ($code < 500) {
                    $sql = "INSERT INTO EmailQueue(emailto, emailfrom, emailcc, emailsubject, body, status) VALUES(?, ?, ?, ?, ?, ?);";
                    $param_arr = array($recipientinfo[$i]['email'], $emailfrom, $emailcc, $email['subject'], $emailverify['body'], $e->getCode());
                    $types = "sssssi";
                    $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
                }
            }
            if ($ok == TRUE) {
                if ($ajax) {
                    $emailsSent .= "Sent<br>\n";
                } else {
                    echo "Sent<br>\n";
                }{}
            }
        }
        $sql = "INSERT INTO EmailHistory(emailto, emailfrom, emailcc, emailsubject, status) VALUES(?, ?, ?, ?, ?);";
        $param_arr = array($recipientinfo[$i]['email'], $emailfrom, $emailcc, $email['subject'], $code);
        $types = "ssssi";
        $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
    }

    return $emailsSent;
}

?>
