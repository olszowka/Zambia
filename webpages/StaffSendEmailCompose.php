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
// Queue email to be sent into db.  Cron job will actually send it at a pace not to trigger outgoing spam filters. 
$title="Staff Send Email";
$subst_list=array("\$BADGEID\$","\$FIRSTNAME\$","\$LASTNAME\$","\$EMAILADDR\$","\$PUBNAME\$","\$BADGENAME\$");
$email=get_email_from_post();
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
$goodCount=0;
$badCount=0;
unset($arrayOfGood);
unset($arrayOfBad);
for ($i=0; $i<$recipient_count; $i++) {
    $name=(strlen($recipientinfo[$i]['pubsname'])>0)?$recipientinfo[$i]['pubsname']:$recipientinfo[$i]['firstname']." ".$recipientinfo[$i]['lastname'];
    if (!filter_var($recipientinfo[$i]['email'],FILTER_VALIDATE_EMAIL)) {
             // bad email address
             $badCount++;
             $arrayOfBad[]=array('badgeid'=>$recipientinfo[$i]['badgeid'],'name'=>$name,'email'=>$recipientinfo[$i]['email']);
             }
        else {
             $goodCount++;
             $arrayOfGood[]=array('badgeid'=>$recipientinfo[$i]['badgeid'],'name'=>$name,'email'=>$recipientinfo[$i]['email']);
             $repl_list=array($recipientinfo[$i]['badgeid'],$recipientinfo[$i]['firstname'],$recipientinfo[$i]['lastname']);
             $repl_list=array_merge($repl_list,array($recipientinfo[$i]['email'],$recipientinfo[$i]['pubsname'],$recipientinfo[$i]['badgename']));
             $emailverify['body']=str_replace($subst_list,$repl_list,$email['body']);
             $query="INSERT INTO EmailQueue (emailqueueid, emailto, emailfrom, emailcc, emailsubject, body, status) ";
             // to address
             $query.="values(null, \"".mysql_real_escape_string($recipientinfo[$i]['email'],$link)."\",";
             // from address
             $query.="\"".mysql_real_escape_string($emailfrom,$link)."\",";
             // cc (bcc) address
             $query.="\"".mysql_real_escape_string($emailcc,$link)."\",";
             // subject
             $query.="\"".mysql_real_escape_string($email['subject'],$link)."\",";
             // body
             $query.="\"".mysql_real_escape_string(wordwrap(preg_replace("/(?<!\\r)\\n/","\r\n",$emailverify['body']),70,"\r\n"),$link)."\",";
             // status 1 is unsent (queued)
             $query.="1);";
             if (!$result=mysql_query($query,$link)) {
                 db_error($title,$query,$staff=true); // outputs messages regarding db error
                 exit(0);
                 }
             }
    }   
renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad);
?>
