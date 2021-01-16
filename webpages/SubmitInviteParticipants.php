<?php
//	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.

require_once('StaffCommonCode.php');
require_once('surveyFilterBuild.php');

function invite_participant() {
    global $linki;

    $partbadgeid = mysqli_real_escape_string($linki, getString("selpart"));
    $sessionid = getInt("selsess", 0);
    if (($partbadgeid == '') || ($sessionid == 0)) {
        $message = "<p class=\"alert alert-error\">Database not updated. Select a participant and a session.</p>";
        $alerttype = "warning";
    } else {
        $query = "INSERT INTO ParticipantSessionInterest SET badgeid='$partbadgeid', ";
        $query .= "sessionid=$sessionid;";
        $result = mysqli_query($linki, $query);
        if ($result) {
            $message =  "<p>Database successfully updated.</p>";
        } elseif (mysqli_errno($linki) == 1062) {
            $message =  "<p>Database not updated. That participant was already invited to that session.</p>";
            $alerttype = "warning";
        } else {
            $message = $query . "<p>Database not updated.</p>";
            $alerttype = "danger";
        }
    }
    $json_return = array();
    $json_return["message"] = $message;
    $json_return["alerttype"] = $alerttype;
    echo json_encode($json_return) . "\n";
}

function filter_participants() {
    global $linki;

    $filterlist = json_decode(getString("filters"));
    $matchall = getString('matchall');
    //error_log("filters=");
    //var_error_log($filterlist);
    //error_log("matchall = " . $matchall);

    // build question filter clauses
    $andor = $matchall == "true" ? ' AND ' : ' OR ';
    $qcte = survey_filter_prepare_filter($filterlist, $andor);
    $query = survey_filter_build_cte($qcte);

    $query .= <<<EOD
SELECT DISTINCT
        CD.lastname,
        CD.firstname,
        CD.badgename,
        P.badgeid,
        P.pubsname,
        CONCAT(
            CASE
                WHEN P.pubsname != "" THEN P.pubsname
                WHEN CD.lastname != "" THEN CONCAT(CD.lastname, ", ", CD.firstname)
                ELSE CD.firstname
            END, ' (', CD.badgename, ') - ', P.badgeid) AS name
    FROM
             Participants P
        JOIN CongoDump CD USING(badgeid)
EOD;
    $query .= survey_filter_build_join($qcte);
    $query .= <<<EOD
    WHERE
        P.interested=1
EOD;
    $query .= survey_filter_build_where($qcte, $andor);
    $query .= <<<EOD
    ORDER BY
        IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;

    //error_log("\nquery: $query\n\n");

    $result = mysqli_query_exit_on_error($query);
	$participants = array();
    $select = '<select id="participant-select" name="selpart">' . "\n" .
        '<option value="" selected="selected" disabled="true">Select Participant</option>' . "\n";
    while ($row = mysqli_fetch_assoc($result)) {
        $participants[] =  $row;
        $select .= '<option value="' . $row["badgeid"] . '">' . $row["name"] . "</option>\n";
    }
    $select .= "</select>\n";
	mysqli_free_result($result);
	$json_return["participants"] = $participants;
    $json_return["select"] = $select;
    echo json_encode($json_return);
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "" || !isLoggedIn() || !may_I("Administrator")) {
    exit();
}

switch ($ajax_request_action) {
    case "filter":
        filter_participants();
        break;
    case "invite":
        invite_participant();
        break;
    default:
        exit();
}

?>