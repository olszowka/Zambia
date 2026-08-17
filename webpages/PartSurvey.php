<?php
// Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-29

global $header_section, $message_error, $title;
$title = 'Participant Survey';
// This can be a participant or a staff page
require_once('PartCommonCode.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
$message = '';
$rows = 0;

// Now that title is set, get common text
if (!populateCustomTextArray()) {
    $message_error = 'Failed to retrieve custom text. ' . $message_error;
    RenderError($message_error);
    exit();
}

$edit_badgeid = getInt('edit_badgeid');
if ($edit_badgeid === false) {
    $edit_badgeid = $badgeid;
    participant_header($title, false, 'Normal', 'bs4');
} else {
    $header_section = HEADER_STAFF;
    if (!may_I('edit_participant_responses')) {
        $message_error = 'You do not have permission to access this page.';
        StaffRenderErrorPage($title, $message_error, 'bs4');
        exit();
    }
    staff_header($title, 'bs4');
}
if (isLoggedIn()) {
    if (isset($_POST['PostCheck'])) {
        $priorValues = interpretControlString($_POST['control'], $_POST['controliv']);
        if ($priorValues['getSessionID'] !=  session_id()) {
            $message = 'Session expired, survey not updated';
        } else {
            $shortname_types = json_decode($priorValues['shortname_types']);
        }

        // ParticipantSurveyResponses (one JSON blob per participant, keyed by shortname) is the source of
        // truth. Load the participant's current answers, apply this submission's changes in PHP, write the
        // merged result back in one shot, then mirror it into the deprecated ParticipantSurveyAnswers table.
        $result = mysqli_query_with_prepare_and_exit_on_error(
            "SELECT answers FROM ParticipantSurveyResponses WHERE badgeid = ?;", 's', array($edit_badgeid)
        );
        $existingRow = mysqli_fetch_assoc($result);
        $answers = $existingRow ? json_decode($existingRow['answers'], true) : array();

        $inserted = 0;
        $updated = 0;
        $deleted = 0;
        foreach ($shortname_types as $obj) {
            if ($obj->typename != 'heading') {
                if (!isset($_POST[$obj->id])) {
                    if (isset($answers[$obj->shortname])) {
                        unset($answers[$obj->shortname]);
                        $deleted++;
                    }
                    continue;
                }
                $separator = ',';
                $othertextname = $obj->id . '-othertext';
                if (isset($_POST[$othertextname])) {
                    $othertext = $_POST[$othertextname];
                } else {
                    $othertext = null;
                }
                if ($othertext == '') {
                    $othertext = null;
                }

                $privacyname = $obj->id . '-privacyuser';
                if (isset($_POST[$privacyname])) {
                    $privacyuser = (int)$_POST[$privacyname];
                } else {
                    $privacyuser = 0;
                }

                switch ($obj->typename) {
                    case 'monthyear':
                        $separator = ' ';
                    case 'multi-select list':
                    case 'multi-checkbox list':
                    case 'multi-display':
                        // error_log("processing " . $obj->typename );
                        //  error_log("shortname = '" . $obj->shortname . "', questionid = " . $obj->questionid . ", id = '" . $obj->id);
                        // var_dump($_POST[$obj->id]);
                        $value = implode($separator, $_POST[$obj->id]);
                        break;
                    default:
                        //echo "processing default for " . $obj->typename . "<br/>";
                        //echo "shortname = '" . $obj->shortname . "', questionid = " . $obj->questionid . ", id = '" . $obj->id . "'<br/>";
                        $value = $_POST[$obj->id];
                }

                $prior = $answers[$obj->shortname] ?? null;
                if ($prior === null) {
                    $inserted++;
                } else if ($prior['value'] !== $value || $prior['othertext'] !== $othertext || $prior['privacy_setting'] !== $privacyuser) {
                    $updated++;
                }
                $answers[$obj->shortname] = array('value' => $value, 'othertext' => $othertext, 'privacy_setting' => $privacyuser);
            }
        }

        if (count($answers) === 0) {
            mysql_cmd_with_prepare("DELETE FROM ParticipantSurveyResponses WHERE badgeid = ?;", 's', array($edit_badgeid));
        } else {
            $json = json_encode($answers);
            $sql = <<<EOD
INSERT INTO ParticipantSurveyResponses(badgeid, answers, updatedby)
VALUES (?, ?, ?)
ON DUPLICATE KEY UPDATE
    answers = ?,
    updatedby = ?;
EOD;
            mysql_cmd_with_prepare($sql, 'sssss', array($edit_badgeid, $json, $badgeid, $json, $badgeid));
        }
        sync_participant_survey_answers_from_json($edit_badgeid, $badgeid);

        $message = '';
        if ($inserted > 0) {
            $message = $message . $inserted . ' answers inserted, ';
        }
        if ($updated > 0) {
            $message = $message . $updated . ' answers updated, ';
        }
        if ($deleted > 0) {
            $message = $message . $deleted . ' answers deleted, ';
        }
        if ($message == "") {
            $message = 'No changes made to survey';
        } else {
            $message = 'Survey updated: ' . preg_replace('/, $/', '', $message);
        }
    }

    // Start of display portion

    // ParticipantSurveyResponses is the source of truth for this participant's answers; fetch it once and
    // merge it (in PHP) into the per-question config below, instead of joining ParticipantSurveyAnswers.
    $result = mysqli_query_with_prepare_and_exit_on_error(
        "SELECT answers FROM ParticipantSurveyResponses WHERE badgeid = ?;", 's', array($edit_badgeid)
    );
    $existingRow = mysqli_fetch_assoc($result);
    $currentAnswers = $existingRow ? json_decode($existingRow['answers'], true) : array();

    // json of current questions and question options
    $paramArray = array();
    $query = [];
    $query["options"] = <<<EOD
SELECT
        questionid, display_order, ordinal, value, optionshort, optionhover, allowothertext, display_order
    FROM
        SurveyQuestionOptionConfig
    ORDER BY
        questionid, display_order;
EOD;
    $resultXML = mysql_query_XML($query);

    $questionsSql = <<<EOD
SELECT
        d.questionid, d.shortname, d.description, prompt, hover, d.display_order, d.typeid, t.shortname as typename,
        required, publish, privacy_user, searchable, ascending, display_only, min_value, max_value,
        CASE
            WHEN t.shortname = "openend" THEN
                CASE
                    WHEN max_value > 100 THEN 100
                    WHEN max_value < 50 THEN 50
                    ELSE max_value
                END
            WHEN t.shortname = "text" OR t.shortname = "html-text" THEN
                CASE
                        WHEN max_value > 400 THEN 100
                        WHEN max_value < 200 THEN 50
                        ELSE max_value / 4
                END
            ELSE ""
        END AS size,
        CASE
            WHEN t.shortname = "text" OR t.shortname = "html-text" THEN
                CASE WHEN max_value > 500 THEN 8 ELSE 4 END
            ELSE ""
        END as `rows`,
        CASE WHEN SUM(o.allowothertext) > 0 THEN 1 ELSE 0 END AS allowothertext
    FROM
                  SurveyQuestionConfig d
             JOIN SurveyQuestionTypes t USING (typeid)
        LEFT JOIN SurveyQuestionOptionConfig o ON (d.questionid = o.questionid)
    GROUP BY
        d.questionid, d.display_order
    ORDER BY
        d.display_order ASC;
EOD;
    $result = mysqli_query_exit_on_error($questionsSql);
    $questionRows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $answer = $currentAnswers[$row['shortname']] ?? null;
        $row['answer'] = $answer['value'] ?? '';
        $row['othertext'] = $answer['othertext'] ?? '';
        $row['privacy_setting'] = $answer['privacy_setting'] ?? $row['publish'];
        // mysql_query_XML() omits attributes for fields that are NULL or '' -- replicate that here so the
        // XML shape (and thus RenderSurvey.xsl's attribute-presence checks) is unaffected by this rewrite.
        $questionRows[] = array_filter($row, fn($value) => $value !== null && $value !== '');
    }
    $resultXML = ObjecttoXML('questions', $questionRows, $resultXML);

    // get any questions that need programically create options as well as build array for the 'save'
    $sql = <<<EOD
SELECT
        d.questionid, d.shortname, t.shortname as typename, min_value, max_value, ascending
    FROM
             SurveyQuestionConfig d
        JOIN SurveyQuestionTypes t USING (typeid)
    WHERE
            t.shortname != 'heading'
        AND d.display_only = 0;
EOD;
    $result = mysqli_query_exit_on_error($sql);
    $shortname_types = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $obj = new stdClass();
        $obj->questionid = $row['questionid'];
        $obj->shortname = $row['shortname'];
        $obj->id = str_replace(' ', '_', $row['shortname']);
        $obj->typename = $row['typename'];
        $shortname_types[] = $obj;
        $numberquery = 'years';
        switch ($row["typename"]) {
            case 'numberselect':
                $numberquery = 'options';   // fall into monthyear
            case 'monthyear':
                // build xml array from begin to end
                $options = [];
                $question_id = $row['questionid'];
                if ($row['ascending'] == 1) {
                    $next = $row['min_value'];
                    $end = $row['max_value'];
                    while ($next <= $end) {
                        $ojson = new stdClass();
                        $ojson->questionid = $question_id;
                        $ojson->value = $next;
                        $ojson->optionshort = $next;
                        $options[] = $ojson;
                        $next = $next + 1;
                    }
                } else {
                    $next = $row['max_value'];
                    $end = $row['min_value'];
                    while ($next >= $end) {
                        $ojson = new stdClass();
                        $ojson->questionid = $question_id;
                        $ojson->value = $next;
                        $ojson->optionshort = $next;
                        $options[] = $ojson;
                        $next = $next - 1;
                    }
                }
                $resultXML = ObjecttoXML($numberquery, $options, $resultXML);
                break;
        }
    }
    $rows = count($currentAnswers);

    $paramArray['buttons'] = $rows == 0 ?  'save' : 'update';
    $PriorArray['getSessionID'] = session_id();
    $PriorArray['shortname_types'] = json_encode($shortname_types);

    $ControlStrArray = generateControlString($PriorArray);
    $paramArray['control'] = $ControlStrArray['control'];
    $paramArray['controliv'] = $ControlStrArray['controliv'];

    if ($message != '') {
        $paramArray['UpdateMessage'] = $message;
    }
    if ($edit_badgeid != $badgeid) {
        $query = <<<EOD
SELECT
        firstname, lastname
     FROM
         CongoDump
     WHERE
         badgeid = ?;
EOD;
        $query_param_array = array($edit_badgeid);
        $result = mysqli_query_with_prepare_and_exit_on_error($query, 's', $query_param_array);
        $row = mysqli_fetch_assoc($result);
        $paramArray['EditParticipantName'] = $row['firstname'] . ' ' . $row['lastname'];
        $paramArray['EditBadgeId'] = $edit_badgeid;
    }
    RenderXSLT('RenderSurvey.xsl', $paramArray, $resultXML);
    echo "<br/>\n";
    $surveyCustomText = fetchCustomText('survey_displayonly');
    if (strlen($surveyCustomText) > 0) {
        echo $surveyCustomText;
    } else { ?>
<p>Note: Some questions may no longer allow you to enter/change their answers. The time has passed for when you can change them and they have been changed from answerable to display only.</p>
<p>If you need to have a display only answer changed, please reach out to programming at the email address below.</p>
<?php }

}
if ($header_section == HEADER_PARTICIPANT) {
    participant_footer();
} else {
    staff_footer();
}
?>
