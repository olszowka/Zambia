<?php
// This page has two completely different entry points from a user flow standpoint:
//   1) Beginning of send email flow -- start to specify parameters
//   2) After verify -- 'back' can change parameters -- 'send' fire off email sending code
require_once('email_functions.php');
require_once('db_functions.php');
require_once('render_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once(SWIFT_DIRECTORY."/swift_required.php");
global $title, $message, $link;
if (isset($_POST['sendto'])) { // page has been visited before
// restore previous values to form
        $email=get_email_from_post();
        }
    else { // page hasn't just been visited
        $email=set_email_defaults();
        }
$message_warning="";
if ($_POST['navigate']!='send') {
    render_send_email($email,$message_warning);
    exit(0);
    }
// put code to send email here.
// render_send_email_engine($email,$message_warning);
$title="Staff Send Email";
$subst_list=array("\$BADGEID\$","\$FIRSTNAME\$","\$LASTNAME\$","\$EMAILADDR\$","\$PUBNAME\$","\$BADGENAME\$");
$email=get_email_from_post();
//Create the Transport
$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -t -i');
//Create the Mailer using your created Transport
$mailer = Swift_Mailer::newInstance($transport);
//$swift =& new Swift(new Swift_Connection_SMTP(SMTP_ADDRESS)); // Is machine name of SMTP host defined in db_name.php
//$log =& Swift_LogContainer::getLog();
//$log->setLogLevel(0); // 0 is minimum logging; 4 is maximum logging
$query="SELECT emailtoquery FROM EmailTo where emailtoid=".$email['sendto'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailto=mysql_fetch_array($result,MYSQL_ASSOC);
$query=$emailto['emailtoquery'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$i=0;
while ($recipientinfo[$i]=mysql_fetch_array($result,MYSQL_ASSOC)) {
    $i++;
    }
$recipient_count=$i;
$query="SELECT emailfromaddress FROM EmailFrom where emailfromid=".$email['sendfrom'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailfrom=mysql_result($result,0);
$x=$email['sendcc'];
$query="SELECT emailaddress FROM EmailCC where emailccid=$x";
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailcc=mysql_result($result,0);
for ($i=0; $i<$recipient_count; $i++) {
    $ok=TRUE;
    //Create the message
    $message = Swift_Message::newInstance();
    $repl_list=array($recipientinfo[$i]['badgeid'],$recipientinfo[$i]['firstname'],$recipientinfo[$i]['lastname']);
    $repl_list=array_merge($repl_list,array($recipientinfo[$i]['email'],$recipientinfo[$i]['pubsname'],$recipientinfo[$i]['badgename']));
    $emailverify['body']=str_replace($subst_list,$repl_list,$email['body']);
    //Give the message a subject
        $message->setSubject($email['subject']);
    //Define from address
	$message->setFrom($emailfrom);
    //Define body
        $message->setBody($emailverify['body'],'text/plain');
    //$message =& new Swift_Message($email['subject'],$emailverify['body']);
    echo ($recipientinfo[$i]['pubsname']." - ".$recipientinfo[$i]['email'].": ");
    try {
        $message->addTo($recipientinfo[$i]['email']);
        }
    catch (Swift_SwiftException $e) {
        echo $e->getMessage()."<BR>\n";
	$ok=FALSE;
        }
    //$recipients =& new Swift_RecipientList();
    //$recipients->addTo($recipientinfo[$i]['email']); // define the To: field
    if ($emailcc!="") {
	$message->addBcc($emailcc);
        //$recipients->addBcc($emailcc); // define the BCC: field
        }
    //if ($swift->send($message, $recipients, $emailfrom)) {
    try {
        $mailer->send($message);
        }
    catch (Swift_SwiftException $e) {
        echo $e->getMessage()."<BR>\n";
	$ok=FALSE;
        }
    if ($ok==TRUE) {
            echo "Sent<BR>";
            }
    }
//$log =& Swift_LogContainer::getLog();
//echo $log->dump(true);
?>
