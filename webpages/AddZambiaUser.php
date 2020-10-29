<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-10-28
global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Add Zambia User";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;
$textcontents = 'hidden-empty';
$selected = '';
$paramArray = array();

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
	if (isset($_POST["PostCheck"])) {
		$priorValues = interpretControlString($_POST["control"], $_POST["controliv"]);

		if ($priorValues["getSessionID"] !=  session_id()) {
            $message = "Session expired, no text updated";
        } else {

            if ($_POST["firstname"])
				$paramArray["firstname"] = $_POST["firstname"];
			if ($_POST["lastname"])
				$paramArray["lastname"] = $_POST["lastname"];
			if ($_POST["badgename"])
				$paramArray["badgename"] = $_POST["badgename"];
			if ($_POST["pubsname"])
				$paramArray["pubsname"] = $_POST["pubsname"];
			if ($_POST["phone"])
				$paramArray["phone"] = $_POST["phone"];
			if ($_POST["email"])
                $paramArray["email"] = $_POST["email"];
			if ($_POST["postaddress1"])
                $paramArray["postaddress1"] = $_POST["postaddress1"];
			if ($_POST["postaddress2"])
                $paramArray["postaddress2"] = $_POST["postaddress2"];
			if ($_POST["postcity"])
                $paramArray["postcity"] = $_POST["postcity"];
			if ($_POST["poststate"])
                $paramArray["poststate"] = $_POST["poststate"];
			if ($_POST["postzip"])
                $paramArray["postzip"] = $_POST["postzip"];
			if ($_POST["postcountry"])
                $paramArray["postcountry"] = $_POST["postcountry"];
			if ($_POST["email"])
                $paramArray["email"] = $_POST["email"];
			if ($_POST["regtype"])
				$paramArray["selected"] = $_POST["regtype"];

			if ($paramArray["badgename"] == "") {
				$paramArray["badgename"] = trim($paramArray["firstname"] . " " . $paramArray["lastname"]);
            }
			if ($paramArray["pubsname"] == "")
                $paramArray["pubsname"] = $paramArray["badgename"];

			if ($paramArray["firstname"] != "" && $paramArray["lastname"] != "" && $paramArray["email"] != "") {
				if ($_POST["override"] != 1) {
					$query =<<<EOD
SELECT badgeid, firstname, lastname, email, badgename
FROM CongoDump
WHERE email = ?
EOD;
					$sel_array = array($paramArray["email"]);
					$result = mysqli_query_with_prepare_and_exit_on_error($query, "s", $sel_array);
					while ($row = mysqli_fetch_assoc($result)) {
						$message .= "User found matching this email address: Badgeid: " .
							$row["badgeid"] . ": " . $row["firstname"] . " " . $row["lastname"] . ", " . $row["email"] . ", Badgename: " . $row["badgename"] . "<br>";
					}
					mysqli_free_result($result);

                    $query = <<<EOD
SELECT badgeid, firstname, lastname, email, badgename
FROM CongoDump
WHERE firstname = ? and lastname = ? and email != ?
EOD;

					$sel_array = array($paramArray["firstname"], $paramArray["lastname"], $paramArray["email"]);
					$result = mysqli_query_with_prepare_and_exit_on_error($query, "sss", $sel_array);
					while ($row = mysqli_fetch_assoc($result)) {
						$message .= "User found matching this first name and last name: Badgeid: " .
							$row["badgeid"] . ": " . $row["firstname"] . " " . $row["lastname"] . ", " . $row["email"] . ", Badgename: " . $row["badgename"] . "<br>";
					}
					mysqli_free_result($result);

					if ($message != "") {
						$message .= "<br>Set Override to Yes to add this user anyway";
						$paramArray["override"] = "0";
					}
                }
                if ($message == "") {
					$passwordhash = password_hash(trim($_POST['firstname']), PASSWORD_DEFAULT);
					$badgeid = $_POST["badgeid"];

					$query = "INSERT INTO Participants (badgeid, password, pubsname) VALUES (?,?,?);";
					$upd_array = array($badgeid,$passwordhash, $paramArray["pubsname"]);
					$rows = mysql_cmd_with_prepare($query, "sss", $upd_array);
					if (is_null($rows))
						$message = "Failed adding Participant, User not added<br>";
					else if ($rows != 1)
						$message = "Failed adding Participant, User not added<br>";

					$query = "INSERT INTO CongoDump (badgeid, firstname, lastname, badgename, email";
					$query_end = " VALUES(?,?,?,?,?";
					$upd_array = array($badgeid, $paramArray["firstname"], $paramArray["lastname"], $paramArray["badgename"], $paramArray["email"]);
					$typestr = "sssss";
					if ($_POST["phone"]) {
                        $query .= ", phone";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["phone"]);
                    }
					if ($_POST["postaddress1"]) {
                        $query .= ", postaddress1";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["postaddress1"]);
                    }
					if ($_POST["postaddress2"]) {
                        $query .= ", postaddress2";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["postaddress2"]);
                    }
					if ($_POST["postcity"]) {
                        $query .= ", postcity";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["postcity"]);
                    }
					if ($_POST["poststate"]) {
                        $query .= ", poststate";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["poststate"]);
                    }
					if ($_POST["postzip"]) {
                        $query .= ", postzip";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["postzip"]);
                    }
					if ($_POST["postcountry"]) {
                        $query .= ", postcountry";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["postcountry"]);
                    }
					if ($_POST["regtype"]) {
                        $query .= ", regtype";
						$query_end .= ",?";
						$typestr .= "s";
						array_push($upd_array, $paramArray["selected"]);
                    }

					$query .= ") " . $query_end . ");";
					$rows = mysql_cmd_with_prepare($query, $typestr, $upd_array);
					if (is_null($rows))
						$message .= "Failed adding Registration Data, User not added correctly - get help<br>";
					else if ($rows != 1)
						$message .= "Failed adding Registration Data, User not added correctly - get help<br>";

					if ($paramArray["selected"] == REG_STAFF_COMP) {
						$query = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES (?,2);";
						$upd_array = array($badgeid);
						$typestr = "s";
						$rows = mysql_cmd_with_prepare($query, $typestr, $upd_array);
						if (is_null($rows))
							$message .= "Failed adding Staff Role - get help<br>";
						else if ($rows != 1)
							$message .= "Failed adding Staff Role - get help<br>";
					}

					$query = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES (?,3);";
					$upd_array = array($badgeid);
					$typestr = "s";
					$rows = mysql_cmd_with_prepare($query, $typestr, $upd_array);
					if (is_null($rows))
						$message .= "Failed adding Program Participant Role - get help<br>";
					else if ($rows != 1)
						$message .= "Failed adding Program Participant Role - get help<br>";

					if ($message == "") {
						$message = "User " . $badgeid . ": " . $paramArray["firstname"] . " " . $paramArray["lastname"] . ", " .
							$paramArray["email"] . " added successfully";
						$paramArray = array(); // Empty the array to start fresh with a new user.
					}
                }
			} else {
				$message = "At least Firstname, Last Name, and Email must be provided - User not added";
            }
        }
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
	if ($last_badgeid == "")
        $last_badgeid = REG_PART_PREFIX . "1000";

	$id = substr($last_badgeid, strlen(REG_PART_PREFIX));
	$new_badgeid = REG_PART_PREFIX . strval($id + 1);

    $query=<<<EOD
SELECT
		regtype,message
	FROM
		RegTypes
EOD;

	$result = mysqli_query_exit_on_error($query);
	$resultXML = mysql_result_to_XML("regtypes", $result);
	mysqli_data_seek($result, 0);

	$PriorArray["getSessionID"] = session_id();
	$PriorArray["new_badgeid"] = $new_badgeid;

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];
	$paramArray["new_badgeid"] = $new_badgeid;

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }
	// following line for debugging only
	// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('AddZambiaUser.xsl', $paramArray, $resultXML);
}
staff_footer();
?>