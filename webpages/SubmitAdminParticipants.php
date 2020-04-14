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
        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
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
    $query = "UPDATE Participants SET ";
    if ($password) {
        $query .= "password=\"" . md5($password) . "\", ";
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
    $query .= " WHERE badgeid=\"$partid\"";
    if (!mysqli_query_with_error_handling($query)) {
        return;
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
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
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
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
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
