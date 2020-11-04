<?php
// Copyright (c) 2006-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('StaffCommonCode.php');

// gets data for a participant to be displayed.  Returns as XML
function fetch_participant() {
    global $message_error;
    $fbadgeid = getString("badgeid");
    if (!$fbadgeid) {
        exit();
    }
    $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested,
        CASE WHEN ISNULL(P.htmlbio) THEN P.bio ELSE P.htmlbio END AS htmlbio,
        CASE WHEN ISNULL(P.bio) THEN P.htmlbio ELSE P.bio END AS bio,
        P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.phone, CD.email, CD.postaddress1,
        CD.postaddress2, CD.postcity, CD.poststate, CD.postzip, CD.postcountry, CD.regtype
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
    $partid = getString("badgeid");
    $password = getString("password");
    $biodirty = isset($_POST["htmlbio"]);
    // $bio = getString("bio");
    $htmlbio = getString("htmlbio");
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


    $query_end = " WHERE badgeid = ?;";

    if ($password || $biodirty || $pubsnamedirty || $staffnotesdirty || $interested)  {
         $query = "UPDATE Participants SET ";
         $update_arr = array();
         $updateStr = "s";
        if ($password) {
            $query .= "password=?, ";
            array_push($update_arr, password_hash($password, PASSWORD_DEFAULT));
            $updateStr .= "s";
        }
        if ($biodirty) {
            $query .= "htmlbio=?, ";
            array_push($update_arr, $htmlbio);
            $updateStr .= "s";
            $query .= "bio=?, ";
            array_push($update_arr, strip_tags($htmlbio));
            $updateStr .= "s";
        }
        if ($pubsnamedirty) {
            $query .= "pubsname=?, ";
            array_push($update_arr, $pubsname);
            $updateStr .= "s";
        }
        if ($staffnotesdirty) {
            $query .= "staff_notes=?, ";
            array_push($update_arr, $staffnotes);
            $updateStr .= "s";
        }
        if ($interested) {
            $query .= "interested=?, ";
            array_push($update_arr, $interested);
            $updateStr .= "s";
        }
        $query = mb_substr($query, 0, -2); //drop two characters at end: ", "
        $query .= $query_end;
        array_push($update_arr, $partid);
        $rows = mysql_cmd_with_prepare($query, $updateStr, $update_arr);
        if (is_null($rows)) {
            $message_error .= "Failed updating participation record, seek assistance.";
            RenderErrorAjax($message_error);
            return;
        } else if ($rows < 0) {
            $message_error .= "Failed updating participation record, seek assistance.";
            RenderErrorAjax($message_error);
            return;
        }
    }

    if ($lastnameDirty || $firstnameDirty || $bnameDirty || $phoneDirty || $emailDirty || $post1Dirty || $post2Dirty || $postcityDirty || $poststateDirty || $postzipDirty || $postcountryDirty) {

        mysqli_query_with_error_handling("INSERT INTO CongoDumpHistory (
            badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,
            postcity,poststate,postzip,postcountry,regtype,loginid
        )
        SELECT badgeid,firstname,lastname,badgename,phone,email,postaddress1,postaddress2,
            postcity,poststate,postzip,postcountry,regtype, \"" . $_SESSION['badgeid'] . "\" FROM CongoDump WHERE badgeid = \"" .
            mysqli_real_escape_string($linki, $partid) . "\";", true, true);
        $query = "UPDATE CongoDump SET ";
        $update_arr = array();
        $updateStr = "s";
        if ($lastnameDirty) {
	        $query .= "lastname=?, ";
            array_push($update_arr, $lastname);
            $updateStr .= "s";
        }
        if ($firstnameDirty) {
	        $query .= "firstname=?, ";
            array_push($update_arr, $firstname);
            $updateStr .= "s";
        }
        if ($bnameDirty) {
	        $query .= "badgename=?, ";
            array_push($update_arr, $bname);
            $updateStr .= "s";
        }
        if ($phoneDirty) {
	        $query .= "phone=?, ";
            array_push($update_arr, $phone);
            $updateStr .= "s";
        }
        if ($emailDirty) {
	        $query .= "email=?, ";
            array_push($update_arr, $email);
            $updateStr .= "s";
        }
        if ($post1Dirty) {
	        $query .= "postaddress1=?, ";
            array_push($update_arr, $postaddress1);
            $updateStr .= "s";
        }
        if ($post2Dirty) {
	        $query .= "postaddress2=?, ";
            array_push($update_arr, $postaddress2);
            $updateStr .= "s";
        }
        if ($postcityDirty) {
	        $query .= "postcity=?, ";
            array_push($update_arr, $postcity);
            $updateStr .= "s";
        }
        if ($poststateDirty) {
	        $query .= "poststate=?, ";
            array_push($update_arr, $poststate);
            $updateStr .= "s";
        }
        if ($postzipDirty) {
	        $query .= "postzip=?, ";
            array_push($update_arr, $postzip);
            $updateStr .= "s";
        }
        if ($postcountryDirty) {
	        $query .= "postcountry=?, ";
            array_push($update_arr, $postcountry);
            $updateStr .= "s";
        }

        $query = mb_substr($query, 0, -2); //drop two characters at end: ", "
        $query .= $query_end;
        array_push($update_arr, $partid);
        $rows = mysql_cmd_with_prepare($query, $updateStr, $update_arr);
        if (is_null($rows)) {
            $message_error .= "Failed updating registration record, seek assistance.";
            RenderErrorAjax($message_error);
            return;
        } else if ($rows < 0) {
            $message_error .= "Failed updating registration record, seek assistance.";
            RenderErrorAjax($message_error);
            return;
        }
    }
    $message = "<p class=\"alert alert-success\">Database updated successfully.</p>";
    if ($interested == 2) {
        $query = <<<EOD
UPDATE ParticipantOnSessionHistory
    SET inactivatedts = NOW(), inactivatedbybadgeid = ?
	WHERE
	        badgeid = ?
		AND inactivatedts IS NULL;
EOD;
        $update_arr = array($_SESSION['badgeid'],$partid);
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
        $message .= "<p class=\"alert alert-info\">Participant removed from " . $rows . " session(s).</p>";
    }
    echo $message;
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
			        P.badgeid, P.pubsname, P.interested,
                    CASE WHEN ISNULL(P.htmlbio) THEN P.bio ELSE P.htmlbio END AS htmlbio,
                    CASE WHEN ISNULL(P.bio) THEN P.htmlbio ELSE P.bio END AS bio,
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
			        P.badgeid, P.pubsname, P.interested,
                    CASE WHEN ISNULL(P.htmlbio) THEN P.bio ELSE P.htmlbio END AS htmlbio,
                    CASE WHEN ISNULL(P.bio) THEN P.htmlbio ELSE P.bio END AS bio,
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
