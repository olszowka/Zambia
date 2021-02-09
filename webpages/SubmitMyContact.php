<?php
// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;
$title = "My Profile";
require('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
$returnAjaxErrors = true;
$return500errors = true;


function updateparticipant() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title, $badgeid;
    $pubsname = false;
    $CongoDumpUpdated = false;
    $ParticipantsUpdated = false;
    $may_edit_bio = may_I('EditBio');
    $query = "UPDATE Participants SET ";
    $updateClause = "";
    $query_end = " WHERE badgeid = '$badgeid';";
    if (isset($_POST['interested'])) {
        $x = $_POST['interested'];
        if ($x == 1 || $x == 2) {
            $updateClause .= "interested=$x, ";
        } else {
            $updateClause .= "interested=0, ";
        }
    }
    if (isset($_POST['share_email'])) {
        $x = $_POST['share_email'];
        if ($x == 0 || $x == 1) {
            $updateClause .= "share_email=$x, ";
        } else {
            $updateClause .= "share_email=null, ";
        }
    }
    if (isset($_POST['use_photo'])) {
        $x = $_POST['use_photo'];
        if ($x == 0 || $x == 1) {
            $updateClause .= "use_photo=$x, ";
        } else {
            $updateClause .= "use_photo=null, ";
        }
    }
    if (isset($_POST['bestway'])) {
        $x = $_POST['bestway'];
        if ($x == "Email" || $x == "Postal mail" || $x == "Phone") {
            $updateClause .= "bestway=\"$x\", ";
        } else {
            $message_error = "Invalid value for bestway: $x.  Database not updated.";
            Render500ErrorAjax($message_error);
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
            Render500ErrorAjax($message_error);
            exit();
        }
    if (isset($_POST['bio']))
        if ($may_edit_bio) {
            $updateClause .= "bio=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['bio'])) . "\", ";
        } else {
            $message_error = "You may not update your biography at this time.  Database not updated.";
            Render500ErrorAjax($message_error);
            exit();
        }
    $query2 = "REPLACE ParticipantHasCredential (badgeid, credentialid) VALUES ";
    $valuesClause2 = "";
    $query3 = "DELETE FROM ParticipantHasCredential WHERE badgeid = $badgeid AND credentialid in (";
    $credentialClause3 = "";
    foreach ($_POST as $name => $value) {
        if (mb_substr($name, 0, 13) != "credentialCHK") {
            continue;
        }
        $ccid = mb_substr($name, 13);
        switch ($value) {
            case "1":
                $valuesClause2 .= ($valuesClause2 ? ", " : "") . "($badgeid, $ccid)";
                break;
            case "0":
                $credentialClause3 .= ($credentialClause3 ? ", " : "") . $ccid;
                break;
            default:
                $message_error = "Invalid value for $name: $value.  Database not updated.";
                Render500ErrorAjax($message_error);
                exit();
                break;
        }
    }
    if ($updateClause) {
        mysqli_query_with_error_handling(($query . mb_substr($updateClause, 0, -2) . $query_end), true, true);
        $ParticipantsUpdated = true;
    }
    if ($valuesClause2) {
        mysqli_query_with_error_handling($query2 . $valuesClause2, true, true);
        $ParticipantsUpdated = true;
    }
    if ($credentialClause3) {
        mysqli_query_with_error_handling($query3 . $credentialClause3 . ")", true, true);
        $ParticipantsUpdated = true;
    }
    $fname = getString('fname');
    $lname = getString('lname');
    $badgename = getString('badgename');
    $phone = getString('phone');
    $email = getString('email');
    $postaddress1 = getString('postaddress1');
    $postaddress2 = getString('postaddress2');
    $postcity = getString('postcity');
    $poststate = getString('poststate');
    $postzip = getString('postzip');
    $postcountry = getString('postcountry');
    if (!is_null($fname) || !is_null($lname) || !is_null($badgename) || !is_null($phone) || !is_null($email) || !is_null($postaddress1)
        || !is_null($postaddress2) || !is_null($postcity) || !is_null($poststate) || !is_null($postzip) || !is_null($postcountry)) {
        if (USE_REG_SYSTEM) {
            $message_error = "Zambia configuration error.  Editing contact data is not permitted.";
            Render500ErrorAjax($message_error);
            exit();
        }

        $query = <<<EOD
UPDATE CongoDumpHistory
    SET inactivatedts = CURRENT_TIMESTAMP, inactivatedbybadgeid = ?
    WHERE
            badgeid = ?
        AND inactivatedts IS NULL;
EOD;
        $rows = mysql_cmd_with_prepare($query, "ss", array($badgeid, $badgeid));
        if (is_null($rows)) {
            exit();
        }

        $query_preable = "UPDATE CongoDump SET ";
        $query_portion_arr = array();
        $query_param_arr = array();
        $query_param_type_str = "";
        push_query_arrays($fname, 'firstname', 's', 30, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($lname, 'lastname', 's', 40, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($badgename, 'badgename', 's', 51, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($phone, 'phone', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($email, 'email', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postaddress1, 'postaddress1', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postaddress2, 'postaddress2', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postcity, 'postcity', 's', 50, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($poststate, 'poststate', 's', 25, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postzip, 'postzip', 's', 10, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postcountry, 'postcountry', 's', 25, $query_portion_arr, $query_param_arr, $query_param_type_str);
        $query_param_arr[] = $badgeid;
        $query_param_type_str .= 's';
        $query = $query_preable . implode(', ', $query_portion_arr) . " WHERE badgeid = ?";
        $rows = mysql_cmd_with_prepare($query, $query_param_type_str, $query_param_arr);
        if ($rows !== 1) {
            $message_error = "Error updating db.";
            Render500ErrorAjax($message_error);
            exit();
        }

        $query = <<<EOD
INSERT INTO CongoDumpHistory
    (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, createdbybadgeid)
    SELECT
            badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, badgeid
        FROM
            CongoDump
        WHERE
            badgeid = ?;
EOD;
        $rows = mysql_cmd_with_prepare($query, "s", array($badgeid));
        if ($rows !== 1) {
            $message_error = "Error updating db.";
            Render500ErrorAjax($message_error);
            exit();
        }
        $CongoDumpUpdated = true;
    }
    if (empty($password) && !$ParticipantsUpdated and !$CongoDumpUpdated) {
        $message_error = "No data found to update. Database not updated.";
        Render500ErrorAjax($message_error);
        exit();
    }
?>
    <div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-success">
<?php
    if (!empty($password)) {
        echo "Password updated. <br />\n";
        $_SESSION['hashedPassword'] = $hashedPassword;
    }
?>
            Database updated successfully.
        </div>
    </div>
</div>
<?php
    if ($pubsname) {
        $_SESSION['badgename'] = $pubsname;
    }
}

function uploadphoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title, $badgeid;
    $source = $_FILES["photo"]["tmp_name"];
    $type = $_FILES["photo"]["type"];
    $fname = $_FILES["photo"]["name"];
    $upload_error = $_FILES["photo"]["error"];
    $upload_size = $_FILES["photo"]["size"];
    error_log("source = $source of type $type with status $upload_error");
    if ($upload_error) {
         RenderErrorAjax("Error uploading photo.  Check if too large (Max 2MB)");
         exit();
    }
    if (!(preg_match("/image\/jpg/i", $type) || preg_match("/image\/png/i", $type) || preg_match("/image\/jpeg/i", $type))) {
         RenderErrorAjax("Photo must be a JPG/JPEG or PNG image file");
         exit();
    }

    $ext = pathinfo($fname, PATHINFO_EXTENSION);
    error_log("original name =  $fname, ext = $ext");
    $dest = getcwd();
    $newname = hash('md5', $badgeid, false) . "." . $ext;
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $newname;
    error_log("dest = $dest");
    move_uploaded_file($source, $dest);

    $sql = <<<EOD
UPDATE Participants
SET
    uploadedphotofilename = ?,
    photodenialreasonid = NULL,
    photodenialreasonothertext = NULL,
    photouploadstatus = CASE
        WHEN ISNULL(approvedphotofilename) THEN 1
        WHEN LENGTH(approvedphotofilename) > 0 THEN 2
    ELSE 1 END
WHERE badgeid = ?;
EOD;
    $paramarray = array();
    $paramarray[0] = $newname;
    $paramarray[1] = $badgeid;
    $rows = mysql_cmd_with_prepare($sql, "ss", $paramarray);

    if ($rows == 1)
        $json_return["message"] = "$fname uploaded";
    else {
        RenderErrorAjax("Unable to update database");
        exit();
    }

    $json_return["newpath"] = $newname;
    echo json_encode($json_return) . "\n";
}

function fetchphoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title, $badgeid;

    $sql = "SELECT uploadedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $badgeid;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row)
        exit();
    $dest = getcwd();
    $fname = $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $row["uploadedphotofilename"];
    echo file_get_contents($fname);
}

// start of AJAX dispatch
if (!isLoggedIn()) {
    $message_error = "You are not logged in or your session has expired.";
    RenderErrorAjax($message_error);
    exit();
}
if (!may_I('Participant')) {
    $message_error = "You do not have permission to perform this function.";
    RenderErrorAjax($message_error);
    exit();
}
if (array_key_exists('ajax_request_action', $_POST)) {
    $action = $_POST['ajax_request_action'];
} else if (array_key_exists('ajax_request_action', $_GET)) {
    $action = $_GET['ajax_request_action'];
} else {
    $action = '';
}

switch ($action) {
    case 'update_participant':
        updateparticipant();
        break;
    case 'uploadPhoto':
        uploadphoto();
        break;
    case 'fetchPhoto':
        fetchphoto();
        break;
    default:
        $message_error = "Invalid ajax_request_action: $action.  Database not updated.";
        Render500ErrorAjax($message_error);
        exit();
}

exit();
?>