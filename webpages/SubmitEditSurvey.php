<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
define("USEOPTATOB", FALSE);
require_once('StaffCommonCode.php');

function update_survey() {
    global $linki, $message_error;
    //error_log("\n\nin update surver:\n");
    //error_log("string loaded: " . getString("survey"));
    $questions = ((array_key_exists("survey", $_POST) && is_array($_POST["survey"])) ? $_POST["survey"] : []);
    // Survey Question Options are submitted as a JSON object with numeric IDs as properties. Convert to array so they can be referenced as PHP array indexes.
    $questionOptions = ((array_key_exists("survey_options", $_POST) && is_array($_POST["survey_options"])) ? $_POST["survey_options"] : []);
//$message = print_r($questions, TRUE);
//$message = print_r($questionOptions, TRUE);
//$message = print_r($_POST["survey_options"], TRUE);
//fetch_survey($message);
//return;

    //var_error_log($questions);
    // reset display order to match new order and find which rows to delete
    $idsFound = "";
    $display_order = 10;
    //var_error_log($questions);
    foreach ($questions as $key => $quest) {
        $questions[$key]['display_order'] = $display_order;
        $display_order = $display_order + 10;
        $id = (int) $quest['questionid'];
        if ($id) {
            $idsFound .= ',' . $id;
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
        $id = (int) $quest["questionid"];
        //error_log("\n\nChecking " . $id);
        // If ID is negative, entry is a new row, so insert.
        if ($id < 0) {

            $paramarray = array(
                array_key_exists("shortname", $quest) ? $quest["shortname"] : "",
                array_key_exists("description", $quest) ? $quest["description"] : null,
                array_key_exists("prompt", $quest) ? $quest["prompt"] : "",
                array_key_exists("hover", $quest) ? $quest["hover"] : null,
                array_key_exists("display_order", $quest) ? $quest["display_order"] : null,
                array_key_exists("typeid", $quest) ? (int) $quest["typeid"] : 60,
                array_key_exists("required", $quest) ? $quest["required"] : 1,
                array_key_exists("publish", $quest) ? $quest["publish"] : 0,
                array_key_exists("privacy_user", $quest) ? $quest["privacy_user"] : 0,
                array_key_exists("searchable", $quest) ? $quest["searchable"] : 0,
                array_key_exists("ascending", $quest) ? $quest["ascending"] : 1,
                array_key_exists("display_only", $quest) ? $quest["display_only"] : 0,
                array_key_exists("min_value", $quest) ? ($quest["min_value"] != "" ? $quest["min_value"] : null) : null,
                array_key_exists("max_value", $quest) ? ($quest["max_value"] != "" ? $quest["max_value"] : null) : null
            );
            //error_log("\n\nInsert of " . $quest["shortname"]);
            //error_log($sql);
            //var_error_log($paramarray);
            $inserted = $inserted + mysql_cmd_with_prepare($sql, "ssssiiiiiiiiii", $paramarray);
            $questionid = mysqli_insert_id($linki);
            if (array_key_exists($id, $questionOptions)) {
                $options = $questionOptions[$id];
            }
            else {
                $options = [];
            }
            //error_log("\n\nOptions:\n");
            //error_log("\nsql='" . $optinssql . "'");
            //var_error_log($options);
            $optord = 1;
            $optdisplayorder = 10;
            if (is_array($options)) {
                foreach ($options as $opt) {
                    if (USEOPTATOB) {
                        $optparamarray = array(
                            $questionid, $optord,
                            array_key_exists("value", $opt) ? base64_decode($opt['value']) : "",
                            $optdisplayorder,
                            array_key_exists("optionshort", $opt) ? base64_decode($opt['optionshort']) : "",
                            array_key_exists("optionhover", $opt) ? base64_decode($opt['optionhover']) : "",
                            array_key_exists("allowothertext", $opt) ? $opt['allowothertext'] : 0
                        );
                    } else {
                        $optparamarray = array(
                            $questionid, $optord,
                            array_key_exists("value", $opt) ? $opt['value'] : "",
                            $optdisplayorder,
                            array_key_exists("optionshort", $opt) ? $opt['optionshort'] : "",
                            array_key_exists("optionhover", $opt) ? $opt['optionhover'] : "",
                            array_key_exists("allowothertext", $opt) ? $opt['allowothertext'] : 0
                        );
                    }
                    $optinserted = $optinserted + mysql_cmd_with_prepare($optinssql, "iisissi", $optparamarray);
                    //error_log("options inserted now " . $optinserted);
                    $optord = $optord + 1;
                    $optdisplayorder = $optdisplayorder + 10;
                }
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
        $id = (int) $quest["questionid"];
        //error_log("\n\nupdate loop " . $id);
        if ($id >= 0) {
            //error_log("\n\nUpdate Processing question id: " . $id);

            $paramarray = array(
                array_key_exists("shortname", $quest) ? $quest["shortname"] : "",
                array_key_exists("description", $quest) ? $quest["description"] : null,
                array_key_exists("prompt", $quest) ? $quest["prompt"] : "",
                array_key_exists("hover", $quest) ? $quest["hover"] : null,
                array_key_exists("display_order", $quest) ? $quest["display_order"] : null,
                array_key_exists("typeid", $quest) ? (int) $quest["typeid"] : 60,
                array_key_exists("required", $quest) ? $quest["required"] : 1,
                array_key_exists("publish", $quest) ? $quest["publish"] : 0,
                array_key_exists("privacy_user", $quest) ? $quest["privacy_user"] : 0,
                array_key_exists("searchable", $quest) ? $quest["searchable"] : 0,
                array_key_exists("ascending", $quest) ? $quest["ascending"] : 1,
                array_key_exists("display_only", $quest) ? $quest["display_only"] : 0,
                array_key_exists("min_value", $quest) ? (strlen($quest["min_value"]) > 0 ? $quest["min_value"] : null) : null,
                array_key_exists("max_value", $quest) ? (strlen($quest["max_value"]) > 0 ? $quest["max_value"] : null) : null,
                $id
            );
            //error_log("\n\nupdate of " . $id . "\n" . $sql);
            //var_error_log($paramarray);
            $updated = $updated + mysql_cmd_with_prepare($sql, "ssssiiiiiiiiiii", $paramarray);
            if (array_key_exists($id, $questionOptions)) {
                $options = $questionOptions[$id];
            }
            else {
                $options = [];
            }
            $optdisplayorder = 10;
            $idsFound = "";

            // Delete options no longer needed
            foreach ($options as $opt) {
                $opt['display_order'] = $optdisplayorder;
                $optdisplayorder = $optdisplayorder + 10;

                $ord = (int) $opt['ordinal'];
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

            if (is_array($options)) {
                // Update existing options
                foreach ($options as $opt) {
                    if ($opt['ordinal'] >= 0) {
                        if (USEOPTATOB) {
                            $paramarray = array(
                                array_key_exists("value", $opt) ? base64_decode($opt['value']) : "",
                                $optdisplayorder,
                                array_key_exists("optionshort", $opt) ? base64_decode($opt['optionshort']) : "",
                                array_key_exists("optionhover", $opt) ? base64_decode($opt['optionhover']) : "",
                                array_key_exists("allowothertext", $opt) ? $opt['allowothertext'] : 0,
                                $id, $opt['ordinal']
                            );
                        } else {
                            $paramarray = array(
                                array_key_exists("value", $opt) ? $opt['value'] : "",
                                $optdisplayorder,
                                array_key_exists("optionshort", $opt) ? $opt['optionshort'] : "",
                                array_key_exists("optionhover", $opt) ? $opt['optionhover'] : "",
                                array_key_exists("allowothertext", $opt) ? $opt['allowothertext'] : 0,
                                $id, $opt['ordinal']
                            );
                        }
                        //error_log("\n\n" . $optsql);
                        //var_error_log($paramarray);
                        $optupdated = $optupdated + mysql_cmd_with_prepare($optsql, "sisssii", $paramarray);
                    }
                }
            }

            // Insert new options
            foreach ($options as $opt) {
                if ($opt['ordinal'] < 0) {
                    if (USEOPTATOB) {
                        $paramarray = array(
                            $id, $optord,
                            array_key_exists("value", $opt) ? base64_decode($opt['value']) : "",
                            $optdisplayorder,
                            array_key_exists("optionshort", $opt) ? base64_decode($opt['optionshort']) : "",
                            array_key_exists("optionhover", $opt) ? base64_decode($opt['optionhover']) : "",
                            array_key_exists("allowothertext", $opt) ? $opt['allowothertext'] : 0
                            );
                    } else {
                        $paramarray = array(
                            $id, $optord,
                            array_key_exists("value", $opt) ? $opt['value'] : "",
                            $optdisplayorder,
                            array_key_exists("optionshort", $opt) ? $opt['optionshort'] : "",
                            array_key_exists("optionhover", $opt) ? $opt['optionhover'] : "",
                            array_key_exists("allowothertext", $opt) ? $opt['allowothertext'] : 0
                            );
                    }
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
        SELECT d.questionid, d.shortname, d.description, prompt, hover, d.display_order, d.typeid, t.shortname AS typename, required, publish, privacy_user, searchable, ascending, display_only, min_value, max_value
        FROM SurveyQuestionConfig d
        JOIN SurveyQuestionTypes t USING (typeid)
        ORDER BY d.display_order ASC;
EOD;

    $result = mysqli_query_exit_on_error($query);
    $Config = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $Config[] = $row;
    }
    mysqli_free_result($result);
    $json_return["survey"] = $Config;

    $query = <<<EOD
        SELECT questionid, ordinal, value, optionshort, optionhover, allowothertext, display_order
        FROM SurveyQuestionOptionConfig
        GROUP BY questionid, ordinal
        ORDER BY questionid, display_order;
EOD;
    $result = mysqli_query_exit_on_error($query);

    $survey_options = [];
    $cur_qid = "";
    $cur_config = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $qid = $row["questionid"];

        if ($qid != $cur_qid) {
            if ($cur_qid != "") {
                $survey_options[$cur_qid] = $cur_config;
            }
            $cur_config = [];
            $cur_qid = $qid;
        }
        $cur_config[] = $row;
    }
    mysqli_free_result($result);

    $survey_options[$cur_qid] = $cur_config;
    $json_return["survey_options"] = (object)$survey_options;

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
