<?php
// Copyright (c) 2005-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Invite Participants";
require_once('StaffCommonCode.php');
staff_header($title, 'bs4');
$message = "";
$alerttype = "success";
$submittype = "";
if(may_I("Staff")) {
    $query = [];
    $query['participants'] = <<<EOD
SELECT
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
    WHERE
        P.interested=1
    ORDER BY
        IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;

    $query['sessions'] = <<<EOD
SELECT
        T.trackname, S.sessionid, S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
    WHERE
        SS.may_be_scheduled=1
    ORDER BY
        T.trackname, S.sessionid, S.title;
EOD;

    // check for any survey stuff defined, before doing survey queries
    $sql = "SELECT COUNT(*) AS questions FROM SurveyQuestionConfig WHERE searchable = 1;";
    $result = mysqli_query_exit_on_error($sql);
    $row = mysqli_fetch_assoc($result);
    if ($row)
        $SurveyUsed = $row["questions"]  > 0;
    else
        $SurveyUsed = false;
    mysqli_free_result($result);

    if ($SurveyUsed) {
        // get searchable survey response options
        $query['questions'] = <<<EOD
SELECT s.questionid, s.shortname, s.hover, t.shortname as typename
FROM SurveyQuestionConfig s
JOIN SurveyQuestionTypes t USING (typeid)
WHERE searchable = 1
ORDER BY s.display_order;
EOD;
        $query['options'] = <<<EOD
SELECT o.questionid, o.ordinal, o.optionshort, o.optionhover, o.value
FROM SurveyQuestionOptionConfig o
JOIN SurveyQuestionConfig s USING (questionid)
WHERE s.searchable = 1
ORDER by o.questionid, o.display_order
EOD;
    }
    $resultXML = mysql_query_XML($query);

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
        mysqli_free_result($result);
    }

    $PriorArray["getSessionID"] = session_id();

    $ControlStrArray = generateControlString($PriorArray);
    $paramArray["control"] = $ControlStrArray["control"];
    $paramArray["controliv"] = $ControlStrArray["controliv"];
    $paramArray["SurveyUsed"] = $SurveyUsed ? "1" : "0";

    if ($message != "") {
        $paramArray["UpdateMessage"] = $message;
        $paramArray["MessageAlertType"] = $alerttype;
    }
    // following line for debugging only
    //echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
    RenderXSLT('InviteParticipants.xsl', $paramArray, $resultXML);
}
staff_footer();
?>
