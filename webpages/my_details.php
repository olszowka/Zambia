<?php
global $linki,$participant,$message_error,$message2,$reginfo;
$title="Personal Details";
require ('PartCommonCode.php'); // initialize db; check login;
require_once('ParticipantHeader.php');
require_once('renderMyDetails.php');
// set $badgeid from session
$query = "SELECT * FROM ParticipantDetails WHERE badgeid='$badgeid'";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}

$rows=mysqli_num_rows($result);
if ($rows > 1) {
    $message = $query . "<br>Multiple rows returned from database where one expected. Unable to continue.";
    RenderError($message);
    exit();
}
if ($rows==0) {
    $dayjob="";
    $accessibilityissues=""; 
    $ethnicity="";
    $gender="";
    $sexualorientation="";
    $agerangeid=1;
    $pronounid=1;
    $pronounother="";
    $newrow=true;
} else {
    list($foo, $dayjob, $accessibilityissues, $ethnicity, $gender, $sexualorientation, $agerangeid, $pronounid, $pronounother)=mysqli_fetch_array($result, MYSQLI_NUM);
    $newrow=false;
}
mysqli_free_result($result);

$error=false;
$message="";
renderMyDetails($title, $error, $message);
participant_footer();
?>
