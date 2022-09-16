<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php');

function update_survey() {
    global $linki, $mysqli, $message_error;
    //error_log("\n\nin update surver:\n");
    //error_log("string loaded: " . getString("survey"));
    $questions = json_decode(base64_decode(getString("survey")));
    //var_error_log($questions);
    // reset display order to match new order and find which rows to delete
    $idsFound = "";
    $display_order = 10;
    //var_error_log($questions);
    foreach ($questions as $quest) {
        $quest->display_order = $display_order;
        $display_order = $display_order + 10;
        $id = (int) $quest->questionid;
        if ($id) {
            $idsFound = $idsFound . ',' . $id;
        }
    }
    //error_log($idsFound);

    // delete the ones no longer in the JSON uploaded, check for none uploaded
    if (mb_strlen($idsFound) < 2) {
        $sql = "DELETE FROM SurveyQuestionOptionConfig WHERE questionid >= 0;";
    } else {
        $sql = "DELETE FROM SurveyQuestionOptionConfig WHERE questionid NOT IN (" . mb_substr($idsFound, 1) . ");";
    }

    //error_log("\ndelete unused options = '" . $sql . "'");
    if (!mysqli_query_exit_on_error($sql)) {
        exit(); // Should have exited already.
    }
    $optdeleted = mysqli_affected_rows($linki);

    if (mb_strlen($idsFound) < 2) {
        $sql = "DELETE FROM SurveyQuestionConfig WHERE questionid >= 0;";
    } else {
        $sql = "DELETE FROM SurveyQuestionConfig WHERE questionid NOT IN (" . mb_substr($idsFound, 1) . ");";
    }
    //error_log("\ndelete unused questions = '" . $sql . "'");

    if (!mysqli_query_exit_on_error($sql)) {
        exit(); // Should have exited already.
    }
    $deleted = mysqli_affected_rows($linki);

    // insert new rows (those with id < 0)
    $inserted = 0;
    $optinserted = 0;
    $sql = <<<EOD
        INSERT INTO SurveyQuestionConfig (shortname, description, prompt,
            hover, display_order, typeid, required, publish, privacy_user, searchable, ascending, display_only, min_value, max_value)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
EOD;
    $optinssql = <<<EOD
        INSERT INTO SurveyQuestionOptionConfig (questionid, ordinal, value, display_order,
            optionshort, optionhover, allowothertext)
        VALUES(?, ?, ?, ?, ?, ?, ?);
EOD;
    foreach ($questions as $quest) {
        $id = (int) $quest->questionid;
        if ($id < 0) {

            $paramarray = array(
                property_exists($quest, "shortname") ? $quest->shortname : "",
                property_exists($quest, "description") ? $quest->description : null,
                property_exists($quest, "prompt") ? $quest->prompt : "",
                property_exists($quest, "hover") ?  $quest->hover : null,
                property_exists($quest, "display_order") ? $quest->display_order: null,
                property_exists($quest, "typeid") ? (int) $quest->typeid: 60,
                property_exists($quest, "required") ? $quest->required : 1,
                property_exists($quest, "publish") ? $quest->publish : 0,
                property_exists($quest, "privacy_user") ? $quest->privacy_user : 0,
                property_exists($quest, "searchable") ? $quest->searchable : 0,
                property_exists($quest, "ascending") ? $quest->ascending : 1,
                property_exists($quest, "display_only") ? $quest->display_only : 0,
                property_exists($quest, "min_value") ? ($quest->min_value != "" ? $quest->min_value : null) : null,
                property_exists($quest, "max_value") ? ($quest->max_value != "" ? $quest->max_value : null) : null
            );
            //error_log("\n\nInsert of " . $quest->shortname);
            //error_log($sql);
            //var_error_log($paramarray);
            $inserted = $inserted + mysql_cmd_with_prepare($sql, "ssssiiiiiiiiii", $paramarray);
            $questionid = $mysqli->insert_id;
            $options = [];
            if (property_exists($quest, "options")) {
                $optstring = base64_decode($quest->options);
                if ($optstring != "")
                    $options  = json_decode($optstring);
            }
            //error_log("\n\nOptions:\n");
            //error_log("\nsql='" . $optinssql . "'");
            //var_error_log($options);
            $optord = 1;
            $optdisplayorder = 10;
            foreach ($options as $opt) {              
                $optparamarray = array(
                    $questionid, $optord,
                    property_exists($opt, "value") ? $opt->value : "",
                    $optdisplayorder,
                    property_exists($opt, "optionshort") ? $opt->optionshort : "",
                    property_exists($opt, "optionhover") ? $opt->optionhover : "",
                    property_exists($opt, "allowothertext") ? $opt->allowothertext : 0
                );

                $optinserted = $optinserted + mysql_cmd_with_prepare($optinssql, "iisissi", $optparamarray);
                //error_log("options inserted now " . $optinserted);
                $optord = $optord + 1;
                $optdisplayorder = $optdisplayorder + 10;
            }
        }
    }

    // update existing rows (those with id >= 0)
    $updated = 0;
    $optupdated = 0;
    $sql = <<<EOD
        UPDATE SurveyQuestionConfig SET
            shortname = ?,
            description = ?,
            prompt = ?,
            hover = ?,
            display_order = ?,
            typeid = ?,
            required = ?,
            publish = ?,
            privacy_user = ?,
            searchable = ?,
            ascending = ?,
            display_only = ?,
            min_value = ?,
            max_value = ?
        WHERE questionid = ?;
EOD;
    $optsql = <<<EOD
        UPDATE SurveyQuestionOptionConfig SET
            value = ?, display_order = ?, optionshort = ?, optionhover = ?, allowothertext = ?
        WHERE questionid = ? AND ordinal = ?;
EOD;
    foreach ($questions as $quest) {
        $id = (int) $quest->questionid;
        //error_log("\n\nupdate loop " . $id);
        if ($id >= 0) {
            //error_log("\n\nUpdate Processing question id: " . $id);

            $paramarray = array(
                property_exists($quest, "shortname") ? $quest->shortname : "",
                property_exists($quest, "description") ? $quest->description : null,
                property_exists($quest, "prompt") ? $quest->prompt : "",
                property_exists($quest, "hover") ? $quest->hover : null,
                property_exists($quest, "display_order") ? $quest->display_order: null,
                property_exists($quest, "typeid") ? (int) $quest->typeid: 60,
                property_exists($quest, "required") ? $quest->required : 1,
                property_exists($quest, "publish") ? $quest->publish : 0,
                property_exists($quest, "privacy_user") ? $quest->privacy_user : 0,
                property_exists($quest, "searchable") ? $quest->searchable : 0,
                property_exists($quest, "ascending") ? $quest->ascending : 1,
                property_exists($quest, "display_only") ? $quest->display_only : 0,
                property_exists($quest, "min_value") ? (strlen($quest->min_value) > 0 ? $quest->min_value : null) : null,
                property_exists($quest, "max_value") ? (strlen($quest->max_value) > 0 ? $quest->max_value : null) : null,
                $id
            );
            //error_log("\n\nupdate of " . $id . "\n" . $sql);
            //var_error_log($paramarray);
            $updated = $updated + mysql_cmd_with_prepare($sql, "ssssiiiiiiiiiii", $paramarray);
            $options = [];
            if (property_exists($quest, "options")) {
                $optstring = $quest->options;
                //error_log("\n\nquestion options = '" . $optstring . "'\n\n");
                if (mb_strlen($optstring) > 3) {
                    $optstring = base64_decode($optstring);
                    //error_log("\n\ndecoded optstring = '" . $optstring . "'\n\n");
                    $options  = json_decode($optstring);
                    //error_log("\n\npost json decode\n");
                    //var_error_log($options);
                }
            }
            $optdisplayorder = 10;
            $idsFound = "";

            // Delete options no longer needed
            foreach ($options as $opt) {
                $opt->display_order = $optdisplayorder;
                $optdisplayorder = $optdisplayorder + 10;

                $ord = (int) $opt->ordinal;
                if ($ord > 0) {
                    $idsFound = $idsFound . ',' . $ord;
                }
            }
            $optdelsql = "DELETE FROM SurveyQuestionOptionConfig WHERE questionid = ?";
            if (mb_strlen($idsFound) >= 2) {
                $optdelsql = $optdelsql . " and ordinal NOT IN (" . mb_substr($idsFound, 1) . ")";
            }
            $optdelsql = $optdelsql . ";";
            //error_log($optdelsql);
            $paramarray = array($id);
            //var_error_log($paramarray);
            $optdeleted = $optdeleted + mysql_cmd_with_prepare($optdelsql, "i", $paramarray);

            // get new max ordinal
            $optord = 0;
            $maxsql = "SELECT MAX(ordinal) AS max FROM SurveyQuestionOptionConfig WHERE questionid = ?;";
            $paramarray = array($id);
            $result = mysqli_query_with_prepare_and_exit_on_error($maxsql, "i", $paramarray);
            while ($row = mysqli_fetch_assoc($result)) {
                $optord = $row["max"];
            }
            if ($optord == null) {
                $optord = 0;
            }
            $optord = $optord + 1;

            // Update existing options
            foreach ($options as $opt) {
                if ($opt->ordinal >= 0) {                   
                    $paramarray = array(
                        property_exists($opt, "value") ? $opt->value : "",
                        $opt->display_order,
                        property_exists($opt, "optionshort") ? $opt->optionshort : "",
                        property_exists($opt, "optionhover") ? $opt->optionhover : "",
                        property_exists($opt, "allowothertext") ? $opt->allowothertext : 0,
                        $id, $opt->ordinal
                    );

                    //error_log("\n\n" . $optsql);
                    //var_error_log($paramarray);
                    $optupdated = $optupdated + mysql_cmd_with_prepare($optsql, "sisssii", $paramarray);
                }
            }

            // Insert new options
            foreach ($options as $opt) {
                if ($opt->ordinal < 0) {                  
                    $paramarray = array(
                        $id, $optord,
                        property_exists($opt, "value") ? $opt->value : "",
                        $opt->display_order,
                        property_exists($opt, "optionshort") ? $opt->optionshort : "",
                        property_exists($opt, "optionhover") ? $opt->optionhover : "",
                        property_exists($opt, "allowothertext") ? $opt->allowothertext : 0
                        );                  

                    //error_log("\n\n" . $optinssql);
                    //var_error_log($paramarray);
                    $optinserted = $optinserted + mysql_cmd_with_prepare($optinssql, "iisissi", $paramarray);
                    $optord = $optord + 1;
                }
            }
        }
    }
    $message = "";
    if ($deleted > 0) {
        $message = ", " . $deleted . " questions deleted";
    }
    if ($inserted > 0) {
        $message = $message . ", " . $inserted . " questions inserted";
    }
    if ($updated > 0) {
        $message = $message . ", " . $updated . " questions updated";
    }
    if ($optdeleted > 0) {
        $message = $message . ", " . $optdeleted . " options deleted";
    }
    if ($optinserted > 0) {
        $message = $message . ", " . $optinserted . " options inserted";
    }
     if ($optupdated > 0) {
        $message = $message . ", " . $optupdated . " options updated";
    }
   if (mb_strlen($message) > 2) {
        $message = "<p>Database changes: " . mb_substr($message, 2) .  "</p>";
   } else {
       $message = "";
   }

    // get updated survey now with the id's in it
    fetch_survey($message);
}

function fetch_survey($message) {
    $json_return = array();
    if ($message != "")
        $json_return["message"] = $message;

    // json of current questions and question options
	$query=<<<EOD
		SELECT JSON_OBJECT(
			'questionid', d.questionid,
			'shortname', d.shortname,
			'description', d.description,
			'prompt', prompt,
			'hover', hover,
			'display_order', d.display_order,
			'typeid', d.typeid,
			'typename', t.shortname,
			'required', required,
			'publish', publish,
			'privacy_user', privacy_user,
			'searchable', searchable,
			'ascending', ascending,
			'display_only', display_only,
			'min_value', min_value,
			'max_value', max_value,
			'options', ""
			) AS config
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
		ORDER BY d.display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$Config = "[\n\t";
    while ($row = mysqli_fetch_assoc($result)) {
        $Config .= "\t" . $row["config"] . ",\n";
    }
	$Config = mb_substr($Config, 0, -2) . "\n]";
	mysqli_free_result($result);
	$json_return["survey"] = base64_encode($Config);

    $query = <<<EOD
	SELECT questionid, display_order, JSON_OBJECT(
		'questionid', questionid,
        'ordinal', ordinal,
        'value', value,
		'optionshort', optionshort,
		'optionhover', optionhover,
		'allowothertext', allowothertext,
		'display_order', display_order
		) AS optionconfig
	FROM SurveyQuestionOptionConfig
	GROUP BY questionid, ordinal
	ORDER BY questionid, display_order;
EOD;
	$result = mysqli_query_exit_on_error($query);

	$survey_options = array();
    $cur_qid = "";
    $cur_config = "[";

    while ($row = mysqli_fetch_assoc($result)) {
        $qid = $row["questionid"];
        $config = $row["optionconfig"];

        if ($qid != $cur_qid) {
            if ($cur_qid != "") {
                $survey_options[$cur_qid] = base64_encode(mb_substr($cur_config, 0, -2) . "]");
            }
            $cur_config = "[";
            $cur_qid = $qid;
        }
        $cur_config = $cur_config . $config . ",\n";
    }
    mysqli_free_result($result);

    $survey_options[$cur_qid] = base64_encode(mb_substr($cur_config, 0, -2) . "]");
    $json_return["survey_options"] = $survey_options;

    echo json_encode($json_return) . "\n";
}

// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;

$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "" || !isLoggedIn() || !may_I("Administrator")) {
    exit();
}

switch ($ajax_request_action) {
    case "fetch_survey":
        fetch_survey("");
        break;
    case "update_survey":
        update_survey();
        break;
    default:
        $message_error = "Internal error.";
        RenderErrorAjax($message_error);
        exit();
}

?>
