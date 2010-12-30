<?php
// Not sure if there is any need to support post/been here before
require_once('email_functions.php');
require_once('db_functions.php');
require_once('render_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
global $message,$link;
$subst_list=array("\$BADGEID\$","\$FIRSTNAME\$","\$LASTNAME\$","\$EMAILADDR\$","\$PUBNAME\$","\$BADGENAME\$");
$title="Send Email (Step 2 - verify)";
if (!isset($_POST['sendto'])) { // page has not been visited before
    $message_error="Expected POST data was missing.  This page is intended to be reached via a form.";
    $message_error.=" It will not work if you link to it directly.\n";
    StaffRenderError ($title, $message_error);
    exit(0);
    }
$email=get_email_from_post();
if (!validate_email($email)) {
    render_send_email($email,$message); // $message came from validate_email
    exit(0);
    }
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
$emailverify['recipient_list']="";
for ($i=0; $i<$recipient_count; $i++) {
    $emailverify['recipient_list'].=$recipientinfo[$i]['pubsname']." - ";
    $emailverify['recipient_list'].=htmlspecialchars($recipientinfo[$i]['email'],ENT_NOQUOTES)."\n";
    }
$query="SELECT emailfromaddress FROM EmailFrom where emailfromid=".$email['sendfrom'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailverify['emailfrom']=mysql_result($result,0);
$repl_list=array($recipientinfo[0]['badgeid'],$recipientinfo[0]['firstname'],$recipientinfo[0]['lastname']);
$repl_list=array_merge($repl_list,array($recipientinfo[0]['email'],$recipientinfo[0]['pubsname'],$recipientinfo[0]['badgename']));
$emailverify['body']=str_replace($subst_list,$repl_list,$email['body']);
render_verify_email($email,$emailverify,$message_warning="");
?>
