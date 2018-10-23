<?php
//	Copyright (c) 2006-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
// function $email=get_email_from_post()
// reads post variable to populate email array
// returns email array or false if an error was encountered.
// A message describing the problem will be stored in global variable $message_error
function get_email_from_post() {
    global $message_error;
    $message_error = "";
    $email['sendto'] = getString('sendto');
    $email['sendfrom'] = getString('sendfrom');
    $email['sendcc'] = getString('sendcc');
    $email['subject'] = stripslashes(getString('subject'));
    $email['body'] = stripslashes(getString('body'));
    return ($email);
}

// function $OK=validate_email($email)
// Checks if values in $email array are acceptible
function validate_email($email) {
    global $message;
    $message = "";
    $OK = true;
    if (strlen($email['subject']) < 6) {
        $message .= "Please enter a more substantive subject.<BR>\n";
        $OK = false;
    }
    if (strlen($email['body']) < 16) {
        $message .= "Please enter a more substantive body.<BR>\n";
        $OK = false;
    }
    return ($OK);
}

// function $email=set_email_defaults()
// Sets values for $email array to be used as defaults for the email
// form when first entering page.
function set_email_defaults() {
    $email['sendto'] = 1; // default to all participants
    $email['sendfrom'] = 1; // default to Arisia Programming
    $email['sendcc'] = 1; // default to None
    $email['subject'] = "";
    $email['body'] = "";
    return ($email);
}

// function render_send_email($email,$message_warning)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, sendcc, subject, body
// $message_warning will be displayed at the top, only if set
// This function will render the entire page.
// This page will next go to the StaffSendEmailCompose_POST page
function render_send_email($email, $message_warning) {
    global $title;
    $title = "Send Email to Participants";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);

    if (isset($message_warning) && strlen($message_warning) > 0) {
        echo "<p class=\"alert\">$message_warning</p>\n";
    }
    echo "<h3>Step 1 -- Compose Email</h3>\n";
    echo "<form name=\"emailform\" method=POST action=\"StaffSendEmailCompose_POST.php\">\n";
    echo "<table>";
    echo "    <tr><td><label for=\"sendto\">To: </label></td>\n";
    echo "    <td><select name=\"sendto\">\n";
    populate_select_from_table("EmailTo", $email['sendto'], "", false);
    echo "    </select></td></tr>";
    echo "<tr><td><label for=\"sendfrom\">From: </label></td>\n";
    echo "    <td><select name=\"sendfrom\">\n";
    populate_select_from_table("EmailFrom", $email['sendfrom'], "", false);
    echo "    </select></td></tr>";
    echo "<tr><td><label for=\"sendcc\">CC: </label></td>\n";
    echo "    <td><select name=\"sendcc\">\n";
    populate_select_from_table("EmailCC", $email['sendcc'], "", false);
    echo "    </select></td></tr>";
    echo "<tr><td><label for=\"subject\">Subject: </label></td>\n";
    echo "    <td><input name=\"subject\" type=\"text\" size=\"40\" value=\"";
    echo htmlspecialchars($email['subject'], ENT_NOQUOTES) . "\">\n";
    echo "    </td></tr>";
    echo "</table><br>\n";
    echo "<textarea name=\"body\" cols=\"80\" rows=\"25\" style=\"width: 1000px;\">";
    echo htmlspecialchars($email['body'], ENT_NOQUOTES) . "</textarea><br>\n";
    echo "<button class=\"ib\" type=\"reset\" value=\"reset\">Reset</button>\n";
    echo "<button class=\"ib\" type=\"submit\" value=\"seeit\">See it</button>\n";
    echo "</form><br>\n";
    echo "<p>Available substitutions:</p>\n";
    echo "<table class=\"multcol-list\">\n";
    echo "<tr><td>\$BADGEID\$</td><td>\$EMAILADDR\$</td></tr>\n";
    echo "<tr><td>\$FIRSTNAME\$</td><td>\$PUBNAME\$</td></tr>\n";
    echo "<tr><td>\$LASTNAME\$</td><td>\$BADGENAME\$</td></tr>\n";
    echo "<tr><td>\$EVENTS_SCHEDULE\$</td><td>\$FULL_SCHEDULE\$</td></tr>\n";
    echo "</table>\n";
    staff_footer();
}

// function renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad)
//
function renderQueueEmail($goodCount, $arrayOfGood, $badCount, $arrayOfBad) {
    global $title;
    $title = "Results of Queueing Email";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);
    echo "<p>$goodCount message(s) were queued for email transmission.<br>\n";
    echo "$badCount message(s) failed.</p>\n";
    echo "<p>List of messages successfully queued:<br>\n";
    echo "Badgeid, Name for Publications, Email Address<br>\n";
    if ($arrayOfGood)
        foreach ($arrayOfGood as $recipient) {
            echo htmlspecialchars($recipient['badgeid']) . ", ";
            echo htmlspecialchars($recipient['name']) . ", ";
            echo htmlspecialchars($recipient['email']) . "<br>\n";
        }
    echo "</p>\n";
    echo "<p>List of recipients which failed:<br>\n";
    echo "Badgeid, Name for Publications, Email Address<br>\n";
    if ($arrayOfBad)
        foreach ($arrayOfBad as $recipient) {
            echo htmlspecialchars($recipient['badgeid']) . ", ";
            echo htmlspecialchars($recipient['name']) . ", ";
            echo htmlspecialchars($recipient['email']) . "<br>\n";
        }
    echo "</p>\n";
    staff_footer();
}

// function render_verify_email($email,$emailverify)
// $email is an array with all values for the send email form:
//   sendto, sendfrom, subject, body
// $emailverify is an array with all values for the verify form:
//   recipient_list, emailfrom, body
// This function will render the entire page.
// This page will next go to the StaffSendEmailResults_POST page
function render_verify_email($email, $email_verify, $message_warning) {
    global $title;
    $title = "Send Email";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);

    if (strlen($message_warning) > 0) {
        echo "<p class=\"alert\">$message_warning</p>\n";
    }
    echo "<h3>Step 2 -- Verify </h3>\n";
    echo "<form name=\"emailverifyform\" method=POST action=\"StaffSendEmailCompose.php\">\n";
    echo "<p>Recipient List:<br>\n";
    echo "<textarea readonly rows=\"8\" cols=\"70\" style=\"width:400px;\">";
    echo $email_verify['recipient_list'] . "</textarea></P>\n";
    echo "<p>Rendering of message body to first recipient:<br>\n";
    echo "<textarea readonly rows=\"25\" cols=\"80\" style=\"width:1000px;font-family: monospace, Monospaced;\">";
    echo $email_verify['body'] . "</textarea></p>\n";
    echo "<input type=\"hidden\" name=\"sendto\" value=\"" . $email['sendto'] . "\">\n";
    echo "<input type=\"hidden\" name=\"sendfrom\" value=\"" . $email['sendfrom'] . "\">\n";
    echo "<input type=\"hidden\" name=\"sendcc\" value=\"" . $email['sendcc'] . "\">\n";
    echo "<input type=\"hidden\" name=\"subject\" value=\"" . htmlspecialchars($email['subject']) . "\">\n";
    echo "<input type=\"hidden\" name=\"body\" value=\"" . htmlspecialchars($email['body']) . "\">\n";
    echo "<button class=\"ib\" type=\"submit\" name=\"navigate\" value=\"goback\">Go Back</button>\n";
    echo "<button class=\"ib\" type=\"submit\" name=\"navigate\" value=\"send\">Send</button>\n";
    echo "</form><br>\n";
    staff_footer();
}

function render_send_email_engine($email, $message_warning) {
    global $title;
    $title = "Pretend to actually send email.";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);

    if (strlen($message_warning) > 0) {
        echo "<p class=\"message_warning\">$message_warning</p>\n";
    }
    echo "<h3>Step 3 -- Actually Send Email </h3>\n";
    staff_footer();
}

// "0" don't show schedule; "1" show events schedule; "2" show full schedule; "3" error condition
function checkForShowSchedule($body) {
    global $message;
    $body = "\r\n" . $body . "\r\n";
    if (preg_match('/\\$EVENTS_SCHEDULE\\$/u', $body) === 1) {
        if (preg_match('/\\$FULL_SCHEDULE\\$/u', $body) === 1) {
            $message = "You may not include both events schedule and full schedule";
            return "3";
        } else if (preg_match('/\\$EVENTS_SCHEDULE\\$.*\\$EVENTS_SCHEDULE\\$/su', $body) === 1) {
            $message = "You may not include the schedule more than once in the body.";
            return "3";
        } else if (preg_match('/\\r\\n\\$EVENTS_SCHEDULE\\$\\r\\n/u', $body) === 0) {
            $message = "The schedule may appear only by itself on a line.";
            return "3";
        } else {
            return "1";
        }
    } else if (preg_match('/\\$FULL_SCHEDULE\\$/u', $body) === 1) {
        if (preg_match('/\\$FULL_SCHEDULE\\$.*\\$FULL_SCHEDULE\\$/su', $body) === 1) {
            $message = "You may not include the schedule more than once in the body.";
            return "3";
        } else if (preg_match('/\\r\\n\\$FULL_SCHEDULE\\$\\r\\n/u', $body) === 0) {
            $message = "The schedule may appear only by itself on a line.";
            return "3";
        } else {
            return "2";
        }
    } else {
        return "0";
    }
}

function renderDuration($durMin, $durHrs) {
    if (($durMin === "0" || $durMin === "00") && ($durHrs === "0" || $durHrs === "00")) {
        return "";
    } else if ($durHrs === "0" || $durHrs === "00") {
        return $durMin . " Min";
    } else if ($durMin === "0" || $durMin === "00") {
        return $durHrs . " Hr";
    } else {
        return $durHrs . " Hr " . $durMin . " Min";
    }
}

// status: "1" show events schedule; "2" show full schedule;
function generateSchedules($status, $recipientinfo) {
    $ConStartDatim = CON_START_DATIM;
    if ($status === "1") {
        $extraWhereClause = "        AND S.divisionid=3"; // events
    } else {
        $extraWhereClause = "";
    }
    $badgeidArr = array_column($recipientinfo, 'badgeid');
    $badgeidList = implode(",", $badgeidArr);
    $query = <<<EOD
SELECT
        POS.badgeid, RM.roomname, S.title, DATE_FORMAT(ADDTIME('$ConStartDatim', SCH.starttime),'%a %l:%i %p') as starttime,
        DATE_FORMAT(S.duration, '%i') as durationmin, DATE_FORMAT(S.duration, '%k') as durationhrs, SCH.sessionid
    FROM
             Schedule SCH
        JOIN Rooms RM USING (roomid)
        JOIN Sessions S USING (sessionid)
        JOIN ParticipantOnSession POS USING (sessionid)
    WHERE
            POS.badgeid IN ($badgeidList)
$extraWhereClause
    ORDER BY
        POS.badgeid, 
        SCH.starttime;
EOD;
    $result = mysqli_query_exit_on_error($query);
    $returnResult = array();
    while ($rowArr = mysqli_fetch_assoc($result)) {
        $scheduleRow = str_pad($rowArr["starttime"], 15); // Fri 12:00 AM (plus 3 spaces)
        $scheduleRow .= str_pad(renderDuration($rowArr["durationmin"], $rowArr["durationhrs"]), 14); // 10 Hr 59 Min (plus 2 spaces)
        $scheduleRow .= str_pad(substr($rowArr["roomname"], 0, 25), 27); // Commonwealth Ballroom ABC (plus 2 spaces)
        $scheduleRow .= str_pad($rowArr["sessionid"], 12); // Session ID (plus 2 spaces)
        $scheduleRow .= str_pad($rowArr["title"], 50); // Video 201: Advanced Live Television Production
        if (!isset($returnResult[$rowArr["badgeid"]])) {
            $returnResult[$rowArr["badgeid"]] = array();
        }
        $returnResult[$rowArr["badgeid"]][] = $scheduleRow;
    }
    return $returnResult;
}

?>