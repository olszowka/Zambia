<?php
// Copyright (c) 2022-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
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
        $query = [];
        $query["questions"]=<<<EOD
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
        END AS `rows`,
        IFNULL(PSA.value, '') AS answer, IFNULL(PSA.othertext, '') AS othertext,
        IFNULL(PSA.privacy_setting, publish) AS privacy_setting,
        IF (IFNULL(SQOC.allowothertext, 0) > 0, 1, 0) AS allowothertext
    FROM
                  SurveyQuestionConfig SQC
             JOIN SurveyQuestionTypes SQT USING (typeid)
             JOIN ParticipantSurveyAnswers PSA ON (PSA.questionid = SQC.questionid AND PSA.participantid = '$badgeid')
        LEFT JOIN SurveyQuestionOptionConfig SQOC ON (SQC.questionid = SQOC.questionid AND PSA.value = SQOC.value)
    ORDER BY
        SQC.display_order;
EOD;
        $resultXML = mysql_query_XML($query);
        $query = <<<EOD
SELECT
        CD.firstname, CD.lastname, CD.badgename, P.pubsname, IFNULL(A.answercount, 0) AS answercount
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN (
            SELECT
                    participantid, COUNT(*) AS answercount
                FROM
                     ParticipantSurveyAnswers
                GROUP BY
                    participantid 
                  ) A ON (A.participantid = P.badgeid)
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
