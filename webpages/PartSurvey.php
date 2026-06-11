<?php
// Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-29

global $header_section, $message_error, $title, $linki;
$title = 'Participant Survey';
// This can be a participant or a staff page
require_once('PartCommonCode.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
$message = '';
$rows = 0;
$rows_modified = 0;

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
        $sql = <<<EOD
INSERT INTO ParticipantSurveyAnswers(participantid, questionid, privacy_setting, value, othertext, updatedby)
VALUES (?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
    privacy_setting = ?,
    value = ?,
    othertext = ?,
    updatedby = ?;
EOD;
        $delsql = <<<EOD
DELETE FROM ParticipantSurveyAnswers WHERE participantid = ? and questionid = ?;
EOD;
        $parms = [];
        $types = '';
        $inserted = 0;
        $updated = 0;
        $deleted = 0;
        $errors = 0;
        $types = 'siisssisss';
        foreach ($shortname_types as $obj) {
            if ($obj->typename != 'heading') {
                if (!isset($_POST[$obj->id])) {
                    $deleted += mysql_cmd_with_prepare($delsql, 'si', array($edit_badgeid, $obj->questionid));
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
                    $privacyuser = $_POST[$privacyname];
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
                        $ans = implode($separator, $_POST[$obj->id]);
                        $parms = array($edit_badgeid, $obj->questionid, $privacyuser, $ans, $othertext, $badgeid, $privacyuser, $ans, $othertext, $badgeid);
                        break;
                    default:
                        //echo "processing default for " . $obj->typename . "<br/>";
                        //echo "shortname = '" . $obj->shortname . "', questionid = " . $obj->questionid . ", id = '" . $obj->id . "'<br/>";
                        $parms = array($edit_badgeid, $obj->questionid, $privacyuser, $_POST[$obj->id], $othertext, $badgeid, $privacyuser, $_POST[$obj->id], $othertext, $badgeid);
                }
                //var_dump($parms);
                $rows_modified = mysql_cmd_with_prepare($sql, $types, $parms);
                //echo "status = $rows_modified<br/><br/>";
                if ($rows_modified == 1) {
                    $inserted = $inserted + 1;
                } else if ($rows_modified == 2) {
                    $updated = $updated + 1;
                } else if ($rows_modified < 0) {
                    echo('Error description: ' . mysqli_error($linki) . '<br/><br/>');
                    $errors = $errors + 1;
                    break;
                }
            }
        }
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

    // json of current questions and question options
    $paramArray = array();
    $query = [];
    $query['questions']=<<<EOD
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
        IFNULL(a.value, "") AS answer,
        IFNULL(a.othertext, "") AS othertext,
        IFNULL(a.privacy_setting, publish) AS privacy_setting,
        CASE WHEN SUM(o.allowothertext) > 0 THEN 1 ELSE 0 END AS allowothertext
    FROM
                  SurveyQuestionConfig d
             JOIN SurveyQuestionTypes t USING (typeid)
        LEFT JOIN ParticipantSurveyAnswers a ON (a.questionid = d.questionid and a.participantid = "$edit_badgeid")
        LEFT JOIN SurveyQuestionOptionConfig o ON (d.questionid = o.questionid)
    GROUP BY
        d.questionid
    ORDER BY
        d.display_order ASC;
EOD;

    $query["options"] = <<<EOD
SELECT
        questionid, display_order, ordinal, value, optionshort, optionhover, allowothertext, display_order
    FROM
        SurveyQuestionOptionConfig
    ORDER BY
        questionid, display_order;
EOD;
    $resultXML = mysql_query_XML($query);

    // get any questions that need programically create options as well as build array for the 'save'
    $sql = <<<EOD
SELECT
        d.questionid, d.shortname, t.shortname as typename, min_value, max_value, ascending
    FROM
             SurveyQuestionConfig d
        JOIN SurveyQuestionTypes t USING (typeid)
    WHERE
            t.shortname != "heading"
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
    $sql = <<<EOD
SELECT
        count(*) AS answers
    FROM
        ParticipantSurveyAnswers
    WHERE
        participantid = "$edit_badgeid";
EOD;
    $result = mysqli_query_exit_on_error($sql);
    $rows = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $rows = $row['answers'];
    }

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
