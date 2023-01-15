<?php
// Copyright (c) 2011-2022 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;
$title = "My Profile";
require('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
$returnAjaxErrors = true;
$return500errors = true;
function update_participant($badgeid) {
    global $linki, $message_error, $returnAjaxErrors, $return500errors;
    $pubsname = false;
    $CongoDumpUpdated = false;
    $ParticipantsUpdated = false;
    if (($x = $_POST['ajax_request_action']) != "update_participant") {
        $message_error = "Invalid ajax_request_action: $x.  Database not updated.";
        Render500ErrorAjax($message_error);
        exit();
    }
    $may_edit_bio = may_I('EditBio');
    $query = "UPDATE Participants SET ";
    $updateClause = "";
    $query_end = " WHERE badgeid = '$badgeid';";
    if (may_I('Participant')) {
        $x = getInt('interested', -1);
        if ($x != -1) {
            $updateClause .= "interested=$x, ";
        }
    } else {
        $updateClause .= "interested=2, "; // force non participants to not interested
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
    if (isset($_POST['htmlbio'])) {
        if ($may_edit_bio) {
            $updateClause .= "htmlbio=\"" . mysqli_real_escape_string($linki, $_POST['htmlbio']) . "\", ";
        } else {
            $message_error = "You may not update your biography at this time.  Database not updated.";
            Render500ErrorAjax($message_error);
            exit();
        }
    } 
    if (isset($_POST['bio'])) {
        if ($may_edit_bio) {
            $updateClause .= "bio=\"" . mysqli_real_escape_string($linki, stripslashes($_POST['bio'])) . "\", ";
        } else {
            $message_error = "You may not update your biography at this time.  Database not updated.";
            Render500ErrorAjax($message_error);
            exit();
        }
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
        if (USE_REG_SYSTEM && !UPDATE_REG_SYSTEM) {
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
        $message_error = "Error updating db. (close history record";
        Render500ErrorAjax($message_error);
        exit();
    }
    if ($rows == 0) {   // no record existed with old values, add one
         $query = <<<EOD
INSERT INTO CongoDumpHistory
    (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype, createdbybadgeid, createdts, inactivatedts, inactivatedbybadgeid)
    SELECT
            badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype, badgeid, CURRENT_TIMESTAMP - 1, CURRENT_TIMESTAMP, ?
        FROM
            CongoDump
        WHERE
            badgeid = ?;
EOD;
        $rows = mysql_cmd_with_prepare($query, "ss", array($badgeid, $badgeid));
        if ($rows != 1) {
            $message_error = "Error updating db. (insert history record)";
            Render500ErrorAjax($message_error);
            exit();
        }
    }
// for Balticon update perinfo then congodump so if the congodump fails, it gets it from reginfo, and if the cron job runs, congodump is the same either way
    if (is_numeric($badgeid) && UPDATE_REG_SYSTEM) {
        $query_preable = "UPDATE " . REG_DBNAME . ".perinfo SET ";
        $query_portion_arr = array();
        $query_param_arr = array();
        $query_param_type_str = "";
        push_query_arrays($fname, 'first_name', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($lname, 'last_name', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($badgename, 'badge_name', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($phone, 'phone', 's', 15, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($email, 'email_addr', 's', 64, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postaddress1, 'address', 's', 64, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postaddress2, 'addr_2', 's', 64, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postcity, 'city', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($poststate, 'state', 's', 2, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postzip, 'zip', 's', 10, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postcountry, 'country', 's', 20, $query_portion_arr, $query_param_arr, $query_param_type_str);
        $query_param_arr[] = $badgeid;
        $query_param_type_str .= 's';
        $query = $query_preable . implode(', ', $query_portion_arr) . " WHERE id = ?";
        $rows = mysql_cmd_with_prepare($query, $query_param_type_str, $query_param_arr);
        if ($rows != 1) {
            $message_error = "Error updating db. (reg update)";
            Render500ErrorAjax($message_error);
            exit();
        }
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
    if ($rows != 1) {
        $message_error = "Error updating db. (record update)";
        Render500ErrorAjax($message_error);
        exit();
    }

        $query = <<<EOD
INSERT INTO CongoDumpHistory
    (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype, createdbybadgeid, createdts)
    SELECT
        badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype, ?, CURRENT_TIMESTAMP
    FROM
        CongoDump
    WHERE
        badgeid = ?;
EOD;
    $rows = mysql_cmd_with_prepare($query, "ss", array($badgeid, $badgeid));
    if ($rows != 1) {
        $message_error = "Error updating db. (history create)";
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

function uploadphoto($badgeid) {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;
    $pos = strpos($_POST["photo"], ",");
    $source = substr($_POST["photo"], $pos + 1);
    $type = substr($_POST["photo"], 0, $pos);
    error_log($type);
    //error_log($source);

    $image = base64_decode($source);
    $image_size = strlen($image);
    list($up_width, $up_height) = getimagesizefromstring($image);
    list($min_width, $min_height, $max_width, $max_height, $max_size) = explode(',', PHOTO_DIMENSIONS);

    if (!(preg_match("/image\/jpg/i", $type) || preg_match("/image\/png/i", $type) || preg_match("/image\/jpeg/i", $type))) {
         RenderErrorAjax("Photo must be a JPG/JPEG or PNG image file");
         exit();
    }

    if (preg_match("/image\/png/i", $type))
        $ext = 'png';
    else
        $ext = 'jpg';

    # get image size for resizing
    error_log("w: $up_width, h: $up_height");
    $dest = getcwd();
    $newname = hash('md5', $badgeid, false) . "." . $ext;
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $newname;
    error_log("dest = $dest");

    $resize = 1;
    # check if need to resize
    if ($up_width < $min_width && $up_height < $min_height) {
        # resize - too small
        $resizemin = max($min_width / $up_width, $min_height / $up_height);
        $resizemax = max($max_width / $up_width, $max_height / $up_height);
        if (intval($resizemin + 1) > $resizemax)
            $resize = $resizemin;
        else
            $resize = intval($resizemin + 1);
    }
    if ($up_width > $max_width && $up_height > $max_height) {
        # resize - too big
        $resizemin = max($up_width / $max_height, $up_height / $max_height);
        $resizemax = max($up_width / $min_height, $up_height / $min_height);
        if (intval($resizemin + 1) > $resizemax)
            $resize = 1.0/$resizemin;
        else
            $resize = 1.0/intval($resizemin + 1);
    }
    error_log("resize: $resize");
    if ($resize != 1) {
        $originalimage = imagecreatefromstring($image);

        $newwidth = intval($up_width * $resize);
        $newheight = intval($up_height * $resize);
        error_log("nw: $newwidth, nh: $newheight");
        $resizedimage = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($resizedimage, $originalimage, 0, 0, 0, 0, $newwidth, $newheight, $up_width, $up_height);
        if ($ext == 'png')
            $result = imagepng($resizedimage, $dest);
        else
            $result = imagejpeg($resizedimage, $dest);
        imagedestroy($resizedimage);
    }
    else {
        if ($image_size > $max_size) {
            RenderErrorAjax("Image is too large, maximim size = $max_size");
            exit();
        }
        $fd = fopen($dest, 'wb');
        if ($fd === false) {
            RenderErrorAjax("Error with uploaded image, unable to create file");
            exit();
        }
        $len = fwrite($fd, $image, $image_size);
        if ($len != $image_size) {
            RenderErrorAjax("Error with uploaded image, unable to save");
            exit();
        }
        fclose($fd);
    }

    $sql = <<<EOD
UPDATE Participants
SET
    uploadedphotofilename = ?,
    photodenialreasonid = NULL,
    photodenialreasonothertext = NULL,
EOD;
    $sql .= " photouploadstatus = ((IFNULL(photouploadstatus,0) | " . strval(PHOTO_UPLOAD_MASK) . ") &  ~" . strval(PHOTO_DENIED_MASK) . ")\nWHERE badgeid = ?;";
    error_log($sql);
    $paramarray = array();
    $paramarray[] = $newname;
    $paramarray[] = $badgeid;
    $rows = mysql_cmd_with_prepare($sql, "ss", $paramarray);
    if ($rows === false) {
        RenderErrorAjax("Unable to update database");
        exit();
    }
    if ($rows == 1 || $rows == 0)
        $json_return["message"] = "Image uploaded";
    else {
        RenderErrorAjax("Error updating database");
        exit();
    }

    $sql = <<<EOD
SELECT CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus, R.statustext
FROM Participants P
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE badgeid = ?;
EOD;

    $paramarray = array();
    $paramarray[] = $badgeid;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        RenderErrorAjax("Error fetching Photo Status");
        exit();
    }
    $json_return["photostatus"] = $row["statustext"];
    $dest = getcwd();
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $newname;

    $json_return["image"] = "data:$type;base64," . base64_encode(file_get_contents($dest));
    echo json_encode($json_return) . "\n";
}

function fetchphoto($badgeid) {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $sql = "SELECT uploadedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $badgeid;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row)
        exit();
    $dest = getcwd();
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $row["uploadedphotofilename"];
    echo file_get_contents($dest);
}

function deleteuploadedphoto($badgeid) {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $json_return = array();
    $dest = getcwd();
    $do_update = true;

    $sql = "SELECT uploadedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $badgeid;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        RenderErrorAjax("Error fetching photo to delete");
        exit();
    }

    $fname = $dest . "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $row["uploadedphotofilename"];
    try {
        unlink($fname);
    }
    catch (Exception $e) {
        error_log("Caught: " . $e->getMessage());
        $json_return["message"] = "Error deleting photo";
        $do_update = false;
    }

    if ($do_update) {
        $sql = "UPDATE Participants SET uploadedphotofilename = NULL, photodenialreasonothertext = NULL, photodenialreasonid = NULL," .
           " photouploadstatus = IFNULL(photouploadstatus, 0) & ~" . strval(PHOTO_UPLOAD_MASK) . " & ~" . strval(PHOTO_DENIED_MASK) .
           "\nWHERE badgeid = ?;";
        $paramarray = array();
        $paramarray[0] = $badgeid;
        error_log("Sql=\n$sql\n");
        $rows =  mysql_cmd_with_prepare($sql, 's', $paramarray);
        if ($rows != 1) {
            RenderErrorAjax("Error updating database");
            exit();
        }

        $sql = <<<EOD
SELECT CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus, R.statustext
FROM Participants P
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE badgeid = ?;
EOD;

        $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            $json_return["message"] = "Error fetching Photo Status";
        } else {
            $json_return["photostatus"] = $row["statustext"];
            $json_return["message"] = "Uploaded photo deleted";
        }

        $fname = $dest . PHOTO_PUBLIC_DIRECTORY . "/" . PHOTO_DEFAULT_IMAGE;
        error_log("Default path = $fname");
        $json_return["image"] = 'data:image/png;base64,' . base64_encode(file_get_contents($fname));
    }
    echo json_encode($json_return) . "\n";
};

function deleteapprovedphoto($badgeid) {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $json_return = array();
    $dest = getcwd();
    $do_update = true;

    $sql = "SELECT approvedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $badgeid;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        RenderErrorAjax("Error fetching photo to delete");
        exit();
    }
    $fname = $dest . "/" . PHOTO_PUBLIC_DIRECTORY . "/" . $row["approvedphotofilename"];
    try {
        unlink($fname);
    }
    catch (Exception $e) {
        error_log("Caught: " . $e->getMessage());
        $json_return["message"] = "Error deleting approved photo";
        $do_update = false;
    }
    if ($do_update) {
        $sql = "UPDATE Participants SET approvedphotofilename = NULL, photouploadstatus = IFNULL(photouploadstatus, 0) & ~" . strval(PHOTO_APPROVED_MASK) .
           "\nWHERE badgeid = ?;";
        $paramarray = array();
        $paramarray[0] = $badgeid;
        error_log("Sql=\n$sql\n");
        $rows =  mysql_cmd_with_prepare($sql, 's', $paramarray);
        if ($rows != 1) {
            RenderErrorAjax("Unable to update database");
            exit();
        }

        $sql = <<<EOD
SELECT CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus, R.statustext
FROM Participants P
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE badgeid = ?;
EOD;

        $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            $json_return["message"] = "Error fetching Photo Status";
        } else {
            $json_return["message"] = "Approved photo deleted";
            $json_return["photostatus"] = $row["statustext"];
        }

        $fname = $dest . PHOTO_PUBLIC_DIRECTORY . "/" . PHOTO_DEFAULT_IMAGE;
        error_log("Default path = $fname");
        $json_return["image"] = 'data:image/png;base64,' . base64_encode(file_get_contents($fname));
    }
    echo json_encode($json_return) . "\n";
};

// start of AJAX dispatch
if (!isLoggedIn()) {
    $message_error = "You are not logged in or your session has expired.";
    RenderErrorAjax($message_error);
    exit();
}
if (!may_I('edit_my_contact')) {
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

//error_log("Action = $action");
$badgeid = isset($_SESSION['badgeid']) ? $_SESSION['badgeid'] : null;
switch ($action) {
    case 'update_participant':
        update_participant($badgeid);
        break;
    case 'uploadPhoto':
        uploadphoto($badgeid);
        break;
    case 'fetchPhoto':
        fetchphoto($badgeid);
        break;
    case 'delete_uploaded_photo':
        deleteuploadedphoto($badgeid);
        break;
    case 'delete_approved_photo':
        deleteapprovedphoto($badgeid);
        break;
    default:
        $message_error = "Invalid ajax_request_action: $action.  Database not updated.";
        Render500ErrorAjax($message_error);
        exit();
}

exit();
?>