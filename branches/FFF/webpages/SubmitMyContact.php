<?php
$title="Update My Contact Info";

// initialize db, check login, set $badgeid from session
require_once('PartCommonCode.php');

// Pull the variables passed from the form
$password = $_POST['password'];
$cpassword = $_POST['cpassword'];
$interested = $_POST['interested'];
$wasinterested = $_POST['wasinterested'];
$bestway = $_POST['bestway'];
$wasbestway = $_POST['wasbestway'];
$pubsname = stripslashes($_POST['pubsname']);
$waspubsname = stripslashes($_POST['waspubsname']);
$bio = stripfancy(stripslashes($_POST['bio']));
$progbio = stripfancy(stripslashes($_POST['progbio']));
$wasbio = stripfancy(stripslashes($_POST['wasbio']));
$wasprogbio = stripfancy(stripslashes($_POST['wasprogbio']));

// If the submitted bio is too long, reject it
if (strlen($bio)>MAX_BIO_LEN) {
  $message_error="Web Biography is too long: ".(strlen($bio))." characters.  Please edit.  Database not updated.";
  require('my_contact.php');
}

// If the submitted program book bio is too long, reject it
if (strlen($progbio)>MAX_PROG_BIO_LEN) {
  $message_error="Program Book Biography is too long: ".(strlen($progbio))." characters.  Please edit.  Database not updated.";
  require('my_contact.php');
}

// if the passwords are there, and don't match, reject it
if ($password=="" and $cpassword=="") {
  $update_password=false;
} elseif ($password==$cpassword) {
  $update_password=true;
} else {
  $message_error="Passwords do not match each other.  Database not updated.";
  require('my_contact.php');
}

// if the pubsname, bio, or progbio was changed, and bio editing is currently disallowed, reject it
if ((($waspubsname!=$pubsname) or ($wasbio!=$bio) or ($wasprogbio!=$progbio)) and (!may_I('EditBio'))) {
  if (!may_I('EditBio')) { //Don't have permission to change pubsname
    $message_error="You may not update your name for publication at this time.\n";
    require('my_contact.php');
  }
}

// Begin the query
$query_start="UPDATE Participants SET ";
$query="";

// Add password
if ($update_password==true) {
  $x = md5($password);
  if ($query!="") {$query.=", ";}
  $query.="password=\"$x\"";
  $_SESSION['password']=$x;
}

// Add pubsname and update the session variable
if ($waspubsname!=$pubsname) {
  $x=mysql_real_escape_string($pubsname,$link);
  if ($query!="") {$query.=", ";}
  $query=$query."pubsname=\"$x\"";
  $_SESSION['badgename']=$x;
}

// Add bestway
if ($wasbestway!=$bestway) {
  if ($query!="") {$query.=", ";}
  $query.="bestway=\"$bestway\"";
}

// Add interested
if ($wasinterested!=$interested) {
  if ($query!="") {$query.=", ";}
  $query.="interested=\"$interested\"";
}

// Add bio
if ($wasbio!=$bio) {
  $x=mysql_real_escape_string($bio,$link);
  if ($query!="") {$query.=", ";}
  $query.="bio=\"$x\"";
}

// Add progbio
if ($wasprogbio!=$progbio) {
  $x=mysql_real_escape_string($progbio,$link);
  if ($query!="") {$query.=", ";}
  $query.="progbio=\"$x\"";
}

// Check to see if we are actually doing anything
if ($query!="") {
  $query_start.=$query;
  $query=$query_start;
  $query.=" WHERE badgeid=\"".$badgeid."\"";
  if (!mysql_query($query,$link)) {
    $message=$query."<BR>Error updating database.  Database not updated.";
    RenderError($title,$message);
    exit();
  }
  $message="Database updated successfully.";
}
require('my_contact.php');
?>
