<?php
//	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.

require_once('StaffCommonCode.php');

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

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "" || !isLoggedIn() || !may_I("Administrator")) {
    exit();
}

function filter_participants() {
    global $linki;

    $filterlist = json_decode(getString("filters"));
    $matchall = getString('matchall');
    error_log("filters=");
    var_error_log($filterlist);
    error_log("matchall = " . $matchall);

    // build question where clause
    $andor = $matchall == "true" ? ' AND ' : ' OR ';
    $filterwhere = '';
    $qfilter = array();
    foreach ($filterlist as $filter) {
        if (array_key_exists($filter->questionid, $qfilter))
            $qfilter[$filter->questionid]  .= ',' . $filter->value;
        else
            $qfilter[$filter->questionid] = $filter->value;
    }

    error_log("qfilter");
    var_error_log($qfilter);

    // having built question lookup, now deal with the matches
    foreach($filterlist as $filter) {
        switch ($filter->type) {
            case 'text':
                if ($filterwhere != "")
                    $filterwhere .= $andor;
                $filterwhere .= "(SA.questionid=" . $filter->questionid . " AND SA.value LIKE '%" . $filter->value . "%')\n";
                break;
            case 'min':
            case 'max':
                if ($qfilter[$filter->questionid] != "") {
                    if ($filterwhere != "")
                        $filterwhere .= $andor;
                    $range = explode(",", $qfilter[$filter->questionid]);
                    if (count($range) == 2) {
                        $min = $range[0] > $range[1] ? $range[1] : $range[0];
                        $max = $range[0] < $range[1] ? $range[1] : $range[0];
                        $filterwhere .= "(SA.questionid=" . $filter->questionid . " AND CAST(SA.value AS UNSIGNED) BETWEEN $min AND $max)\n";
                    } else {
                        $filterwhere .= "(SA.questionid=" . $filter->questionid . " AND CAST(SA.value AS UNSIGNED) " .
                            $filter->type == 'min' ? ">= " : "<= " . $range[0] . ")\n";
                    }
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'check':
                if ($qfilter[$filter->questionid] != "") {
                    if ($filterwhere != "")
                        $filterwhere .= $andor;

                    $range = explode(",", $qfilter[$filter->questionid]);
                    $values = "";
                    foreach ($range as $value) {
                        $values .= "CONCAT(',', SA.value, ',') LIKE '%,$value,%' OR ";
                    }
                    $filterwhere .= "(SA.questionid=" . $filter->questionid . " AND (" . mb_substr($values, 0, -3) . "))\n";
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'month':
            case 'year':
                if ($qfilter[$filter->questionid] != "") {
                    if ($filterwhere != "")
                        $filterwhere .= $andor;
                    $range = explode(",", $qfilter[$filter->questionid]);
                    $months = array();
                    $years = array();
                    foreach ($range as $value) {
                        if (is_numeric($value))
                            $years[] = $value;
                        else
                            $months[] = $value;
                    }
                    $values = "(";
                    foreach ($months as $value) {
                        $values .= "SA.value LIKE '$value %' OR ";
                    }
                    $values = mb_substr($values, 0, -3) . ") AND (";
                    foreach ($years as $value) {
                        $values .= "SA.value LIKE '% $value' OR ";
                    }
                    $values = mb_substr($values, 0, -3) . ")";
                    $filterwhere .= "(SA.questionid=" . $filter->questionid . " AND " . $values . ")\n";
                    $qfilter[$filter->questionid] = "";
                }
                break;
        }
    }

    $query = <<<EOD
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
    IF ($filterwhere != "") {
        $query .= "\n\tJOIN ParticipantSurveyAnswers SA ON (P.badgeid=SA.participantid)\n";
        $filterwhere = "AND (\n" . $filterwhere . ")";
    }
    $query .= <<<EOD
    WHERE
        P.interested=1 $filterwhere
    ORDER BY
        IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;

    error_log("\nquery: $query\n\n");

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