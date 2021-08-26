<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Assign Participants";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
require_once('StaffAssignParticipants_FNC.php');
staff_header($title, $bootstrap4);
if (may_I('Staff')) {
    $topsectiononly = true; // no room selected -- flag indicates to display only the top section of the page
    if (isset($_POST["numrows"])) {
        SubmitAssignParticipants();
    }
    $selsessionid = getInt("selsess", 0);
    if ($selsessionid != 0) {
        $topsectiononly = false;
    } else {
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
    }
    $query = <<<EOD
SELECT
        T.trackname,
        S.sessionid,
        S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
    WHERE
        SS.may_be_scheduled=1
    ORDER BY
        T.trackname, S.sessionid, S.title;
EOD;
    $Sresult = mysqli_query_exit_on_error($query);
?>
<form id='selsesform' name='selsesform' method='get' action='StaffAssignParticipants.php'>
  <div class="form-group row mt-4">
    <div class="col-auto">
      <label for='sessionDropdown'>Select Session:</label>
    </div>
    <div class="col col-5">
        <select id='sessionDropdown' name='selsess'>
<?php
    echo "     <option value=0" . (($selsessionid == 0) ? "selected" : "") . ">Select Session</option>\n";
    while (list($trackname, $sessionid, $title) = mysqli_fetch_array($Sresult, MYSQLI_NUM)) {
        echo "     <option value=\"$sessionid\" " . (($selsessionid == $sessionid) ? "selected" : "");
        echo ">" . htmlspecialchars($trackname) . " - ";
        echo htmlspecialchars($sessionid) . " - " . htmlspecialchars($title) . "</option>\n";
    }
    mysqli_free_result($Sresult);
?>
        </select>
    </div>
    <div class="col col-2">
        <button id='sessionBtn' type='submit' name='submit' class='btn btn-primary'>Select Session</button>
    </div>
<?php
    if (isset($_SESSION['return_to_page'])) {
        echo "<div class='col col-auto'><a href=\"" . $_SESSION['return_to_page'] . "\">Return to report</a></div>";
    }
    echo "</div></form>\n";
    if ($topsectiononly) {
        staff_footer();
        exit();
    }

    // check for any survey stuff defined, before doing survey queries
    $sql = "SELECT COUNT(*) AS questions FROM SurveyQuestionConfig WHERE searchable = 1;";
    $result = mysqli_query_exit_on_error($sql);
    $row = mysqli_fetch_assoc($result);
    if ($row)
        $SurveyUsed = $row["questions"]  > 0;
    else
        $SurveyUsed = false;

    mysqli_free_result($result);
    $queryArray["timestampsetup1"] = "SET @maxcr = (SELECT max(createdts) FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid);";
    $queryArray["timestampsetup2"] = "SET @maxin = (SELECT max(inactivatedts) FROM ParticipantOnSessionHistory WHERE sessionid = $selsessionid);";
    $queryArray["maxtimestamp"] = "SELECT IF(@maxcr IS NULL, @maxin, IF(@maxin IS NULL, @maxcr, IF(@maxcr > @maxin, @maxcr, @maxin))) AS maxtimestamp;";
    $queryArray["sessionInfo"] = <<<EOD
SELECT
        S.sessionid,
        S.title,
        S.progguiddesc,
        S.persppartinfo,
        S.notesforpart,
        S.notesforprog,
        CONCAT('Made by: ', SEH.name, ', Email: ', SEH.email_address, ', Date: ', SEH.timestamp) AS sessionhistory
    FROM
             Sessions S
        JOIN SessionEditHistory SEH USING (sessionid)
    WHERE
        S.sessionid=$selsessionid
        AND SEH.sessioneditcode IN (1, 2, 6);
EOD;
    if (DBVER >= "8") {
        $queryArray["participantInterest"] = <<<EOD
WITH AnsweredSurvey(participantid, answercount) AS (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
), R2(badgeid, sessionid) AS (
    SELECT badgeid, sessionid FROM ParticipantOnSession WHERE sessionid=$selsessionid
    UNION
    SELECT badgeid, sessionid FROM ParticipantSessionInterest WHERE sessionid=$selsessionid
), R(badgeid, sessionid) AS (
    SELECT DISTINCT badgeid, sessionid FROM R2
)
SELECT
        POS.badgeid AS posbadgeid,
        COALESCE(POS.moderator, 0) AS moderator,
        P.badgeid,
        P.pubsname,
        P.sortedpubsname,
        P.staff_notes,
        IFNULL(PSI.rank, 99) AS `rank`,
        PSI.willmoderate,
        PSI.comments,
        P.bio,
        PHR.roleid,
        IF(P.interested = 1, 1, 0) AS attending,
        IFNULL(A.answercount, 0) AS answercount
    FROM      Participants AS P
         JOIN R ON (P.badgeid = R.badgeid)
    LEFT JOIN ParticipantSessionInterest AS PSI ON R.badgeid = PSI.badgeid AND R.sessionid = PSI.sessionid
    LEFT JOIN ParticipantOnSession AS POS ON R.badgeid = POS.badgeid AND R.sessionid = POS.sessionid
    LEFT JOIN ParticipantHasRole AS PHR ON P.badgeid = PHR.badgeid and PHR.roleid = 10 /* moderator */
    LEFT JOIN AnsweredSurvey A ON (A.participantid = P.badgeid)
    WHERE
        POS.sessionid = $selsessionid
        OR POS.sessionid IS NULL
    ORDER BY
        attending DESC,
        moderator DESC,
        IFNULL(POS.badgeid, "~") ASC,
        `rank` ASC,
        P.pubsname ASC;
EOD;
    } else {
        $queryArray["participantInterest"] = <<<EOD
SELECT
        POS.badgeid AS posbadgeid,
        COALESCE(POS.moderator, 0) AS moderator,
        P.badgeid,
        P.pubsname,
        P.sortedpubsname,
        P.staff_notes,
        IFNULL(PSI.rank, 99) AS `rank`,
        PSI.willmoderate,
        PSI.comments,
        P.bio,
        PHR.roleid,
        IF(P.interested = 1, 1, 0) AS attending,
        IFNULL(A.answercount, 0) AS answercount
FROM
        Participants AS P
    JOIN (
        SELECT DISTINCT badgeid, sessionid FROM (
            SELECT badgeid, sessionid FROM ParticipantOnSession WHERE sessionid=$selsessionid
                UNION
            SELECT badgeid, sessionid FROM ParticipantSessionInterest WHERE sessionid=$selsessionid
        ) R2
    ) R ON (P.badgeid = R.badgeid)            
LEFT JOIN ParticipantSessionInterest AS PSI ON R.badgeid = PSI.badgeid AND R.sessionid = PSI.sessionid
LEFT JOIN ParticipantOnSession AS POS ON R.badgeid = POS.badgeid AND R.sessionid = POS.sessionid
LEFT JOIN ParticipantHasRole AS PHR ON P.badgeid = PHR.badgeid and PHR.roleid = 10 /* moderator */
LEFT JOIN (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
) A ON (A.participantid = P.badgeid)
WHERE
        POS.sessionid = $selsessionid
        OR POS.sessionid IS NULL
ORDER BY
        attending DESC,
        moderator DESC,
        IFNULL(POS.badgeid, "~") ASC,
        `rank` ASC,
        P.sortedpubsname ASC;
EOD;
    }
    if ($SurveyUsed) {
        $queryArray['questions'] = <<<EOD
SELECT s.questionid, s.shortname, s.hover, t.shortname as typename
FROM SurveyQuestionConfig s
JOIN SurveyQuestionTypes t USING (typeid)
WHERE searchable = 1
ORDER BY s.display_order;
EOD;
        $queryArray['options'] = <<<EOD
SELECT o.questionid, o.ordinal, o.optionshort, o.optionhover, o.value
FROM SurveyQuestionOptionConfig o
JOIN SurveyQuestionConfig s USING (questionid)
WHERE s.searchable = 1
ORDER by o.questionid, o.display_order
EOD;
    }
    if (($resultXML = mysql_query_XML($queryArray)) === false) {
        if (!isset($message_error)) {
            $message_error = "";
        }
        $message_error .= "<br>Error querying database. Unable to continue.<br>";
        echo "<p class\"alert alert-error\">" . $message_error . "</p>\n";
        staff_footer();
        exit();
    }
    if ($SurveyUsed) {
        // get any questions that need programically create options
        $sql = <<<EOD
SELECT d.questionid, t.shortname as typename, min_value, max_value, ascending
FROM SurveyQuestionConfig d
JOIN SurveyQuestionTypes t USING (typeid)
WHERE t.shortname = 'monthyear';
EOD;
        $result = mysqli_query_exit_on_error($sql);
        while ($row = mysqli_fetch_assoc($result)) {
            // build xml array from begin to end
            $options = [];
            $question_id = $row["questionid"];
            if ($row["ascending"] == 1) {
                $next = $row["min_value"];
                $end = $row["max_value"];
                while ($next <= $end) {
                    $ojson = new stdClass();
                    $ojson->questionid = $question_id;
                    $ojson->value = $next;
                    $ojson->optionshort = $next;
                    $options[] = $ojson;
                    $next = $next + 1;
                }
            }
            else {
                $next = $row["max_value"];
                $end = $row["min_value"];
                while ($next >= $end) {
                    $ojson = new stdClass();
                    $ojson->questionid = $question_id;
                    $ojson->value = $next;
                    $ojson->optionshort = $next;
                    $options[] = $ojson;
                    $next = $next - 1;
                }
            }
            //var_error_log($options);
            $resultXML = ObjecttoXML('years', $options, $resultXML);
        }
    }
    if (DBVER > "8") {
        $otherParticipantsQuery = <<<EOD
WITH AnsweredSurvey(participantid, answercount) AS (
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
    P.sortedpubsname,
    CONCAT(CASE
        WHEN P.pubsname != "" THEN P.pubsname
        WHEN CD.lastname != "" THEN CONCAT(CD.lastname, ", ", CD.firstname)
        ELSE CD.firstname
    END, ' (', CD.badgename, ') - ', P.badgeid) AS name,
        IFNULL(A.answercount, 0) as answercount
    FROM
        Participants P
    JOIN CongoDump CD USING(badgeid)
    LEFT OUTER JOIN SessionParticipants S ON (P.badgeid = S.badgeid)
    LEFT OUTER JOIN AnsweredSurvey A ON (P.badgeid = A.participantid)
    WHERE P.interested = 1 AND S.badgeid IS NULL
ORDER BY
    IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname;
EOD;     
    } else {
        $otherParticipantsQuery = <<<EOD
SELECT
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.badgeid,
    P.pubsname,
    P.sortedpubsname,
    CONCAT(CASE
        WHEN P.pubsname != "" THEN P.pubsname
        WHEN CD.lastname != "" THEN CONCAT(CD.lastname, ", ", CD.firstname)
        ELSE CD.firstname
    END, ' (', CD.badgename, ') - ', P.badgeid) AS name,
        IFNULL(A.answercount, 0) as answercount
    FROM
        Participants P
    JOIN CongoDump CD USING(badgeid)
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
ORDER BY
    #IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname;
    P.sortedpubsname;
EOD;    
    }
    $otherParticipantsResult = mysqli_query_exit_on_error($otherParticipantsQuery);

    $docNode = $resultXML->getElementsByTagName("doc")->item(0);

    $queryNode = $resultXML->createElement("query");
    $queryNode = $docNode->appendChild($queryNode);
    $queryNode->setAttribute("queryName", "otherParticipants");
    $regexArr = array();
    $SurveysAnswered = 0;
    while ($row = mysqli_fetch_assoc($otherParticipantsResult)) {
        $rowNode = $resultXML->createElement("row");
        $rowNode = $queryNode->appendChild($rowNode);
        $badgeid = $row["badgeid"];
        $rowNode->setAttribute("badgeid", $badgeid);
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
        if (isset($row["sortedpubsname"]) and !empty($row["sortedpubsname"])) {
            $sortableName = $row["sortedpubsname"];
        }
        $rowNode->setAttribute("sortableName", $sortableName);
        $rowNode->setAttribute("sortableNameLc", mb_convert_case($sortableName, MB_CASE_LOWER));
        $SurveysAnswered += $row['answercount'];
    }

    $parametersNode = $resultXML->createElement("parameters");
    $parametersNode = $docNode->appendChild($parametersNode);
    if (may_I('EditSesNtsAsgnPartPg')) {
        $parametersNode->setAttribute("editSessionNotes", "true");
    }
    $paramArray = array();
    $paramArray['surveys'] = $SurveysAnswered;
    $paramArray["SurveyUsed"] = $SurveyUsed ? "1" : "0";
    //echo($resultXML->saveXML()); //for debugging only
    RenderXSLT('StaffAssignParticipants.xsl', $paramArray, $resultXML);
}
staff_footer();
?>
