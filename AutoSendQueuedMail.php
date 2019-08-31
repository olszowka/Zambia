#!/usr/local/bin/php -q
<?php
// Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
//require_once('email_functions.php');
//require_once('error_functions.php');
require_once('/home/zambia_admin/zambia.2012.arisiahosting.org/ASQMparams.php');
$logFile = fopen(EmailSpoolerLogFile, "a");
date_default_timezone_set('US/Eastern');
fwrite($logFile, "AutoSendQueuedMail launched: " . date("D M j G:i:s T Y") . "\n");
$linki = mysqli_connect(DBHOSTNAME, DBUSERID, DBPASSWORD);
if ($linki === false) {
    fwrite($logFile, "     Can't connect to MySQL host " . DBHOSTNAME . " using " . DBUSERID . "/" . DBPASSWORD . ".\n");
    fclose($logFile);
    exit();
}
if (!(mysqli_select_db($linki, DBDB))) {
    fwrite($logFile, "     Can't connect to DB " . DBDB . ".\n");
    fclose($logFile);
    exit();
}
$query = "SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body FROM EmailQueue ";
$query .= "WHERE status=1 ORDER BY emailtimestamp LIMIT 99";
if (!$result = mysqli_query($linki, $query)) {
    $message .= "Zambia: AutoSendQueuedMail: " . $query . " Error querying database.\n";
    //error_log($message);
    fwrite($logFile, "    " . $query . " Error querying database.\n");
    //RenderError($message);
    fclose($logFile);
    exit();
}
$rows = mysqli_num_rows($result);
fwrite($logFile, "    $rows row(s) retrieved by main SELECT query.\n");
if ($rows == 0) {
    fclose($logFile);
    exit();
}
$numGood = 0;
$numBad = 0;
for ($i = 0; $i < $rows; $i++) {
    $row = mysqli_fetch_array($result, MYSQLI_BOTH);
    $headers = "From: " . $row['emailfrom'] . "\r\nBCC: " . $row['emailcc'];
    if (mail($row['emailto'], $row['emailsubject'], $row['body'], $headers)) {
        //succeeded
        $goodList .= sprintf("%d,", $row['emailqueueid']);
        $numGood++;
    } else {
        //failed
        $badList .= sprintf("%d,", $row['emailqueueid']);
        $numBad++;
    }
}
$goodList = substr($goodList, 0, -1); //remove final trailing comma
$badList = substr($badList, 0, -1); //remove final trailing comma
fwrite($logFile, "    Num good: $numGood. Num bad: $numBad.\n");
fwrite($logFile, "    Good list: $goodList \n");
fwrite($logFile, "    Bad list: $badList \n");
if ($numGood > 0) {
    $query = "UPDATE EmailQueue SET status=2 WHERE emailqueueid in ($goodList)";
    if (!$result = mysqli_query($linki, $query)) {
        $message .= "Zambia: AutoSendQueuedMail: " . $query . " Error querying database.\n";
        error_log($message);
        fwrite($logFile, "    " . $query . " Error querying database.\n");
        fclose($logFile);
        exit();
    }
}
if ($numBad > 0) {
    $query = "UPDATE EmailQueue SET status=3 WHERE emailqueueid in ($badList)";
    if (!$result = mysqli_query($linki, $query)) {
        $message .= "Zambia: AutoSendQueuedMail: " . $query . " Error querying database.\n";
        error_log($message);
        fwrite($logFile, "    " . $query . " Error querying database.\n");
        fclose($logFile);
        exit();
    }
}
fwrite($logFile, "    Success\n");
fclose($logFile);
exit();
?>
