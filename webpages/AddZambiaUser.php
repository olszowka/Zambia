<?php
// Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-10-28
require('EditPermRoles_FNC.php');

function insert_user($mayIEditAllRoles, $rolesIMayEditArr) {
    global $linki, $message, $message_error, $paramArray;
    $message_error = "";
    $paramArray['firstname'] = getString("firstname");
    $paramArray['lastname'] = getString("lastname");
    $paramArray['badgename'] = getString("badgename");
    $paramArray['pubsname'] = getString("pubsname");
    $paramArray['phone'] = getString("phone");
    $paramArray['email'] = getString("email");
    $paramArray['postaddress1'] = getString("postaddress1");
    $paramArray['postaddress2'] = getString("postaddress2");
    $paramArray['postcity'] = getString("postcity");
    $paramArray['poststate'] = getString("poststate");
    $paramArray['postzip'] = getString("postzip");
    $paramArray['postcountry'] = getString("postcountry");
    $paramArray['override'] = getInt("override");
    $paramArray['permissionRoles'] = getArrayOfInts("permissionRoles");

    if ($paramArray['badgename'] === "") {
        $paramArray['badgename'] = trim($paramArray['firstname'] . " " . $paramArray['lastname']);
    }
    if ($paramArray['pubsname'] === "")
        $paramArray['pubsname'] = $paramArray['badgename'];
    if (empty($paramArray['firstname']) || empty($paramArray['lastname']) || empty($paramArray['email'])) {
        $message_error = "First name, last name, and email address are required.";
        return false;
    }
    if ($paramArray['override'] !== 1) {
        $query =<<<EOD
SELECT
        badgeid, firstname, lastname, email, badgename
    FROM
        CongoDump
    WHERE
        email = ?
EOD;
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "s", array($paramArray['email']));
        if (!$result) {
            exit();
        }
        $error = false;
        while ($row = mysqli_fetch_assoc($result)) {
            $error = true;
            $message_error .= "User found matching this email address: Badgeid: {$row["badgeid"]}: {$row["firstname"]} " .
                "{$row["lastname"]}, {$row["email"]}, Badgename: {$row["badgename"]} <br />\n";
        }
        mysqli_free_result($result);
        $query = <<<EOD
SELECT
        badgeid, firstname, lastname, email, badgename
    FROM
        CongoDump
    WHERE
            firstname = ?
        AND lastname = ?
        AND email != ?
EOD;
        $result = mysqli_query_with_prepare_and_exit_on_error($query, "sss",
            array($paramArray['firstname'], $paramArray['lastname'], $paramArray['email']));
        if (!$result) {
            exit();
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $error = true;
            $message_error .= "User found matching this first name and last name: Badgeid: {$row["badgeid"]}: {$row["firstname"]} " .
                "{$row["lastname"]}, {$row["email"]}, Badgename: {$row["badgename"]} <br />\n";
        }
        if (!$paramArray['permissionRoles'] || count($paramArray['permissionRoles']) == 0) {
            $error = true;
            $message_error .= "If you don't assign the user any roles, they will not be able to log in.<br />\n";
        }
        mysqli_free_result($result);
        if ($error) {
            $message_error .= "Set Override to Yes to add this user anyway.";
            $paramArray['override'] = 1;
            return false;
        }
    }
    if (!empty(DEFAULT_USER_PASSWORD) && !RESET_PASSWORD_SELF) {
        $passwordhash = password_hash(DEFAULT_USER_PASSWORD, PASSWORD_DEFAULT);
    } else {
        $passwordhash = '';
    }
    $paramArray["badgeid"] = getString('badgeid');
    $query = <<<EOD
INSERT INTO Participants (badgeid, password, pubsname)
    VALUES (?, ?, ?);
EOD;
    $rows = mysql_cmd_with_prepare($query, "sss", array($paramArray["badgeid"], $passwordhash, $paramArray["pubsname"]));
    if (is_null($rows) || $rows !== 1) {
        $message_error .= "Failed adding Participant, User not added.<br />\n";
        return false;
    }
    $query = <<<EOD
INSERT INTO CongoDump
    (badgeid, firstname, lastname, badgename, email, phone, postaddress1, postaddress2, postcity, poststate, postzip, postcountry)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
EOD;
    $upd_array = array($paramArray["badgeid"], $paramArray["firstname"], $paramArray["lastname"], $paramArray["badgename"],
        $paramArray["email"], $paramArray["phone"], $paramArray["postaddress1"], $paramArray["postaddress2"], $_POST["postcity"],
        $paramArray["poststate"], $paramArray["postzip"], $paramArray["postcountry"]);
    $rows = mysql_cmd_with_prepare($query, "ssssssssssss", $upd_array);
    if (is_null($rows) || $rows !== 1) {
        $message_error .= "Failed adding Registration Data, User not added correctly - get help.<br />\n";
        return false;
    }

    if (!$mayIEditAllRoles) {
        if (($paramArray['permissionRoles'] && count(array_diff($paramArray['permissionRoles'], $rolesIMayEditArr))) > 0) {
            $message_error .= "Server configuration error: You attempting to add roles you do not have permission to add. Seek assistance.<br />\n";
            return false;
        }
    }
    if ($paramArray['permissionRoles'] != false) {
        $badgeIdSafe = mysqli_real_escape_string($linki, $paramArray["badgeid"]);
        $rolesToAddList = implode(',', array_map(function ($role) use ($badgeIdSafe) {
            return "('$badgeIdSafe', $role)";
        }, $paramArray['permissionRoles']));
        $query = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES $rolesToAddList;";
        $result = mysqli_query_exit_on_error($query);
        if (!$result) {
            exit(); // should have exited already
        }
        $updatePerformed = true;
    }
    if (is_null($rows) || $rows !== 1) {
        $message_error .= "Failed adding Program Participant Role - get help.<br />\n";
        return false;
    }

    // TODO: Add code to support other roles if user has permission and data set.

    $message = "User {$paramArray["badgeid"]}: {$paramArray["firstname"]} {$paramArray["lastname"]}, " .
        "{$paramArray["email"]} added successfully.\n";
    $paramArray = array(); // Empty the array to start fresh with a new user.
    return true;
}

// Start here

global $message, $message_error, $paramArray, $title, $linki;
$title = "Add Zambia User";
require_once('StaffCommonCode.php'); // Checks for staff permission among other things
$message = "";
$paramArray = array();
if (!may_I('CreateUser') || !may_I('EditUserPermRoles')) {
    $message_error = "You do not have permission to access this page.";
    StaffRenderErrorPage($title, $message_error, true);
    exit();
}
$loggedInUserBadgeId = $_SESSION['badgeid'];
['mayIEditAllRoles' => $mayIEditAllRoles, 'rolesIMayEditArr' => $rolesIMayEditArr] = fetchMyEditableRoles($loggedInUserBadgeId);
staff_header($title, true);
if (array_key_exists("PostCheck", $_POST)) {
    $insert_successful = insert_user($mayIEditAllRoles, $rolesIMayEditArr);
}

// Start of display portion

	$prefix_len = mb_strlen(REG_PART_PREFIX) + 1;
	$query=<<<EOD
SELECT
		MAX(CONVERT(SUBSTRING(badgeid, $prefix_len), UNSIGNED)) M
	FROM
		CongoDump
	WHERE badgeid LIKE '
EOD;
$query .= REG_PART_PREFIX . "%'";
$last_badgeid = "";

$result = mysqli_query_exit_on_error($query);
while ($row = mysqli_fetch_assoc($result)) {
   $last_badgeid = $row["M"];
}
mysqli_free_result($result);
if ($last_badgeid == "") {
    $last_badgeid = REG_PART_PREFIX . "1000";
}

$id = mb_substr($last_badgeid, mb_strlen(REG_PART_PREFIX));
$new_badgeid = REG_PART_PREFIX . strval(intval($id) + 1);

$PriorArray["new_badgeid"] = $new_badgeid;

$ControlStrArray = generateControlString($PriorArray);
$paramArray["control"] = $ControlStrArray["control"];
$paramArray["controliv"] = $ControlStrArray["controliv"];
$paramArray["new_badgeid"] = $new_badgeid;
$paramArray["updateMessage"] = $message;
$paramArray["errorMessage"] = $message_error;
$queryArr = array();
if ($mayIEditAllRoles) {
    $queryArr['roles'] = <<<EOD
SELECT
        PR.permrolename, PR.permroleid
    FROM
        PermissionRoles PR
    ORDER BY
        PR.display_order;
EOD;
    $XMLDoc = mysql_query_XML($queryArr);
} else {
    $queryArr['roles'] = <<<EOD
SELECT DISTINCT
        PR.permrolename, PR.permroleid
    FROM
             UserHasPermissionRole UHPR
        JOIN Permissions P USING (permroleid)
        JOIN PermissionAtoms PA USING (permatomid)
        JOIN PermissionRoles PR ON PR.permroleid = PA.elementid
    WHERE
            UHPR.badgeid = ?
        AND PA.permatomtag = 'EditUserPermRoles'
    ORDER BY
        PR.display_order;
EOD;
    $XMLDoc = mysql_prepare_query_XML(
        $queryArr,
        array('roles' => 's'),
        array('roles' => array($loggedInUserBadgeId))
    );
}
if (!array_key_exists('permissionRoles', $paramArray)) {
    $paramArray['permissionRoles'] = array();
}
ArrayToXML('selectedRoles', $paramArray['permissionRoles'], $XMLDoc);
unset($paramArray['permissionRoles']);
// following line for debugging only
// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $XMLDoc->saveXML(), "i"));
RenderXSLT('AddZambiaUser.xsl', $paramArray, $XMLDoc);
staff_footer();
?>
