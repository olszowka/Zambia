<?php
// Copyright (c) 2006-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('StaffCommonCode.php');

// gets data for a participant to be displayed.  Returns as XML
function fetch_participant() {
    global $message_error;
    $fbadgeid = getInt("badgeid");
    if (!$fbadgeid) {
        exit();
    }
    $query["fetchParticipants"] = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
        CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip, CD.postcountry, CD.regtype
    FROM
			 Participants P
		JOIN CongoDump CD ON P.badgeid = CD.badgeid
    WHERE
        P.badgeid = "$fbadgeid"
    ORDER BY
        CD.lastname, CD.firstname
EOD;
    $resultXML = mysql_query_XML($query);
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
    $partid = mysqli_real_escape_string($linki, getString("badgeid"));
    $password = getString("password");
    $biodirty = isset($_POST["bio"]);
    $bio = getString("bio");
    $pubsnamedirty = isset($_POST["pname"]);
    $pubsname = getString("pname");
    $staffnotesdirty = isset($_POST["staffnotes"]);
    $staffnotes = getString("staffnotes");
    $interested = getInt("interested", "");
    $lastnameDirty = isset($_POST["lastname"]);
    $firstnameDirty = isset($_POST["firstname"]);
    $bnameDirty = isset($_POST["bname"]);
    $phoneDirty = isset($_POST["phone"]);
    $emailDirty = isset($_POST["email"]);
    $post1Dirty = isset($_POST["postaddress1"]);
    $post2Dirty = isset($_POST["postaddress2"]);
    $postcityDirty = isset($_POST["postcity"]);
    $poststateDirty = isset($_POST["poststate"]);
    $postzipDirty = isset($_POST["postzip"]);
    $postcountryDirty = isset($_POST["postcountry"]);
    $lastname = getString("lastname");
    $firstname = getString("firstname");
    $bname = getString("bname");
    $phone = getString("phone");
    $email = getString("email");
    $postaddress1 = getString("postaddress1");
    $postaddress2 = getString("postaddress2");
    $postcity = getString("postcity");
    $poststate = getString("poststate");
    $postzip = getString("postzip");
    $postcountry = getString("postcountry");


    $query_end = " WHERE badgeid = '$partid';";

    if ($password || $biodirty || $pubsnamedirty || $staffnotesdirty || $interested)  {
         $query = "UPDATE Participants SET ";
        if ($password) {
            $query .= "password=\"" . password_hash($password, PASSWORD_DEFAULT) . "\", ";
        }
        if ($biodirty) {
            $query .= "bio=\"" . mysqli_real_escape_string($linki, $bio) . "\", ";
        }
        if ($pubsnamedirty) {
            $query .= "pubsname=\"" . mysqli_real_escape_string($linki, $pubsname) . "\", ";
        }
        if ($staffnotesdirty) {
            $query .= "staff_notes=\"" . mysqli_real_escape_string($linki, $staffnotes) . "\", ";
        }
        if ($interested) {
            $query .= "interested=" . mysqli_real_escape_string($linki, $interested) . ", ";
        }
        $query = mb_substr($query, 0, -2); //drop two characters at end: ", "
        $query .= $query_end;
        if (!mysqli_query_with_error_handling($query)) {
            return;
        }
    }

    if ($lastnameDirty || $firstnameDirty || $bnameDirty || $phoneDirty || $emailDirty || $post1Dirty || $post2Dirty || $postcityDirty || $poststateDirty || $postzipDirty || $postcountryDirty) {

        mysqli_query_with_error_handling("INSERT INTO CongoDumpHistory (
            badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,
            postcity,poststate,postzip,postcountry,regtype,loginid
        )
        SELECT badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,
            postcity,poststate,postzip,postcountry,regtype, \"" . $_SESSION['badgeid'] . "\" FROM CongoDump " . $query_end, true, true);
        $query = "UPDATE CongoDump SET ";
        if ($lastnameDirty) {
	        $query .= "lastname=\"" . mysqli_real_escape_string($linki, $lastname) . "\", ";
        }
        if ($firstnameDirty) {
	        $query .= "firstname=\"" . mysqli_real_escape_string($linki, $firstname) . "\", ";
        }
        if ($bnameDirty) {
	        $query .= "badgename=\"" . mysqli_real_escape_string($linki, $bname) . "\", ";
        }
        if ($phoneDirty) {
	        $query .= "phone=\"" . mysqli_real_escape_string($linki, $phone) . "\", ";
        }
        if ($emailDirty) {
	        $query .= "email=\"" . mysqli_real_escape_string($linki, $email) . "\", ";
        }
        if ($post1Dirty) {
	        $query .= "postaddress1=\"" . mysqli_real_escape_string($linki, $postaddress1) . "\", ";
        }
        if ($post2Dirty) {
	        $query .= "postaddress2=\"" . mysqli_real_escape_string($linki, $postaddress2) . "\", ";
        }
        if ($postcityDirty) {
	        $query .= "postcity=\"" . mysqli_real_escape_string($linki, $postcity) . "\", ";
        }
        if ($poststateDirty) {
	        $query .= "poststate=\"" . mysqli_real_escape_string($linki, $poststate) . "\", ";
        }
        if ($postzipDirty) {
	        $query .= "postzip=\"" . mysqli_real_escape_string($linki, $postzip) . "\", ";
        }
        if ($postcountryDirty) {
	        $query .= "postcountry=\"" . mysqli_real_escape_string($linki, $postcountry) . "\", ";
        }
        $query = mb_substr($query, 0, -2); //drop two characters at end: ", "
        $query .= $query_end;
        if (!mysqli_query_with_error_handling($query)) {
            return;
        }
    }
    $message = "<p class=\"alert alert-success\">Database updated successfully.</p>";
    if ($interested == 2) {
        $query = <<<EOD
UPDATE ParticipantOnSessionHistory
    SET inactivatedts = NOW(), inactivatedbybadgeid = "{$_SESSION['badgeid']}"
	WHERE
	        badgeid = "$partid"
		AND inactivatedts IS NULL;
EOD;
        if (!mysqli_query_with_error_handling($query)) {
            return;
        }
        $message .= "<p class=\"alert alert-info\">Participant removed from " . mysqli_affected_rows($linki) . " session(s).</p>";
    }
    echo $message;
}

function perform_search() {
    global $linki, $message_error;
    $searchString = mysqli_real_escape_string($linki, (getString("searchString")));
    if ($searchString == "")
        exit();
    if (is_numeric($searchString)) {
        $query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
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
    } else {
        $searchString = '%' . $searchString . '%';
        $query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename,
                    CD.phone, CD.email, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
                    CD.postcountry, CD.regtype
			    FROM
						 Participants P
					JOIN CongoDump CD ON P.badgeid = CD.badgeid
			    WHERE
			           P.pubsname LIKE "$searchString"
					OR CD.lastname LIKE "$searchString"
					OR CD.firstname LIKE "$searchString"
					OR CD.badgename LIKE "$searchString"
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
    }
    $xml = mysql_query_XML($query);
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
    //echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xml->saveXML(), "i")); //for debugging only
    RenderXSLT('AdminParticipants.xsl', array(), $xml);
	exit();
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "") {
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
    default:
        exit();
}

?>
