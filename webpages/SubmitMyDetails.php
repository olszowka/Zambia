<?php

global $linki, $participant, $message_error, $message2, $title;
$title = "Personal Details";
require ('PartCommonCode.php'); // initialize db; check login;
require_once('renderMyDetails.php');

if (!may_I('my_gen_int_write')) {
    $message = "Currently, you do not have write access to this page.\n";
    RenderError($message);
    exit();
}

$newrow = $_POST["newrow"];
$dayjob = stripslashes($_POST["dayjob"]);
$accessibilityissues = stripslashes($_POST["accessibilityissues"]);
$ethnicity = stripslashes($_POST["ethnicity"]);
$gender = stripslashes($_POST["gender"]);
$sexualorientation = stripslashes($_POST["sexualorientation"]);
$agerangeid = isset($_POST["agerangeid"]) ? $_POST["agerangeid"] : 1;
$pronounid = isset($_POST["pronounid"]) ? $_POST["pronounid"] : 1;
$pronounother = isset($_POST["pronounother"]) ? $_POST["pronounother"] : 1;

if ($newrow) {
    $query = "INSERT INTO ParticipantDetails SET badgeid=\"" . $badgeid;
    $query .= "\",dayjob=\"" . mysqli_real_escape_string($linki, $dayjob);
    $query .= "\",accessibilityissues=\"" . mysqli_real_escape_string($linki, $accessibilityissues);
    $query .= "\",ethnicity=\"" . mysqli_real_escape_string($linki, $ethnicity);
    $query .= "\",gender=\"" . mysqli_real_escape_string($linki, $gender);
    $query .= "\",sexualorientation=\"" . mysqli_real_escape_string($linki, $sexualorientation);
    $query .= "\",agerangeid=\"" . mysqli_real_escape_string($linki, $agerangeid);
    $query .= "\",pronounid=\"" . mysqli_real_escape_string($linki, $pronounid);
    $query .= "\",pronounother=\"" . mysqli_real_escape_string($linki, $pronounother)."\"";
    if (!mysqli_query($linki, $query)) {
        $message = $query . "<BR>Error inserting into database.  Database not updated.";
        RenderError($message);
        exit();
    }
} else {
    $query = "UPDATE ParticipantDetails SET ";
    $query .= "dayjob=\"" . mysqli_real_escape_string($linki, $dayjob) . "\",";
    $query .= "accessibilityissues=\"" . mysqli_real_escape_string($linki, $accessibilityissues) . "\",";
    $query .= "ethnicity=\"" . mysqli_real_escape_string($linki, $ethnicity) . "\",";
    $query .= "gender=\"" . mysqli_real_escape_string($linki, $gender) . "\",";
    $query .= "sexualorientation=\"" . mysqli_real_escape_string($linki, $sexualorientation) . "\",";
    $query .= "agerangeid=\"" . mysqli_real_escape_string($linki, $agerangeid) . "\",";
    $query .= "pronounid=\"" . mysqli_real_escape_string($linki, $pronounid) . "\",";
    $query .= "pronounother=\"" . mysqli_real_escape_string($linki, $pronounother) . "\" ";
    $query .= "WHERE badgeid=\"" . $badgeid . "\"";
    if (!mysqli_query($linki, $query)) {
        $message = $query . "<BR>Error updating database.  Database not updated.";
        RenderError($message);
        exit();
    }
}


$message = "Database updated successfully.";
$newrow = false;
$error = false;
renderMyDetails($title, $error, $message);

participant_footer();

exit(0);
?>
