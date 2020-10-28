<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $message_error, $title;
$title = "My Profile";
require('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
$pubsname = false;
//$foo = print_r($_POST,true);
//echo(preg_replace("/\n/","<BR>",$foo));
//exit;
if (($x = $_POST['ajax_request_action']) != "update_participant") {
    $message_error = "Invalid ajax_request_action: $x.  Database not updated.";
    RenderErrorAjax($message_error);
    exit();
}
$may_edit_bio = may_I('EditBio');
$query = "UPDATE Participants SET ";
$updateClause = "";
$query_end = " WHERE badgeid = '$badgeid';";
if (isset($_POST['interested'])) {
    $x = $_POST['interested'];
    if ($x == 1 || $x == 2)
        $updateClause .= "interested=$x, ";
    else
        $updateClause .= "interested=0, ";
}
if (isset($_POST['share_email'])) {
    $x = $_POST['share_email'];
    if ($x == 0 || $x == 1)
        $updateClause .= "share_email=$x, ";
    else
        $updateClause .= "share_email=null, ";
}
if (isset($_POST['use_photo'])) {
    $x = $_POST['use_photo'];
    if ($x == 0 || $x == 1)
        $updateClause .= "use_photo=$x, ";
    else
        $updateClause .= "use_photo=null, ";
}
if (isset($_POST['bestway'])) {
    $x = $_POST['bestway'];
    if ($x == "Email" || $x == "Postal mail" || $x == "Phone")
        $updateClause .= "bestway=\"$x\", ";
    else {
        $message_error = "Invalid value for bestway: $x.  Database not updated.";
        RenderErrorAjax($message_error);
        exit();
    }
}
$password = getString('password');
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $updateClause .= "password=\"$hashedPassword\", ";
}
if (isset($_POST['pubsname']))
    if ($may_edit_bio) {
        $pubsname = stripslashes($_POST['pubsname']);
        $updateClause .= "pubsname=\"" . mysqli_real_escape_string($linki, $pubsname) . "\", ";
    } else {
        $message_error = "You may not update your name for publications at this time.  Database not updated.";
        RenderErrorAjax($message_error);
        exit();
    }
if (isset($_POST['bioText']))
    if ($may_edit_bio)
        $updateClause .= "bio=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['bioText'])) . "\", ";
    else {
        $message_error = "You may not update your biography at this time.  Database not updated.";
        RenderErrorAjax($message_error);
        exit();
    }
$query2 = "REPLACE ParticipantHasCredential (badgeid, credentialid) VALUES ";
$valuesClause2 = "";
$query3 = "DELETE FROM ParticipantHasCredential WHERE badgeid = '$badgeid' AND credentialid in (";
$credentialClause3 = "";
foreach ($_POST as $name => $value) {
    if (mb_substr($name, 0, 13) != "credentialCHK")
        continue;
    $ccid = mb_substr($name, 13);
    switch ($value) {
        case "true":
            $valuesClause2 .= ($valuesClause2 ? ", " : "") . "('$badgeid', $ccid)";
            break;
        case "false":
            $credentialClause3 .= ($credentialClause3 ? ", " : "") . $ccid;
            break;
        default:
            $message_error = "Invalid value for $name: $value.  Database not updated.";
            RenderErrorAjax($message_error);
            exit();
            break;
    }
}
$query4 = "UPDATE CongoDump SET ";
$congoUpdateClause = "";
if (isset($_POST['firstname'])) {
    $congoUpdateClause .= "firstname=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['firstname'])) . "\", ";
}
if (isset($_POST['lastname'])) {
    $congoUpdateClause .= "lastname=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['lastname'])) . "\", ";
}
if (isset($_POST['badgename'])) {
    $congoUpdateClause .= "badgename=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['badgename'])) . "\", ";
}
if (isset($_POST['phone'])) {
    $congoUpdateClause .= "phone=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['phone'])) . "\", ";
}
if (isset($_POST['email'])) {
    $congoUpdateClause .= "email=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['email'])) . "\", ";
}
if (isset($_POST['postaddress1'])) {
    $congoUpdateClause .= "postaddress1=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['postaddress1'])) . "\", ";
}
if (isset($_POST['postaddress2'])) {
    $congoUpdateClause .= "postaddress2=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['postaddress2'])) . "\", ";
}
if (isset($_POST['postcity'])) {
    $congoUpdateClause .= "postcity=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['postcity'])) . "\", ";
}
if (isset($_POST['poststate'])) {
    $congoUpdateClause .= "poststate=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['poststate'])) . "\", ";
}
if (isset($_POST['postzip'])) {
    $congoUpdateClause .= "postzip=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['postzip'])) . "\", ";
}
if (isset($_POST['postcountry'])) {
    $congoUpdateClause .= "postcountry=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['postcountry'])) . "\", ";
}

if (!$updateClause && !$valuesClause2 && !$credentialClause3 && !$congoUpdateClause) {
    $message_error = "No data found to change.  Database not updated.";
    RenderErrorAjax($message_error);
    exit();
}
if ($congoUpdateClause) {
    mysqli_query_with_error_handling(($query4 . mb_substr($congoUpdateClause, 0, -2) . $query_end), true, true);
}
if ($updateClause) {
    mysqli_query_with_error_handling(($query . mb_substr($updateClause, 0, -2) . $query_end), true, true);
}
if ($valuesClause2) {
    mysqli_query_with_error_handling($query2 . $valuesClause2, true, true);
}
if ($credentialClause3) {
    mysqli_query_with_error_handling($query3 . $credentialClause3 . ")", true, true);
}
echo("<span class=\"alert alert-success\">");
if (!empty($password)) {
    echo "Password updated. ";
    $_SESSION['hashedPassword'] = $hashedPassword;
}
if (USE_REG_SYSTEM === FALSE) {

}
echo("Database updated successfully. </span>\n");
if ($pubsname)
    $_SESSION['badgename'] = $pubsname;
exit();
?>
