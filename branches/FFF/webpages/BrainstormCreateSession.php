<?php
require_once ('BrainstormCommonCode.php');
require_once ('RenderEditCreateSession.php');
global $email, $name, $badgeid, $message_error, $message2;
$_SESSION['return_to_page']="BrainstormCreateSession.php";
get_name_and_email($name, $email);
// error_log("badgeid: $badgeid; name: $name; email: $email"); // for debugging only
$message_error="";
$message_warn="";
set_session_defaults();
if (!(may_I('Participant')||may_I('Staff'))) { // must be brainstorm user
  $session["status"]=1; // brainstorm
}
$id=get_next_session_id();
if (!$id) { exit(); }
$session["sessionid"]=$id;
$action="brainstorm";
RenderEditCreateSession($action,$session,$message_warn,$message_error);
exit();
?>
