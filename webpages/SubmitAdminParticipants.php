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
    P.badgeid, P.pubsname, P.interested, P.bio, P.htmlbio,
    P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.phone, CD.email, CD.postaddress1,
    CD.postaddress2, CD.postcity, CD.poststate, CD.postzip, CD.postcountry, CD.regtype,
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

function update_participant() {
    global $linki, $message_error;
    $updatePerformed = false;
    $loggedInUserBadgeId = $_SESSION["badgeid"];
    $participantBadgeId = getString("badgeid");
    $password = getString("password");
    $bio = getString("bio");
    if (HTML_BIO === TRUE)
        $htmlbio = getString("htmlbio");
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
        if (HTML_BIO === true)
            push_query_arrays($htmlbio, 'htmlbio', 's', 65535, $query_portion_arr, $query_param_arr, $query_param_type_str);
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
            $rows = mysql_cmd_with_prepare($query, "ss", array($loggedInUserBadgeId, $participantBadgeId));
            if ($rows != 1) {
                $message_error = "Error updating db. (insert history record)";
                Render500ErrorAjax($message_error);
                exit();
            }
        }
        // for Balticon reg system update perinfo then congodump so if the congodump fails, it gets it from reginfo, and if the cron job runs, congodump is the same either way
        if (defined(UPDATE_REG_SYSTEM)) {
            $reg_dbname = REG_DBNAME;
            if (is_numeric($participantBadgeId)) {
                $query_preable = "UPDATE $reg_dbname.perinfo SET ";
                $query_portion_arr = array();
                $query_param_arr = array();
                $query_param_type_str = "";
                push_query_arrays($firstname, 'first_name', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($lastname, 'last_name', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($badgename, 'badge_name', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($phone, 'phone', 's', 15, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($email, 'email_addr', 's', 64, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($postaddress1, 'address', 's', 64, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($postaddress2, 'addr_2', 's', 64, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($postcity, 'city', 's', 32, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($poststate, 'state', 's', 2, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($postzip, 'zip', 's', 10, $query_portion_arr, $query_param_arr, $query_param_type_str);
                push_query_arrays($postcountry, 'country', 's', 20, $query_portion_arr, $query_param_arr, $query_param_type_str);
                $query_param_arr[] = $participantBadgeId;
                $query_param_type_str .= 's';
                $query = $query_preable . implode(', ', $query_portion_arr) . " WHERE id = ?";
                $rows = mysql_cmd_with_prepare($query, $query_param_type_str, $query_param_arr);
                if ($rows != 1) {
                    $message_error = "Error updating db. (reg update)";
                    Render500ErrorAjax($message_error);
                    exit();
                }
            }
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
    (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype, createdbybadgeid, createdts)
    SELECT
            badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype, ?, CURRENT_TIMESTAMP
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
    $json_return = array ();
    if (is_numeric($searchString)) {
        if (DBVER >= "8") {
            $query = <<<EOD
WITH AnsweredSurvey(participantid, answercount) AS (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
    WHERE participantid = ?
)
SELECT
	P.badgeid, P.pubsname, P.interested, P.bio, P.htmlbio,
    P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
    CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
    CD.postcountry, CD.regtype, IFNULL(A.answercount, 0) AS answercount,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM
    Participants P
	JOIN CongoDump CD ON P.badgeid = CD.badgeid
    LEFT OUTER JOIN AnsweredSurvey A ON (P.badgeid = A.participantid)
    LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
    LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE
	P.badgeid = ?
ORDER BY
	CD.lastname, CD.firstname
EOD;
        } else {
            $query = <<<EOD
SELECT
	P.badgeid, P.pubsname, P.interested, P.bio, P.htmlbio,
    P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
    CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
    CD.postcountry, CD.regtype, IFNULL(A.answercount, 0) AS answercount,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM
    Participants P
	JOIN CongoDump CD ON P.badgeid = CD.badgeid
        LEFT OUTER JOIN (
            SELECT participantid, COUNT(*) AS answercount
                FROM ParticipantSurveyAnswers
                WHERE participantid = ?
    ) A ON (P.badgeid = A.participantid)
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE
	P.badgeid = ?
ORDER BY
	CD.lastname, CD.firstname
EOD;
        }
        $param_arr = array($searchString, $searchString);
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "ss", $param_arr);
    } else {
        $searchString = '%' . strtolower($searchString) . '%';
        if (DBVER >= "8") {
            $query = <<<EOD
WITH AnsweredSurvey(participantid, answercount) AS (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
)
SELECT
	P.badgeid, P.pubsname, P.interested, P.bio, P.htmlbio,
    P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
    CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
    CD.postcountry, CD.regtype, IFNULL(A.answercount, 0) AS answercount,
    P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM
	Participants P
	JOIN CongoDump CD ON P.badgeid = CD.badgeid
    LEFT OUTER JOIN AnsweredSurvey A ON (P.badgeid = A.participantid)
    LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
    LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE
		P.pubsname LIKE ?
	OR CD.lastname LIKE ?
	OR CD.firstname LIKE ?
	OR CD.badgename LIKE ?
ORDER BY
	CD.lastname, CD.firstname
EOD;
        } else {
            $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested, P.bio, P.htmlbio,
        P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
        CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
        CD.postcountry, CD.regtype, IFNULL(A.answercount, 0) AS answercount,
        P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext,
        CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
        R.statustext, D.reasontext
    FROM
                  Participants P
             JOIN CongoDump CD ON P.badgeid = CD.badgeid
        LEFT JOIN (
                SELECT
                        participantid, COUNT(*) AS answercount
                    FROM
                        ParticipantSurveyAnswers
                    GROUP BY
                        participantid
                ) A ON (P.badgeid = A.participantid)
        LEFT JOIN PhotoDenialReasons D USING (photodenialreasonid)
        LEFT JOIN PhotoUploadStatus R USING (photouploadstatus)
    WHERE
           LOWER(P.pubsname) LIKE ?
        OR LOWER(CD.lastname) LIKE ?
        OR LOWER(CD.firstname) LIKE ?
        OR LOWER(CD.badgename) LIKE ?
    ORDER BY
        CD.lastname, CD.firstname;
EOD;
        }
        $param_arr = array($searchString,$searchString,$searchString,$searchString);
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "ssss", $param_arr);
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
    $json_return["HTML"] = RenderXSLT('AdminParticipants.xsl', $paramArray, $xml, true);
    $json_return["rowcount"] = $rows;
    echo json_encode($json_return);
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
    $queryarray = array();
    $paramarray = array();
    $argarray = array();
    if (may_I('EditUserPermRoles')) {
        $loggedInUserBadgeId = $_SESSION['badgeid'];
        ['mayIEditAllRoles' => $mayIEditAllRoles, 'rolesIMayEditArr' => $rolesIMayEditArr] = fetchMyEditableRoles($loggedInUserBadgeId);
        if ($mayIEditAllRoles) {
            $queryarray["permroles"] = <<<EOD
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
            $paramarray["permroles"] = "s";
            $argarray["permroles"] =  array($fetchedUserBadgeId);
        } else { // has permission to edit only specific perm roles
            $queryarray["permroles"] = <<<EOD
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
            $paramarray["permroles"] = "ss";
            $argarray["permroles"] =  array($fetchedUserBadgeId, $loggedInUserBadgeId);
        }
    } else { // has no permission to edit user perm roles
        $queryarray["permroles"] = <<<EOD
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
         $paramarray["permroles"] = "s";
         $argarray["permroles"] =  array($fetchedUserBadgeId);
    }

    $ConStartDatim = CON_START_DATIM;
    $sessionquery = <<<EOD
SELECT POS.badgeid, POS.sessionid, POS.moderator, S.title, R.roomname, R.roomid,
	DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as starttime
FROM ParticipantOnSession POS
JOIN Sessions S ON (S.sessionid = POS.sessionid)
LEFT OUTER JOIN Schedule SCH ON (POS.sessionid = SCH.sessionid)
LEFT OUTER JOIN Rooms R ON (SCH.roomid = R.roomid)
WHERE badgeid = ?;
EOD;
    $queryarray["sessionlist"] = $sessionquery;
    $paramarray["sessionlist"] = "s";
    $argarray["sessionlist"] =  array($fetchedUserBadgeId);
    $resultXML = mysql_prepare_query_XML($queryarray, $paramarray, $argarray);

    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
    }
    //$foo = mb_ereg_replace("<(row|query)([^>]*)/[ ]*>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"); //for debugging only
    //var_error_log($foo);
    RenderXSLT('FetchUserPermRoles.xsl', array(), $resultXML);
}

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
if (!isLoggedIn() || !may_I('Staff')) {
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
