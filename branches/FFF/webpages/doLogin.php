<?php
$logging_in=true;
require_once ('CommonCode.php');
require_once ('error_functions.php');

$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$title="Submit Password";
$badgeid = $_POST['badgeid'];
$password = stripslashes($_POST['passwd']);
$conid = CON_KEY; // Temporary measure until passed in from login/index.php
$target = $_POST['target'];


// echo "Trying to connect to database.\n";
if (prepare_db()===false) {
  $message_error="Unable to connect to database.<BR>No further execution possible.";
  RenderError($title,$message_error);
  exit();
};
// echo "Connected to database.\n";

//Badgid test
$result=mysql_query("Select password from $ReportDB.Participants where badgeid='".$badgeid."'",$link);
if (!$result) {
  $message="Incorrect BadgeID or Password - please be aware that BadgeID and Password are case sensitive and try again.";
  require ('login.php');
  exit();
}

// Password check
$dbobject=mysql_fetch_object($result);
$dbpassword=$dbobject->password;
//echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
//exit(0);
if (md5($password)!=$dbpassword) {
  $message="Incorrect BadgeID or Password - please be aware that BadgeID and Password are case sensitive and try again.";
  require ('login.php');
  exit(0);
}

// Get and set information on individual
$result=mysql_query("Select badgename from $ReportDB.CongoDump where badgeid='".$badgeid."'",$link);
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $badgename=$dbobject->badgename;
  $_SESSION['badgename']=$badgename;
} else {
  $_SESSION['badgename']="";
}
$result=mysql_query("Select pubsname from $ReportDB.Participants where badgeid='".$badgeid."'",$link);
$pubsname="";
if ($result) {
  $dbobject=mysql_fetch_object($result);
  $pubsname=$dbobject->pubsname;
}
if (!($pubsname=="")) {
  $_SESSION['badgename']=$pubsname;
}
$_SESSION['badgeid']=$badgeid;
$_SESSION['password']=$dbpassword;
$_SESSION['conid']=$conid;
set_permission_set($badgeid);
//error_log("Zambia: Completed set_permission_set.\n");

$message2="";

// Switch on which page is shown
if (retrieve_participant_from_db($badgeid)==0) {
  if(may_I('Staff')) {
    require ('StaffPage.php');
  } elseif ((may_I('Vendor')) or ((may_I('public_login')) and ($target=="vendor"))) {
    require ('renderVendorWelcome.php');
  } elseif (may_I('Participant')) {
    require ('renderWelcome.php');
  } elseif (may_I('public_login')) {
    require ('BrainstormWelcome.php');
  } else {
    $message_error.=print_r($_SESSION);
    $message_error.="There is a problem with your userid's permission configuration:  It doesn't have ";
    $message_error.="permission to access any welcome page.  Please contact Zambia staff.";
    RenderError($title,$message_error);
  }
  exit();
}

// Fail to get db information somewhere ...
$message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
RenderError($title,$message_error);
exit();
?>
