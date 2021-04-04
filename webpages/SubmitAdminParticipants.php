<?php
// Copyright (c) 2006-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check if logged in and for staff privileges
require('EditPermRoles_FNC.php');
// skip to below all functions

// gets data for a participant to be displayed.  Returns as XML
function fetch_participant() {
    $fbadgeid = getString("badgeid");
    if (empty($fbadgeid)) {
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
        exit();
    }
    $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested, P.bio,
        P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.phone, CD.email, CD.postaddress1,
        CD.postaddress2, CD.postcity, CD.poststate, CD.postzip, CD.postcountry
    FROM
			 Participants P
		JOIN CongoDump CD ON P.badgeid = CD.badgeid
    WHERE
        P.badgeid = ?
    ORDER BY
        CD.lastname, CD.firstname
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

function update_participant() {
    global $linki, $message_error;
    $updatePerformed = false;
    $loggedInUserBadgeId = $_SESSION["badgeid"];
    $participantBadgeId = getString("badgeid");
    $password = getString("password");
    $bio = getString("bio");
    $pubsname = getString("pubsname");
    $staffnotes = getString("staffnotes");
    $interested = getInt("interested", NULL);

    if (!is_null($password) || !is_null($bio) || !is_null($pubsname) || !is_null($staffnotes) || !is_null($interested)) {
        $query_preable = "UPDATE Participants SET ";
        $query_portion_arr = array();
        $query_param_arr = array();
        $query_param_type_str = "";
        if (!is_null($password)) {
            push_query_arrays(password_hash($password, PASSWORD_DEFAULT), 'password', 's', 254, $query_portion_arr, $query_param_arr, $query_param_type_str);
        }
        push_query_arrays($bio, 'bio', 's', 65535, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($pubsname, 'pubsname', 's', 50, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($staffnotes, 'staff_notes', 's', 65535, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($interested, 'interested', 'i', NULL, $query_portion_arr, $query_param_arr, $query_param_type_str);
        $query_param_arr[] = $participantBadgeId;
        $query_param_type_str .= 's';
        $query = $query_preable . implode(', ', $query_portion_arr) . " WHERE badgeid = ?";
        $rows = mysql_cmd_with_prepare($query, $query_param_type_str, $query_param_arr);
        if ($rows !== 1) {
            $message_error = "Failed updating participation record, seek assistance.";
            RenderErrorAjax($message_error);
            exit();
        } else {
            $updatePerformed = true;
        }
    }

    $lastname = getString("lastname");
    $firstname = getString("firstname");
    $badgename = getString("badgename");
    $phone = getString("phone");
    $email = getString("email");
    $postaddress1 = getString("postaddress1");
    $postaddress2 = getString("postaddress2");
    $postcity = getString("postcity");
    $poststate = getString("poststate");
    $postzip = getString("postzip");
    $postcountry = getString("postcountry");
    
    if (!is_null($lastname) || !is_null($firstname) || !is_null($badgename) || !is_null($phone) || !is_null($email) || !is_null($postaddress1)
        || !is_null($postaddress2) || !is_null($postcity) || !is_null($poststate) || !is_null($postzip) || !is_null($postcountry)) {
        if (USE_REG_SYSTEM) {
            $message_error = "Zambia configuration error.  Editing contact data is not permitted.";
            RenderErrorAjax($message_error);
            exit();
        }

        $query = <<<EOD
UPDATE CongoDumpHistory
    SET inactivatedts = CURRENT_TIMESTAMP, inactivatedbybadgeid = ?
    WHERE 
            badgeid = ?
        AND inactivatedts IS NULL;
EOD;
        $rows = mysql_cmd_with_prepare($query, "ss", array($loggedInUserBadgeId, $participantBadgeId));
        if (is_null($rows)) {
            exit();
        }

        $query_preable = "UPDATE CongoDump SET ";
        $query_portion_arr = array();
        $query_param_arr = array();
        $query_param_type_str = "";
        push_query_arrays($lastname, 'lastname', 's', 40, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($firstname, 'firstname', 's', 30, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($badgename, 'badgename', 's', 51, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($phone, 'phone', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($email, 'email', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postaddress1, 'postaddress1', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postaddress2, 'postaddress2', 's', 100, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postcity, 'postcity', 's', 50, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($poststate, 'poststate', 's', 25, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postzip, 'postzip', 's', 10, $query_portion_arr, $query_param_arr, $query_param_type_str);
        push_query_arrays($postcountry, 'postcountry', 's', 25, $query_portion_arr, $query_param_arr, $query_param_type_str);
        $query_param_arr[] = $participantBadgeId;
        $query_param_type_str .= 's';
        $query = $query_preable . implode(', ', $query_portion_arr) . " WHERE badgeid = ?";
        $rows = mysql_cmd_with_prepare($query, $query_param_type_str, $query_param_arr);
        if ($rows !== 1) {
            $message_error = "Failed updating participation record, seek assistance.";
            RenderErrorAjax($message_error);
            exit();
        } else {
            $updatePerformed = true;
        }

        $query = <<<EOD
INSERT INTO CongoDumpHistory
    (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, createdbybadgeid)
    SELECT
            badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, ?
        FROM
            CongoDump
        WHERE
            badgeid = ?;
EOD;
        $rows = mysql_cmd_with_prepare($query, "ss", array($loggedInUserBadgeId, $participantBadgeId));
        if (is_null($rows)) {
            exit();
        } else {
            $updatePerformed = true;
        }

    }
    $rolesToAddArr = getArrayOfInts("rolesToAdd");
    $rolesToDeleteArr = getArrayOfInts("rolesToDelete");
    if ($rolesToAddArr !== false || $rolesToDeleteArr !== false) {
        if (!may_I('EditUserPermRoles')) {
            $message_error = "Server configuration error: You do not have permission to edit user roles. Seek assistance.";
            RenderErrorAjax($message_error);
            exit();
        }
        ['mayIEditAllRoles' => $mayIEditAllRoles, 'rolesIMayEditArr' => $rolesIMayEditArr] = fetchMyEditableRoles($loggedInUserBadgeId);
        if (!$mayIEditAllRoles) {
            if (count($rolesIMayEditArr) == 0) {
                $message_error = "Server configuration error: You do not have permission to edit user roles. Seek assistance.";
                RenderErrorAjax($message_error);
                exit();
            }
            if (($rolesToAddArr && count(array_diff($rolesToAddArr, $rolesIMayEditArr))) > 0 ||
                ($rolesToDeleteArr && count(array_diff($rolesToDeleteArr, $rolesIMayEditArr)) > 0 )) {
                $message_error = "Server configuration error: You attempting to edit roles you do not have permission to edit. Seek assistance.";
                RenderErrorAjax($message_error);
                exit();
            }
        }
        $badgeIdSafe = mysqli_real_escape_string($linki, $participantBadgeId);
        if ($rolesToAddArr != false && count($rolesToAddArr) > 0) {
            $rolesToAddList = implode(',', array_map(function ($role) use ($badgeIdSafe) {
                return "('$badgeIdSafe', $role)";
            }, $rolesToAddArr));
            $query = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES $rolesToAddList;";
            $result = mysqli_query_exit_on_error($query);
            if (!$result) {
                exit(); // should have exited already
            }
            $updatePerformed = true;
        }
        if ($rolesToDeleteArr != false && count($rolesToDeleteArr) > 0) {
            $rolesToDeleteList = implode(',', $rolesToDeleteArr);
            $query = <<<EOD
    DELETE
            UHPR
        FROM
            UserHasPermissionRole UHPR
        WHERE
                UHPR.badgeid = ?
            AND UHPR.permroleid IN ($rolesToDeleteList);
EOD;
            $rows = mysql_cmd_with_prepare($query, "s", array($participantBadgeId));
            if (is_null($rows)) {
                exit();
            }
            $updatePerformed = true;
        }
    }
    if (!$updatePerformed) {
        $message_error = "Server error: nothing found to update. Seek assistance.";
        RenderErrorAjax($message_error);
        exit();
    }
    $message = "<p>Database updated successfully.</p>";
    if ($interested === 2) {
        $query = <<<EOD
UPDATE ParticipantOnSessionHistory
    SET inactivatedts = NOW(), inactivatedbybadgeid = ?
	WHERE
	        badgeid = ?
		AND inactivatedts IS NULL;
EOD;
        $update_arr = array($_SESSION['badgeid'], $participantBadgeId);
        $updateStr = "ss";
        $rows = mysql_cmd_with_prepare($query, $updateStr, $update_arr);
        if (is_null($rows)) {
            $message_error .= "Failed updating participant sessions record, seek assistance.";
            RenderErrorAjax($message_error);
            return;
        } else if ($rows < 0) {
            $message_error .= "Failed updating participant sessions record, seek assistance.";
            RenderErrorAjax($message_error);
            return;
        }
        $message .= "<p>Participant removed from $rows session(s).</p>";
    }
?>
<div class="row mt-3">
    <div class="col-12">
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    </div>
</div>
<?php
}

function perform_search() {
    global $linki, $message_error;
    $searchString = getString("searchString");
    if ($searchString == "")
        exit();
    if (is_numeric($searchString)) {
        $searchString =  mysqli_real_escape_string($linki, $searchString);
        $query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio,
                    P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
                    CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
                    CD.postcountry, CD.regtype
			    FROM
						 Participants P
					JOIN CongoDump CD ON P.badgeid = CD.badgeid
			    WHERE
			        P.badgeid = "$searchString"
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
        $xml = mysql_query_XML($query);
    } else {
        $searchString = '%' . $searchString . '%';
        $query = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio,
                    P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
                    CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
                    CD.postcountry, CD.regtype
			    FROM
						 Participants P
					JOIN CongoDump CD ON P.badgeid = CD.badgeid
			    WHERE
			           P.pubsname LIKE ?
					OR CD.lastname LIKE ?
					OR CD.firstname LIKE ?
					OR CD.badgename LIKE ?
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
        $param_arr = array($searchString,$searchString,$searchString,$searchString);
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "ssss", $param_arr);
        $xml = mysql_result_to_XML("searchParticipants", $result);
    }
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
    RenderXSLT('AdminParticipants.xsl', $paramArray, $xml);
	exit();
}

function fetch_user_perm_roles() {
    global $message_error;
    $fetchedUserBadgeId = getString('badgeid');
    if (empty($fetchedUserBadgeId)) {
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
        exit();
    }
    if (may_I('EditUserPermRoles')) {
        $loggedInUserBadgeId = $_SESSION['badgeid'];
        ['mayIEditAllRoles' => $mayIEditAllRoles, 'rolesIMayEditArr' => $rolesIMayEditArr] = fetchMyEditableRoles($loggedInUserBadgeId);
        if ($mayIEditAllRoles) {
            $query = <<<EOD
SELECT 
        PR.permrolename, PR.permroleid, UHPR.badgeid, 1 AS mayedit
    FROM
                  PermissionRoles PR
        LEFT JOIN UserHasPermissionRole UHPR ON
                UHPR.badgeid = ?
            AND UHPR.permroleid = PR.permroleid
    ORDER BY
        IF(ISNULL(UHPR.badgeid), 1, 0), PR.display_order;
EOD;
            $resultXML = mysql_prepare_query_XML(
                    array("permroles" => $query),
                    array("permroles" => "s"),
                    array("permroles" => array($fetchedUserBadgeId)));
        } else { // has permission to edit only specific perm roles
            $query = <<<EOD
SELECT 
        PR.permrolename, PR.permroleid, UHPR.badgeid, 
        IF(ISNULL(SQ.elementid), 0, 1) AS mayedit
    FROM
                  PermissionRoles PR
        LEFT JOIN UserHasPermissionRole UHPR ON
                UHPR.badgeid = ?
            AND UHPR.permroleid = PR.permroleid
        LEFT JOIN (
            SELECT 
                    PA.elementid
                FROM
                         UserHasPermissionRole UHPR
                    JOIN Permissions P USING (permroleid)
                    JOIN PermissionAtoms PA USING (permatomid)
                WHERE
                        UHPR.badgeid = ?
                    AND PA.permatomtag = 'EditUserPermRoles'
                    AND PA.elementid IS NOT NULL
                    ) AS SQ ON SQ.elementid = PR.permroleid
    ORDER BY
        IF(ISNULL(UHPR.badgeid), 1, 0), mayedit DESC, PR.display_order;
EOD;
            $resultXML = mysql_prepare_query_XML(
                array("permroles" => $query),
                array("permroles" => "ss"),
                array("permroles" => array($fetchedUserBadgeId, $loggedInUserBadgeId)));
        }
    } else { // has no permission to edit user perm roles
        $query = <<<EOD
SELECT 
        PR.permrolename, PR.permroleid, UHPR.badgeid, 0 AS mayedit
    FROM
                  PermissionRoles PR
        LEFT JOIN UserHasPermissionRole UHPR ON
                UHPR.badgeid = ?
            AND UHPR.permroleid = PR.permroleid
    ORDER BY
        IF(ISNULL(UHPR.badgeid), 1, 0), PR.display_order;
EOD;
        $resultXML = mysql_prepare_query_XML(
            array("permroles" => $query),
            array("permroles" => "s"),
            array("permroles" => array($fetchedUserBadgeId)));
    }
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
    }
    // $foo = mb_ereg_replace("<(row|query)([^>]*)/[ ]*>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"); //for debugging only
    RenderXSLT('FetchUserPermRoles.xsl', array(), $resultXML);
}

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
if (!isLoggedIn()) {
    $message_error = "You are not logged in or your session has expired.";
    RenderErrorAjax($message_error);
    exit();

}
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
    case "update_participant":
        update_participant();
        break;
    case "fetch_user_perm_roles":
        fetch_user_perm_roles();
        break;
    default:
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
        exit();
}

?>
