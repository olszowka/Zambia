<?php
require_once('CommonCode.php');
$_SESSION['role'] = "Participant";
$badgeid=$_SESSION['badgeid'];
if (!(may_I("Participant"))) {
  $message="You are not authorized to access this page.";
  require ('login.php');
  exit();
};
?>
