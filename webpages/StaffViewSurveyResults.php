<?php
// Copyright (c) 2022-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-29 or 2021-01-20 ?

global $message_error, $title, $linki, $session;
$title = "View Survey";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;

staff_header($title, 'bs4');
if (isLoggedIn() && may_I("Staff")) {
    // Start of display portion
?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.alert').alert();
    });
</script>
<?php
    // json of current questions and question options
    $badgeid = getString('badgeid');
    $resultXML = null;
    $paramArray = array();
    if ($badgeid) {
        // ParticipantSurveyResponses is the source of truth; fetch this participant's JSON once and merge
        // it (in PHP) into the question config below, instead of joining ParticipantSurveyAnswers.
        $result = mysqli_query_with_prepare_and_exit_on_error(
            "SELECT answers FROM ParticipantSurveyResponses WHERE badgeid = ?;", 's', array($badgeid)
        );
        $existingRow = mysqli_fetch_assoc($result);
        $currentAnswers = $existingRow ? json_decode($existingRow['answers'], true) : array();

        $optionAllowOtherText = array();
        $result = mysqli_query_exit_on_error("SELECT questionid, value, allowothertext FROM SurveyQuestionOptionConfig;");
        while ($row = mysqli_fetch_assoc($result)) {
            $optionAllowOtherText[$row['questionid'] . "\0" . $row['value']] = $row['allowothertext'];
        }

        $questionsSql = <<<EOD
SELECT
        SQC.questionid, SQC.shortname, SQC.description, SQC.prompt, SQC.hover, SQC.display_order, SQC.typeid, SQT.shortname as typename,
        SQC.required, SQC.publish, SQC.privacy_user, SQC.searchable, SQC.ascending, 1 AS display_only, SQC.min_value, SQC.max_value,
        CASE
            WHEN SQT.shortname = 'openend' THEN
                CASE
                    WHEN SQC.max_value > 100 THEN 100
                    WHEN SQC.max_value < 50 THEN 50
                    ELSE SQC.max_value
                END
            WHEN SQT.shortname = 'text' OR SQT.shortname = 'html-text' THEN
                CASE
                        WHEN SQC.max_value > 400 THEN 100
                        WHEN SQC.max_value < 200 THEN 50
                        ELSE SQC.max_value / 4
                END
            ELSE ''
        END AS size,
        CASE
            WHEN SQT.shortname = 'text' OR SQT.shortname = 'html-text' THEN
                CASE WHEN max_value > 500 THEN 8 ELSE 4 END
            ELSE ''
        END AS `rows`
    FROM
                  SurveyQuestionConfig SQC
             JOIN SurveyQuestionTypes SQT USING (typeid)
    ORDER BY
        SQC.display_order;
EOD;
        $result = mysqli_query_exit_on_error($questionsSql);
        $questionRows = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!isset($currentAnswers[$row['shortname']])) {
                continue; // no ParticipantSurveyResponses entry for this question (mirrors the old INNER JOIN)
            }
            $answer = $currentAnswers[$row['shortname']];
            $row['answer'] = $answer['value'] ?? '';
            $row['othertext'] = $answer['othertext'] ?? '';
            $row['privacy_setting'] = $answer['privacy_setting'] ?? $row['publish'];
            $optionKey = $row['questionid'] . "\0" . ($answer['value'] ?? '');
            $row['allowothertext'] = (isset($optionAllowOtherText[$optionKey]) && $optionAllowOtherText[$optionKey] > 0) ? 1 : 0;
            $questionRows[] = array_filter($row, fn($value) => $value !== null && $value !== '');
        }
        $resultXML = ObjecttoXML('questions', $questionRows);
        $query = <<<EOD
SELECT
        CD.firstname, CD.lastname, CD.badgename, P.pubsname, IFNULL(ANS.answercount, 0) AS answercount
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN (
            SELECT
                    badgeid, JSON_LENGTH(answers) AS answercount
                FROM
                     ParticipantSurveyResponses
                  ) ANS ON (ANS.badgeid = P.badgeid)
    WHERE P.badgeid = '$badgeid';
EOD;
        $result = mysqli_query_exit_on_error($query);
        while ($row = mysqli_fetch_assoc($result)) {
            $pubsname = $row["pubsname"];
            if ($pubsname == '' || $pubsname === null) {
                $pubsname = $row['firstname'];
                if ($row['lastname'] != '' && $row['lastname'] !== null)
                    $pubsname .= " " . $row['lastname'];
            }
            if ($row['badgename'] != '' && $row['badgename'] !== null)
                $pubsname .= ' (' . $row['badgename'] . ')';

            $paramArray['pubsname'] = $pubsname;
            $paramArray['answercount'] = $row['answercount'];
        }
    } else {
        $message = "No participant selected";
    }
    $paramArray["buttons"] = "close";

    if ($message != "") {
        $paramArray["UpdateMessage"] = $message;
    }
    // following line for debugging only
    //echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
    RenderXSLT('RenderSurvey.xsl', $paramArray, $resultXML);
}
staff_footer();
?>
