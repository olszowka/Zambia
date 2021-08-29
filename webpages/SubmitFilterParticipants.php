<?php
//	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php');
require_once('surveyFilterBuild.php');

function filter_participants() {
    global $linki;

    $filterlist = json_decode(getString("filters"));
    $matchall = getString('matchall');
    $source = getString("source");
    //error_log("source = $source");
    //error_log("filters=");
    //var_error_log($filterlist);
    //error_log("matchall = " . $matchall);

    // build question filter clauses
    $andor = $matchall == "true" ? ' AND ' : ' OR ';
    $qcte = survey_filter_prepare_filter($filterlist, $andor);
    $query = "";
    if (DBVER >= "8")
        $query = survey_filter_build_cte($qcte);

    if ($source == "invite") {
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
    }
    if ($source == 'assign') {
        $selsessionid = getString("sessionid");
        if (DBVER >= "8") {
            if ($query == '')
                $query .= "WITH ";
            else
                $query .= ", ";

            $query .= <<<EOD
AnsweredSurvey(participantid, answercount) AS (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
), SessionParticipants(badgeid) AS (
    SELECT badgeid
    FROM ParticipantSessionInterest
    WHERE sessionid = $selsessionid
)
SELECT
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.badgeid,
    P.pubsname,
    CONCAT(CASE
        WHEN P.pubsname != "" THEN P.pubsname
        WHEN CD.lastname != "" THEN CONCAT(CD.lastname, ", ", CD.firstname)
        ELSE CD.firstname
    END, ' (', CD.badgename, ') - ', P.badgeid) AS name,
    IFNULL(A.answercount, 0) as answercount
FROM Participants P
JOIN CongoDump CD USING(badgeid)

EOD;
        } else {
            $query .= <<<EOD
SELECT
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.badgeid,
    P.pubsname,
    CONCAT(CASE
        WHEN P.pubsname != "" THEN P.pubsname
        WHEN CD.lastname != "" THEN CONCAT(CD.lastname, ", ", CD.firstname)
        ELSE CD.firstname
    END, ' (', CD.badgename, ') - ', P.badgeid) AS name,
    IFNULL(A.answercount, 0) as answercount
FROM Participants P
JOIN CongoDump CD USING(badgeid)

EOD;

        }
        $query .= survey_filter_build_join_subquery($qcte);
        $query .= <<<EOD
LEFT OUTER JOIN (
 SELECT badgeid
    FROM ParticipantSessionInterest
    WHERE sessionid = $selsessionid
) S ON (P.badgeid = S.badgeid)
LEFT OUTER JOIN (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
) A ON (P.badgeid = A.participantid)
WHERE P.interested = 1 AND S.badgeid IS NULL

EOD;
        $query .= survey_filter_build_where($qcte, $andor);
        $query .= <<<EOD
ORDER BY
	IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
    }
    //error_log("\nquery: $query\n\n");

    $result = mysqli_query_exit_on_error($query);
	$participants = array();
    if ($source == 'invite') {
        $select = '<select id="participant-select" name="selpart">' . "\n" .
            '<option value="" selected="selected" disabled="true">Select Participant</option>' . "\n";
    }
    if ($source == 'assign') {
        $select = '<select id="partDropdown" name="asgnpart">' . "\n" .
            '<option value="" selected="selected" disabled="true">Assign Participant</option>' . "\n";
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $participants[] =  $row;
        $pubsname = $row["pubsname"];
        if (mb_ereg_match("\w", $pubsname)) {
            $pattern = "(.*)(\b" . preg_quote($row["lastname"]) . "\b)(.*)";
            if (mb_ereg($pattern, $pubsname, $regexArr)) {
                $sortableName = $regexArr[2] . ($regexArr[3] ? $regexArr[3] : "") . ", " . $regexArr[1];
            } else {
                $sortableName = $pubsname;
            }
        } else {
            $sortableName = $pubsname;
        }
        $select .= '<option value="' . $row["badgeid"] . '">' . $sortableName . ' - ' . $row["badgeid"] . "</option>\n";
    }
    $select .= "</select>\n";
	mysqli_free_result($result);
	$json_return["participants"] = $participants;
    $json_return["select"] = $select;
    echo json_encode($json_return);
}

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;

$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "" || !isLoggedIn() || !may_I("Staff")) {
    exit();
}

error_log("\Request action: $ajax_request_action\n\n");
switch ($ajax_request_action) {
    case "filter":
        filter_participants();
        break;
    default:
        $message_error = "Internal error: $ajax_request_action.";
        RenderErrorAjax($message_error);
        exit();
}

?>
