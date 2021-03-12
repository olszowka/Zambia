<?php
// Copyright (c) 2006-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check for staff privileges
require('EditPermRoles_FNC.php');
// skip to below all functions

// gets data for a participant to be displayed.  Returns as XML
function fetch_participant() {
    global $message_error;
    $fbadgeid = getString("badgeid");
    if (empty($fbadgeid)) {
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
        exit();
    }
    $query = <<<EOD
SELECT
    P.badgeid, CD.firstname, CD.lastname, CD.badgename,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM Participants P
JOIN CongoDump CD ON P.badgeid = CD.badgeid
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE P.badgeid = ?
ORDER BY CD.lastname, CD.firstname
EOD;
    $param_arr = array($fbadgeid);
    $result = mysqli_query_with_prepare_and_exit_on_error($query, "s", $param_arr);
    $resultXML = mysql_result_to_XML("fetchParticipants", $result);
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
    }
    header("Content-Type: text/xml");
    echo($resultXML->saveXML());
    exit();
}

function perform_search() {
    global $linki, $message_error;
    $searchString = getString("searchString");
    $photosApproval = getString("photosApproval");
    if ($searchString == "" && $photosApproval == false)
        exit();
    $mask = PHOTO_NEED_APPROVAL_MASK;
    $needs = PHOTO_NEED_APPROVAL;
    $json_return = array ();
    if ($photosApproval == "true") {
        $needswhere = " AND (P.photouploadstatus & $mask) = $needs ";
    } else {
        $needswhere = "";
    }
    if ($photosApproval == "true" && $searchString == "") {

        $query = <<<EOD
SELECT
	P.badgeid, CD.firstname, CD.lastname, CD.badgename,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM Participants P
JOIN CongoDump CD ON P.badgeid = CD.badgeid
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE
	(P.photouploadstatus & $mask) = $needs
ORDER BY
	CD.lastname, CD.firstname
EOD;
        $result = mysqli_query_exit_on_error($query);
    } else if (is_numeric($searchString)) {
        $query = <<<EOD
SELECT
	P.badgeid, CD.firstname, CD.lastname, CD.badgename,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM Participants P
JOIN CongoDump CD ON P.badgeid = CD.badgeid
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE P.badgeid = ? $needswhere
ORDER BY CD.lastname, CD.firstname
EOD;
        $param_arr = array($searchString);
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "s", $param_arr);
    } else {
        $searchString = '%' . $searchString . '%';
        $query = <<<EOD
SELECT
	P.badgeid, CD.firstname, CD.lastname, CD.badgename,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM Participants P
JOIN CongoDump CD ON P.badgeid = CD.badgeid
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE
	    (P.pubsname LIKE ?
    OR CD.lastname LIKE ?
    OR CD.firstname LIKE ?
    OR CD.badgename LIKE ?
    OR P.badgeid LIKE ?) $needswhere
ORDER BY CD.lastname, CD.firstname
EOD;
        $param_arr = array($searchString,$searchString,$searchString,$searchString,$searchString);
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "sssss", $param_arr);
    }
    $xml = mysql_result_to_XML("searchParticipants", $result);
    $rows = mysqli_num_rows($result);
    if ($rows > 1) {
        mysqli_data_seek($result, 0);
        $bidarray = array ();
        while ($row = mysqli_fetch_assoc($result)) {
            $bidarray[] = $row["badgeid"];
        }
        $json_return["badgeids"] = $bidarray;
    }

    mysqli_free_result($result);
    if (!$xml) {
        echo $message_error;
        exit();
    }
    $xpath = new DOMXpath($xml);
	$searchParticipantsResultRowElements = $xpath->query("/doc/query[@queryName='searchParticipants']/row");
    foreach ($searchParticipantsResultRowElements as $resultRowElement) {
    	$badgeid = $resultRowElement -> getAttribute("badgeid");
    	$jsEscapedBadgeid = addslashes($badgeid);
		$resultRowElement -> setAttribute('jsEscapedBadgeid', $jsEscapedBadgeid);
	}
    header("Content-Type: text/html");
    $paramArray = array("userIdPrompt" => USER_ID_PROMPT);
    //echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xml->saveXML(), "i")); //for debugging only
    $json_return["HTML"] = RenderXSLT('AdminPhotos.xsl', $paramArray, $xml, true);
    $json_return["rowcount"] = $rows;
    echo json_encode($json_return);
	exit();
}

function uploadphoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;
    $participantBadgeId = $_POST["badgeid"];
    $pos = strpos($_POST["photo"], ",");
    $source = substr($_POST["photo"], $pos + 1);
    $type = substr($_POST["photo"], 0, $pos);
    //error_log($type);
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
    //error_log("w: $up_width, h: $up_height");
    $dest = getcwd();
    $newname = hash('md5', $participantBadgeId, false) . "." . $ext;
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $newname;
    //error_log("dest = $dest");

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
    //error_log("resize: $resize");
    if ($resize != 1) {
        $originalimage = imagecreatefromstring($image);

        $newwidth = intval($up_width * $resize);
        $newheight = intval($up_height * $resize);
        //error_log("nw: $newwidth, nh: $newheight");
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
    $sql .= " photouploadstatus = ((photouploadstatus | " . strval(PHOTO_UPLOAD_MASK) . ") &  ~" . strval(PHOTO_DENIED_MASK) . ")\nWHERE badgeid = ?;";
    //error_log($sql);
    $paramarray = array();
    $paramarray[] = $newname;
    $paramarray[] = $participantBadgeId;
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
    $paramarray[] = $participantBadgeId;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        $json_return["message"] = "Error fetching Photo Status";
    }
    $json_return["photostatus"] = $row["statustext"];
    $json_return["photostatusid"] = $row["photouploadstatus"];
    $json_return["photoname"] = $newname;
    $dest = getcwd();
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $newname;

    $json_return["image"] = "data:$type;base64," . base64_encode(file_get_contents($dest));
    echo json_encode($json_return) . "\n";
}


function fetch_photo() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;
    $participantBadgeId = getString("badgeid");

    $sql = "SELECT uploadedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $participantBadgeId;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row)
        return;
    $dest = getcwd();
    $dest .= "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $row["uploadedphotofilename"];
    echo file_get_contents($dest);
}

function deleteuploadedphoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $participantBadgeId = getString("badgeid");

    $json_return = array();
    $dest = getcwd();
    $do_update = true;

    $sql = "SELECT uploadedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $participantBadgeId;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        RenderErrorAjax("Error fetching photo to delete");
        exit();
    }

    $fname = $dest . "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $row["uploadedphotofilename"];
    try {
        unlink($fname);
    } catch (Exception $e) {
        error_log("Caught: " . $e->getMessage());
        $json_return["message"] = "Error deleting photo";
        $do_update = false;
    }

    if ($do_update) {
        $sql = "UPDATE Participants SET uploadedphotofilename = NULL, photodenialreasonothertext = NULL, photodenialreasonid = NULL," .
           " photouploadstatus = photouploadstatus & ~" . strval(PHOTO_UPLOAD_MASK) . " & ~" . strval(PHOTO_DENIED_MASK) .
           "\nWHERE badgeid = ?;";
        $paramarray = array();
        $paramarray[0] = $participantBadgeId;
        //error_log("Sql=\n$sql\n");
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
            $json_return["message"] = "Uploaded photo deleted";
            $json_return["photostatus"] = $row["statustext"];
        }

        $fname = $dest . PHOTO_PUBLIC_DIRECTORY . "/" . PHOTO_DEFAULT_IMAGE;
        //error_log("Default path = $fname");
        $json_return["image"] = 'data:image/png;base64,' . base64_encode(file_get_contents($fname));
    }
    echo json_encode($json_return) . "\n";
}

function denyphoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $participantBadgeId = getString("badgeid");
    $reasoncode = getInt("reasonid");
    $othertext = getString("othertext");

    $json_return = array();

    $sql = "UPDATE Participants SET photodenialreasonothertext = ?, photodenialreasonid = ?," .
           " photouploadstatus = photouploadstatus | " . strval(PHOTO_DENIED_MASK) . " WHERE badgeid = ?;";
    $paramarray = array();
    $paramarray[0] = $othertext;
    $paramarray[1] = $reasoncode;
    $paramarray[2] = $participantBadgeId;
    //error_log("Sql=\n$sql\n");
    $rows =  mysql_cmd_with_prepare($sql, 'sis', $paramarray);
    if ($rows != 1) {
        RenderErrorAjax("Unable to update database");
        exit();
    }

    $sql = <<<EOD
SELECT
    P.photodenialreasonothertext, P.photodenialreasonid,
    CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
    R.statustext, D.reasontext
FROM Participants P
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE P.badgeid = ?
EOD;
    $paramarray = array();
    $paramarray[0] = $participantBadgeId;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        $json_return["message"] = "Error fetching Photo Status";
    } else {
        $json_return["message"] = "Photo denied";
        $json_return["photostatus"] = $row["statustext"];
        $json_return["photostatusid"] = $row["photouploadstatus"];
        $json_return["othertext"] = $row["photodenialreasonothertext"];
        $json_return["reasontext"] = $row["reasontext"];
        $json_return["reasonid"] = $row["photodenialreasonid"];
    }

    echo json_encode($json_return) . "\n";
};

function approvephoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $participantBadgeId = getString("badgeid");
    $json_return = array();
    $move_ok = true;

    $sql = "SELECT uploadedphotofilename, approvedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $participantBadgeId;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        RenderErrorAjax("Unable to retrieve photo from database");
        exit();
    }

    $dest = getcwd();
    $oldfilename = $dest . "/" . PHOTO_PUBLIC_DIRECTORY . "/" . $row["approvedphotofilename"];
    if (strlen($oldfilename) > 0) {
        try {
            unlink($oldfilename);
        }
        catch (Exception $e) {
            error_log("Caught: " . $e->getMessage());
            $json_return["message"] = "Error deleting prior approved photo";
            $move_ok = false;
        }
    }

    if ($move_ok) {
        $filename = $row["uploadedphotofilename"];
        $upload_path = $dest .  "/" . PHOTO_UPLOAD_DIRECTORY . "/" . $filename;
        $approved_path = $dest . "/" . PHOTO_PUBLIC_DIRECTORY . "/pp" . $filename;
        try {
            rename($upload_path, $approved_path);
        } catch (Exception $e) {
            error_log("Caught: " . $e->getMessage());
            $json_return["message"] = "Error moving approved photo";
            $move_ok = false;
        }
    }

    if ($move_ok) {
        $sql = "UPDATE Participants SET uploadedphotofilename = NULL, approvedphotofilename = 'pp" . $filename . "', " .
            "photodenialreasonothertext = NULL, photodenialreasonid = NULL, photouploadstatus = " . strval(PHOTO_APPROVED_MASK) . " WHERE badgeid = ?";
        $paramarray = array();
        $paramarray[0] = $participantBadgeId;
        //error_log("Sql=\n$sql\n");
        $rows =  mysql_cmd_with_prepare($sql, 's', $paramarray);
        if ($rows != 1) {
            RenderErrorAjax("Unable to update database");
            exit();
        }
        $sql = <<<EOD
SELECT
    P.photodenialreasonothertext, P.photodenialreasonid, P.approvedphotofilename,
    CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
    R.statustext, D.reasontext
FROM Participants P
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE P.badgeid = ?
EOD;
        $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            $json_return["message"] = "Error fetching Photo Status";
        } else {
            $json_return["photostatus"] = $row["statustext"];
            $json_return["photostatusid"] = $row["photouploadstatus"];
            $json_return["othertext"] = $row["photodenialreasonothertext"];
            $json_return["reasontext"] = $row["reasontext"];
            $json_return["reasonid"] = $row["photodenialreasonid"];
            $json_return["approvedphoto"] = $row["approvedphotofilename"];
            $json_return["message"] = "Photo approved";
        }
    }
    echo json_encode($json_return) . "\n";
};

function deleteapprovedphoto() {
    global $linki, $message_error, $returnAjaxErrors, $return500errors, $title;

    $participantBadgeId = getString("badgeid");
    $json_return = array();
    $dest = getcwd();
    $do_update = true;

    $sql = "SELECT approvedphotofilename FROM Participants WHERE badgeid = ?";
    $paramarray = array();
    $paramarray[0] = $participantBadgeId;
    $result =  mysqli_query_with_prepare_and_exit_on_error($sql, 's', $paramarray);
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        RenderErrorAjax("Unable to fetch photo to delete from database");
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
        $sql = "UPDATE Participants SET approvedphotofilename = NULL, photouploadstatus = photouploadstatus & ~" . strval(PHOTO_APPROVED_MASK) .
           "\nWHERE badgeid = ?;";
        $paramarray = array();
        $paramarray[0] = $participantBadgeId;
        //error_log("Sql=\n$sql\n");
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
        //error_log("Default path = $fname");
        $json_return["image"] = 'data:image/png;base64,' . base64_encode(file_get_contents($fname));
    }
    echo json_encode($json_return) . "\n";
};

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
if (!isLoggedIn()) {
    $message_error = "You are not logged in or your session has expired.";
    RenderErrorAjax($message_error);
    exit();
}

if (!may_I('AdminPhotos'))
    exit(0);

$ajax_request_action = getString("ajax_request_action");
if (is_null($ajax_request_action)) {
    $message_error = "Internal error.";
    RenderErrorAjax($message_error);
    exit();
}
//error_log("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action");
switch ($ajax_request_action) {
    case "fetch_participant":
        fetch_participant();
        break;
    case "perform_search":
        perform_search();
        break;
    case "fetchphoto":
        fetch_photo();
        break;
    case 'uploadPhoto':
        uploadphoto();
        break;
    case 'delete_uploaded_photo':
        deleteuploadedphoto();
        break;
    case 'deny_photo':
        denyphoto();
        break;
    case 'approve_photo':
        approvephoto();
        break;
    case 'delete_approved_photo':
        deleteapprovedphoto();
        break;
    default:
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
}
exit();
?>
