<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $message_error, $title;
require('PartCommonCode.php'); // initialize db; check login;
function update_participant($badgeid) {
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
    $update_arr = array();
    $updateStr = "s";
    $query_end = " WHERE badgeid = ?;";
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
            array_push($update_arr, $pubsname);
            $updateStr .= "s";
            $updateClause .= "pubsname=?, ";
        } else {
            $message_error = "You may not update your name for publications at this time.  Database not updated.";
            RenderErrorAjax($message_error);
            exit();
        }
    if (isset($_POST['htmlbio']))
        if ($may_edit_bio) {
            $updateClause .= "htmlbio=?, bio=?, ";
            array_push($update_arr, $_POST['htmlbio']);
            array_push($update_arr, strip_tags($_POST['htmlbio']));
            $updateStr .= "ss";
        } else {
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
    $congo_arr = array();
    $congoUpdateClause = "";
    $congoStr = "s";
    if (isset($_POST['firstname'])) {
        $congoUpdateClause .= "firstname=?, ";
        array_push($congo_arr, stripslashes($_POST['firstname']));
        $congoStr .= "s";
    }
    if (isset($_POST['lastname'])) {
        $congoUpdateClause .= "lastname=?, ";
        array_push($congo_arr, stripslashes($_POST['lastname']));
        $congoStr .= "s";
    }
    if (isset($_POST['badgename'])) {
        $congoUpdateClause .= "badgename=?, ";
        array_push($congo_arr, stripslashes($_POST['badgename']));
        $congoStr .= "s";
    }
    if (isset($_POST['phone'])) {
        $congoUpdateClause .= "phone=?, ";
        array_push($congo_arr, stripslashes($_POST['phone']));
        $congoStr .= "s";
    }
    if (isset($_POST['email'])) {
        $congoUpdateClause .= "email=?, ";
        array_push($congo_arr, stripslashes($_POST['email']));
        $congoStr .= "s";
    }
    if (isset($_POST['postaddress1'])) {
        $congoUpdateClause .= "postaddress1=?, ";
        array_push($congo_arr, stripslashes($_POST['postaddress1']));
        $congoStr .= "s";
    }
    if (isset($_POST['postaddress2'])) {
        $congoUpdateClause .= "postaddress2=? ,";
        array_push($congo_arr, stripslashes($_POST['postaddress2']));
        $congoStr .= "s";
    }
    if (isset($_POST['postcity'])) {
        $congoUpdateClause .= "postcity=?, ";
        array_push($congo_arr, stripslashes($_POST['postcity']));
        $congoStr .= "s";
    }
    if (isset($_POST['poststate'])) {
        $congoUpdateClause .= "poststate=?, ";
        array_push($congo_arr, stripslashes($_POST['poststate']));
        $congoStr .= "s";
    }
    if (isset($_POST['postzip'])) {
        $congoUpdateClause .= "postzip=?, ";
        array_push($congo_arr, stripslashes($_POST['postzip']));
        $congoStr .= "s";
    }
    if (isset($_POST['postcountry'])) {
        $congoUpdateClause .= "postcountry=?, ";
        array_push($congo_arr, stripslashes($_POST['postcountry']));
        $congoStr .= "s";
    }

    if (!$updateClause && !$valuesClause2 && !$credentialClause3 && !$congoUpdateClause) {
        $message_error = "No data found to change.  Database not updated.";
        RenderErrorAjax($message_error);
        exit();
    }
    if (USE_REG_SYSTEM === FALSE) {
        if ($congoUpdateClause) {
            $sqlq = "INSERT INTO CongoDumpHistory (
            badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,
            postcity,poststate,postzip,postcountry,regtype,loginid
            )
            SELECT badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,
                postcity,poststate,postzip,postcountry,regtype,'" .
                mysqli_real_escape_string($linki, stripslashes($badgeid)) . "'
            FROM CongoDump " . $query_end;

            $sql_array = array($badgeid);
            // echo $sqlq . "<br>'" . join("', '", $sql_array) . "'<br><br>";

            $rows = mysql_cmd_with_prepare($sqlq, "s", $sql_array);
            if (is_null($rows)) {
                $message_error = "Failed adding registration history record, seek assistance.";
                RenderErrorAjax($message_error);
                exit();
            } else if ($rows < 0) {
                $message_error = "Failed adding registration history record, seek assistance.";
                RenderErrorAjax($message_error);
                exit();
            }

            $sqlq= $query4 . mb_substr($congoUpdateClause, 0, -2) . $query_end;
            array_push($congo_arr, $badgeid);
            // echo $sqlq . "<br>'" . join("', '", $congo_arr) . "'<br>" . $congoStr . "<br><br>";

            $rows = mysql_cmd_with_prepare($sqlq, $congoStr, $congo_arr);
            if (is_null($rows)) {
                $message_error = "Null-Failed updating registration record, seek assistance.";
                RenderErrorAjax($message_error);
                exit();
            } else if ($rows < 0) {
                $message_error = strval($rows) . ": " . "Failed updating registration record, seek assistance.";
                RenderErrorAjax($message_error);
                exit();
            }
        }
    }
    if ($updateClause) {
        $sqlq = $query . mb_substr($updateClause, 0, -2) . $query_end;
        array_push($update_arr, $badgeid);
        // echo $sqlq . "<br>'" . join("', '", $update_arr) . "'<br>" . $updateStr . "<br><br>";
        $rows = mysql_cmd_with_prepare($sqlq, $updateStr, $update_arr);
        if (is_null($rows)) {
            $message_error = "Failed updating participant record, seek assistance.";
            RenderErrorAjax($message_error);
            exit();
        } else if ($rows < 0) {
            $message_error = "Failed updating participant record, seek assistance.";
            RenderErrorAjax($message_error);
            exit();
        }
    }
    if ($valuesClause2) {
        $sqlq= $query2 . $valuesClause2;
        // echo $sqlq . "<br>";
        mysqli_query_with_error_handling($query2 . $valuesClause2, true, true);
    }
    if ($credentialClause3) {
        $sqlq = $query3 . $credentialClause3 . ")";
        // echo $sqlq . "<br>";
        mysqli_query_with_error_handling($query3 . $credentialClause3 . ")", true, true);
    }
    echo("<span class=\"alert alert-success\">");
    if (!empty($password)) {
        echo "Password updated. ";
        $_SESSION['hashedPassword'] = $hashedPassword;
    }

    echo("Database updated successfully. </span>\n");
    if ($pubsname)
        $_SESSION['badgename'] = $pubsname;
    exit();
}
// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "") {
    exit();
}
//error_log("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action");

function fetch_bio($badgeid) {
    $result = mysqli_query_with_prepare_and_exit_on_error("SELECT P.bio FROM Participants P WHERE P.badgeid=?;", "s", array($badgeid));
    $xml = mysql_result_to_XML("fetchbio", $result);
    if (!$xml) {
        RenderErrorAjax($message_error);
        exit();
    }
    header("Content-Type: text/xml");
    echo($xml->saveXML());
    exit();
}

$badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
switch ($ajax_request_action) {
    case "fetch_bio":
        fetch_bio($badgeid);
        break;
    case "update_participant":
        update_participant($badgeid);
        break;
    default:
        exit();
}
?>
