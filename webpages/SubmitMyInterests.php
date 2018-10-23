<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $congoinfo, $linki, $message2, $message_error, $participant, $title;
$title = "My Interests";
require('PartCommonCode.php'); // initialize db; check login;
require_once('renderMyInterests.php');
if (!may_I('my_gen_int_write')) {
    $message = "Currently, you do not have write access to this page.\n";
    RenderError($message);
    exit();
}
$rolerows = $_POST["rolerows"];
$newrow = $_POST["newrow"];
$yespanels = stripslashes($_POST["yespanels"]);
$nopanels = stripslashes($_POST["nopanels"]);
$yespeople = stripslashes($_POST["yespeople"]);
$nopeople = stripslashes($_POST["nopeople"]);
$otherroles = stripslashes($_POST["otherroles"]);
$rolearray = array();
for ($i = 0; $i < $rolerows-1; $i++) {
    $rolearray[$i] = array();
    if (isset($_POST["willdorole" . $i])) {
        $rolearray[$i]["badgeid"] = $badgeid;
    }
    $rolearray[$i]["roleid"] = $_POST["roleid" . $i];
    $rolearray[$i]["rolename"] = $_POST["rolename" . $i];
    $rolearray[$i]["diddorole"] = $_POST["diddorole" . $i];
}
if ($newrow) {
    $query = "INSERT INTO ParticipantInterests SET badgeid='$badgeid',";
    $query .= "yespanels=\"" . mysqli_real_escape_string($linki, $yespanels);
    $query .= "\",nopanels=\"" . mysqli_real_escape_string($linki, $nopanels);
    $query .= "\",yespeople=\"" . mysqli_real_escape_string($linki, $yespeople);
    $query .= "\",nopeople=\"" . mysqli_real_escape_string($linki, $nopeople);
    $query .= "\",otherroles=\"" . mysqli_real_escape_string($linki, $otherroles) . "\"";
    if (!mysqli_query($linki, $query)) {
        $message = $query . "<br>Error inserting into database.  Database not updated.";
        RenderError($message);
        exit();
    }
} else {
    $query = "UPDATE ParticipantInterests SET ";
    $query .= "yespanels=\"" . mysqli_real_escape_string($linki, $yespanels) . "\",";
    $query .= "nopanels=\"" . mysqli_real_escape_string($linki, $nopanels) . "\",";
    $query .= "yespeople=\"" . mysqli_real_escape_string($linki, $yespeople) . "\",";
    $query .= "nopeople=\"" . mysqli_real_escape_string($linki, $nopeople) . "\",";
    $query .= "otherroles=\"" . mysqli_real_escape_string($linki, $otherroles) . "\" ";
    $query .= "WHERE badgeid=\"" . $badgeid . "\"";
    if (!mysqli_query($linki, $query)) {
        $message = $query . "<br>Error updating database.  Database not updated.";
        RenderError($message);
        exit();
    }
}
for ($i = 0; $i < $rolerows-1; $i++) {
    if (isset($rolearray[$i]["badgeid"]) && ($rolearray[$i]["diddorole"] == 0)) {
        $query = "INSERT INTO ParticipantHasRole SET badgeid=\"" . $badgeid . "\", roleid=" . $rolearray[$i]["roleid"] . "";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br>Error inserting into database.  Database not updated.";
            RenderError($message);
            exit();
        }
    }
    if ((!isset($rolearray[$i]["badgeid"])) && ($rolearray[$i]["diddorole"] == 1)) {
        $query = "DELETE FROM ParticipantHasRole WHERE badgeid=\"" . $badgeid . "\" AND ";
        $query .= "roleid=" . $rolearray[$i]["roleid"];
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br>Error deleting from database.  Database not updated.";
            RenderError($message);
            exit();
        }
    }
}
$message = "Database updated successfully.";
$newrow = false;
$error = false;
renderMyInterests($title, $error, $message, $rolearray);
participant_footer();
exit(0);
?>        
