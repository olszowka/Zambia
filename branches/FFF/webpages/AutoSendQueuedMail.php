<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
require_once('email_functions.php');
require_once('CGICommonCode.php');
require_once('error_functions.php');
require_once('StaffCommonCode.php');

// For test verbose=1, otherwise for silent, verbose='';
$verbose='';

if ($verbose) {staff_header("Auto Send Queued Mail");}

$query="SELECT emailqueueid, emailto, emailfrom, emailcc, emailsubject, body from EmailQueue ";
$query.="WHERE status=1 ORDER BY emailtimestamp LIMIT 99";
if ($verbose) {echo "<P>EmailQuery Query: $query</P>\n";}
if (!$result=mysql_query($query,$link)) {
    $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
    error_log($message);
    //RenderError($title,$message);
    exit();
    }
$rows=mysql_num_rows($result);
if ($rows==0) exit();
$numGood=0;
$numBad=0;
for ($i=0; $i<$rows; $i++) {
    $row=mysql_fetch_array($result, MYSQL_BOTH);
    $subject=$row['emailsubject'];
    $from=$row['emailfrom'];
    $cc=$row['emailcc'];
    $to=$row['emailto'];
    $body=$row['body'];
    $headers="From: $from\r\nCC: $cc";
    /* Genarlize this, so that if the php "mail" command will work, use that
       otherwise use mutt or exim or ... other mailers as they work
    if (mail($row['emailto'],$row['emailsubject'],$row['body'],$headers)) {
    */
    if ($verbose) {
      echo "<P>Email From: $from</P>\n<P>To: $to</P>\n<P>CC: $cc</P>\n";
      echo "<P>Subject: $subject</P>\n<P>Body: $body</P>\n<P>Headers: $headers</P>\n";
    }
    // For mutt $mailstring, and shell_exec, for php mail use the above if (mail ...
    $mailstring="echo -e \"".$body."\" | /usr/bin/mutt -s '".$subject."' -f /dev/null";
    $mailstring.=" -e 'set copy=no' -e 'set from = \"".$from."\"' -c '".$cc."' \"".$to."\"";
    $mailstring.=" > /dev/null ; echo $?";
    if ($verbose) {echo "<P><PRE>$mailstring</PRE></P>\n";}
    if (shell_exec("$mailstring")) {
            //succeeded
            $goodList.=sprintf("%d,",$row['emailqueueid']);
            $numGood++;
            }
        else {
            //failed
            $badList.=sprintf("%d,",$row['emailqueueid']);
            $numBad++;
            } 
    }
$goodList=substr($goodList,0,-1); //remove final trailing comma
$badList=substr($badList,0,-1); //remove final trailing comma
if ($verbose) {echo "Num good: $numGood. Num bad: $numBad.<BR>\n";}
if ($verbose) {echo "Good list: $goodList <BR>\n";}
if ($verbose) {echo "Bad list: $badList <BR>\n";}
if ($numGood>0) {
    $query="UPDATE EmailQueue SET status=2 WHERE emailqueueid in ($goodList)";
    if (!$result=mysql_query($query,$link)) {
        $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
        error_log($message);
        //RenderError($title,$message);
        exit();
        }
    }
if ($numBad>0) {
    $query="UPDATE EmailQueue SET status=3 WHERE emailqueueid in ($badList)";
    if (!$result=mysql_query($query,$link)) {
        $message.="Zambia: AutoSendQueuedMail: ".$query." Error querying database.\n";
        error_log($message);
        //RenderError($title,$message);
        exit();
        }
    }
exit();
?>
