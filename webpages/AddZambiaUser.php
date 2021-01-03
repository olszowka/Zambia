<?php
// Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-10-28

function insert_user() {
    global $message, $message_error, $paramArray;
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
    // permroleid 3 is Participant
    $query = <<<EOD
INSERT INTO UserHasPermissionRole
    (badgeid, permroleid)
    VALUES (?, 3);
EOD;
    $rows = mysql_cmd_with_prepare($query, "s", array($paramArray["badgeid"]));
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
if (!may_I('CreateUser')) {
    $message_error = "You do not have permission to access this page.";
    StaffRenderErrorPage($title, $message_error, true);
    exit();
}
$bootstrap4 = true;
staff_header($title, $bootstrap4);
if (array_key_exists("PostCheck", $_POST)) {
    $insert_successful = insert_user();
}

// Start of display portion

	$query=<<<EOD
SELECT
		MAX(badgeid) M
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
// following line for debugging only
// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
RenderXSLT('AddZambiaUser.xsl', $paramArray);
staff_footer();
?>