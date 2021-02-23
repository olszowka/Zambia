#!/usr/local/bin/php -q
<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
error_reporting(E_ERROR);
require_once('webpages/db_functions.php'); //reset connection to db and check if logged in
require_once('webpages/email_functions.php');
require_once('webpages/external/swiftmailer-5.4.8/lib/swift_required.php');
if (prepare_db_and_more() === false) {
	echo "Unable to connect to database.\nNo further execution possible.\n";
	exit(1);
};
$now = new DateTime();
echo "Queue run started at " . $now->format('Y-m-d H:m:i') . "\n";

$mailer = get_swift_mailer();
//$logger = new Swift_Plugins_Loggers_EchoLogger();
//$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

$sql = "SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body, status, emailtimestamp FROM EmailQueue;";
$result = mysqli_query_exit_on_error($sql);
while($row = mysqli_fetch_assoc($result)) {
    $action = "";
    $queueid = $row["emailqueueid"];
	echo "checking: $queueid\n";
	$qdate = new DateTime($row["emailtimestamp"]);

	if ($qdate->diff($now)->days > 7) {
        echo "Message too old, deleting, queued " . $row["emailtimestamp"] . " which is " . $qdate->diff($now)->days . " days old\n";
        $action = "delete";
    } else {
		echo "Retrying\n";
		$message = (new Swift_Message($row['emailsubject']));
		$message->setFrom($row["emailfrom"]);
		if ($row["emailcc"] != "") {
            $message->addBcc($emailcc);
        }
		$message->setBody($row["body"],'text/plain');
        $emailto = $row["emailto"];
        try {
            $message->addTo($emailto);
            try {
                $code = 0;
                $sendMailResult = $mailer->send($message);
            }
            catch (Swift_TransportException $e) {
                $msg = $e->getMessage();
                $code = $e->getCode();
                if ($code < 500) {
                    $action = "leave";
                    error_log("Swift transport exception: $msg; send email failed, leaving in queue.");
                } else {
                    $action = "delete";
                    error_log("Swift transport exception: $msg; send email failed, deleting from queue.");
                }
            }
            catch (Swift_SwiftException $e) {
                $msg = $e->getMessage();
                $code = $e->getCode();
                if ($code < 500) {
                    $action = "leave";
                    error_log("Swift transport exception: $msg; send email failed, leaving in queue.");
                } else {
                    $action = "delete";
                    error_log("Swift transport exception: $msg; send email failed, deleting from queue.");
                }
            }
        }
        catch (Swift_SwiftException $e) {
            error_log("Email address $emailto failed, invalid, dropped from queue.");
            echo "$email is invalid\n";
            $action = "delete";
        }
        if ($action == "leave") {
            echo "failed, saved for next retry\n";
        } else {
            $action = "delete";
            echo "Delivered\n";
        }
    }

    if ($action == "delete") {
        $sql = "DELETE FROM EmailQueue WHERE emailqueueid = ?;";
        $param_arr = array($queueid);
        $types = "i";
        $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
    }
    if ($action == "leave") {
        $sql = "UPDATE EmailQueue set status = ? WHERE emailqueueid = ?;";
        $param_arr = array($code, $queueid);
        $types = "ii";
        $rows = mysql_cmd_with_prepare($sql, $types, $param_arr);
    }
}

mysqli_free_result($result);
$now = new DateTime();
echo "Queue run complete at " . $now->format('Y-m-d H:m:i') . "\n";
exit(0);
?>