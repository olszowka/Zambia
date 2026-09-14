<?php
//	Copyright (c) 2006-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
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
    $email['sendfrom'] = 1; // default to Programming
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
    staff_header($title, 'bs5');
    ?>
    <div class="container-xl">
        <?php if (isset($message_warning) && strlen($message_warning) > 0) { ?>
            <p class="alert alert-warning"><?php echo $message_warning; ?></p>
        <?php } ?>
        <h3 class="mt-4">Step 1 -- Compose Email</h3>
        <form name="emailform" method="POST" action="StaffSendEmailCompose_POST.php">
            <div class="row mt-3">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label" for="sendto">To:</label>
                </div>
                <div class="col-md-16 col-lg-14">
                    <select class="form-select" id="sendto" name="sendto">
                        <?php populate_select_from_table("EmailTo", $email['sendto'], "", false); ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label" for="sendfrom">From:</label>
                </div>
                <div class="col-md-16 col-lg-14">
                    <select class="form-select" id="sendfrom" name="sendfrom">
                        <?php populate_select_from_table("EmailFrom", $email['sendfrom'], "", false); ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label" for="sendcc">CC:</label>
                </div>
                <div class="col-md-16 col-lg-14">
                    <select class="form-select" id="sendcc" name="sendcc">
                        <?php populate_select_from_table("EmailCC", $email['sendcc'], "", false); ?>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label" for="subject">Subject:</label>
                </div>
                <div class="col-md-16 col-lg-14">
                    <input class="form-control" id="subject" name="subject" type="text" size="40" value="<?php echo htmlspecialchars($email['subject'], ENT_NOQUOTES); ?>">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-16 col-lg-14">
                    <label class="form-label" for="body">Body:</label>
                    <textarea class="form-control" name="body" id="body" rows="25"><?php echo htmlspecialchars($email['boaalldy'], ENT_NOQUOTES); ?></textarea>
                </div>
            </div>
            <div class="mt-3 mb-3">
                <button class="btn btn-secondary" type="reset">Reset</button>
                <button class="btn btn-primary" type="submit" value="seeit">See it</button>
            </div>
        </form>
        <p>Available substitutions:</p>
        <table class="table table-sm w-auto">
            <tr><td>$BADGEID$</td><td>$EMAILADDR$</td></tr>
            <tr><td>$FIRSTNAME$</td><td>$PUBNAME$</td></tr>
            <tr><td>$LASTNAME$</td><td>$BADGENAME$</td></tr>
            <tr><td>$EVENTS_SCHEDULE$</td><td>$FULL_SCHEDULE$</td></tr>
        </table>
    </div>
    <?php
    staff_footer();
}

// function renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad)
//
function renderQueueEmail($goodCount, $arrayOfGood, $badCount, $arrayOfBad) {
    global $title;
    $title = "Results of Queueing Email";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title, 'bs5');
    ?>
    <div class="container-xl">
        <p><?php echo $goodCount; ?> message(s) were queued for email transmission.<br>
        <?php echo $badCount; ?> message(s) failed.</p>
        <p>List of messages successfully queued:<br>
        Badgeid, Name for Publications, Email Address<br>
        <?php if ($arrayOfGood) {
            foreach ($arrayOfGood as $recipient) {
                echo htmlspecialchars($recipient['badgeid']) . ", ";
                echo htmlspecialchars($recipient['name']) . ", ";
                echo htmlspecialchars($recipient['email']) . "<br>\n";
            }
        } ?>
        </p>
        <p>List of recipients which failed:<br>
        Badgeid, Name for Publications, Email Address<br>
        <?php if ($arrayOfBad) {
            foreach ($arrayOfBad as $recipient) {
                echo htmlspecialchars($recipient['badgeid']) . ", ";
                echo htmlspecialchars($recipient['name']) . ", ";
                echo htmlspecialchars($recipient['email']) . "<br>\n";
            }
        } ?>
        </p>
    </div>
    <?php
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
    staff_header($title, 'bs5');
    ?>
    <div class="container-xl">
        <?php if (strlen($message_warning) > 0) { ?>
            <p class="alert alert-warning"><?php echo $message_warning; ?></p>
        <?php } ?>
        <h3 class="mt-3">Step 2 -- Verify </h3>
        <form name="emailverifyform" method="POST" action="StaffSendEmailCompose.php">
            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label" for="recipient_list">Recipient List:</label>
                    <textarea class="form-control" id="recipient_list" readonly rows="8"><?php echo $email_verify['recipient_list']; ?></textarea>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label" for="verify_body">Rendering of message body to first recipient:</label>
                    <textarea class="form-control" id="verify_body" readonly rows="25" style="font-family: monospace, Monospaced;"><?php echo $email_verify['body']; ?></textarea>
                </div>
            </div>
            <input type="hidden" name="sendto" value="<?php echo $email['sendto']; ?>">
            <input type="hidden" name="sendfrom" value="<?php echo $email['sendfrom']; ?>">
            <input type="hidden" name="sendcc" value="<?php echo $email['sendcc']; ?>">
            <input type="hidden" name="subject" value="<?php echo htmlspecialchars($email['subject']); ?>">
            <input type="hidden" name="body" value="<?php echo htmlspecialchars($email['body']); ?>">
            <div class="mt-3 mb-3">
                <button class="btn btn-secondary" type="submit" name="navigate" value="goback">Go Back</button>
                <button class="btn btn-primary" type="submit" name="navigate" value="send">Send</button>
            </div>
        </form>
    </div>
    <?php
    staff_footer();
}

function render_send_email_engine($email, $message_warning) {
    global $title;
    $title = "Pretend to actually send email.";
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title, 'bs5');
    ?>
    <div class="container-xl">
        <?php if (strlen($message_warning) > 0) { ?>
            <p class="alert alert-warning"><?php echo $message_warning; ?></p>
        <?php } ?>
        <h3>Step 3 -- Actually Send Email </h3>
    </div>
    <?php
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
    $badgeidArr = array_map(function($str){global $linki;return "'" . mysqli_real_escape_string($linki, $str) . "'";}, array_column($recipientinfo, 'badgeid'));
    $badgeidList = implode(",", $badgeidArr);
    $query = <<<EOD
SELECT
        POS.badgeid, RM.roomname, S.title, DATE_FORMAT(ADDTIME('$ConStartDatim$', SCH.starttime),'%a %l:%i %p') as starttime,
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

// Function get_swift_mailer()
// Reads various parameters from configuration and returns a configured mailer object
// ready to send email
function get_swift_mailer() {
    // Local testing only: pretends every message was sent successfully, without making any network connection.
    if (defined('SMTP_USE_NULL_TRANSPORT') && SMTP_USE_NULL_TRANSPORT === TRUE) {
        return new Swift_Mailer(new Swift_NullTransport());
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

//Create the Mailer using the created Transport
    return new Swift_Mailer($transport);
}
?>
