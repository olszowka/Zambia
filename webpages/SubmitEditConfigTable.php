<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('StaffCommonCode.php');

function update_table($tablename) {
    global $linki, $message_error;
    //error_log("\n\nin update table:\n");
    //error_log("string loaded: " . getString("tabledata"));
    $rows = json_decode(base64_decode(getString("tabledata")));
    var_error_log($rows);
    // reset display order to match new order and find which rows to delete
    $idsFound = "";
    $display_order = 10;
    //var_error_log($questions);
    foreach ($rows as $row) {
        $row->display_order = $display_order;
        $display_order = $display_order + 10;
        $id = (int) $row->questionid;
        if ($id) {
            $idsFound = $idsFound . ',' . $id;
        }
    }
    error_log($idsFound);

//    // delete the ones no longer in the JSON uploaded, check for none uploaded
//    if (mb_strlen($idsFound) < 2) {
//        $sql = "DELETE FROM SurveyQuestionOptionConfig WHERE questionid >= 0;";
//    } else {
//        $sql = "DELETE FROM SurveyQuestionOptionConfig WHERE questionid NOT IN (" . mb_substr($idsFound, 1) . ");";
//    }

//    //error_log("\ndelete unused options = '" . $sql . "'");
//    if (!mysqli_query_exit_on_error($sql)) {
//        exit(); // Should have exited already.
//    }
//    $optdeleted = mysqli_affected_rows($linki);

//    if (mb_strlen($idsFound) < 2) {
//        $sql = "DELETE FROM SurveyQuestionConfig WHERE questionid >= 0;";
//    } else {
//        $sql = "DELETE FROM SurveyQuestionConfig WHERE questionid NOT IN (" . mb_substr($idsFound, 1) . ");";
//    }
//    //error_log("\ndelete unused questions = '" . $sql . "'");

//    if (!mysqli_query_exit_on_error($sql)) {
//        exit(); // Should have exited already.
//    }
//    $deleted = mysqli_affected_rows($linki);

//    // insert new rows (those with id < 0)
//    $inserted = 0;
//    $optinserted = 0;
//    $sql = <<<EOD
//        INSERT INTO SurveyQuestionConfig (shortname, description, prompt,
//            hover, display_order, typeid, required, publish, privacy_user, searchable, ascending, display_only, min_value, max_value)
//        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
//EOD;
//    $optinssql = <<<EOD
//        INSERT INTO SurveyQuestionOptionConfig (questionid, ordinal, value, display_order,
//            optionshort, optionhover, allowothertext)
//        VALUES(?, ?, ?, ?, ?, ?, ?);
//EOD;
//    foreach ($questions as $quest) {
//        $id = (int) $quest->questionid;
//        if ($id < 0) {

//            $paramarray = array(
//                property_exists($quest, "shortname") ? $quest->shortname : "",
//                property_exists($quest, "description") ? base64_decode($quest->description) : null,
//                property_exists($quest, "prompt") ? base64_decode($quest->prompt) : "",
//                property_exists($quest, "hover") ? base64_decode($quest->hover) : null,
//                property_exists($quest, "display_order") ? $quest->display_order: null,
//                property_exists($quest, "typeid") ? (int) $quest->typeid: 60,
//                property_exists($quest, "required") ? $quest->required : 1,
//                property_exists($quest, "publish") ? $quest->publish : 0,
//                property_exists($quest, "privacy_user") ? $quest->privacy_user : 0,
//                property_exists($quest, "searchable") ? $quest->searchable : 0,
//                property_exists($quest, "ascending") ? $quest->ascending : 1,
//                property_exists($quest, "display_only") ? $quest->display_only : 0,
//                property_exists($quest, "min_value") ? ($quest->min_value != "" ? $quest->min_value : null) : null,
//                property_exists($quest, "max_value") ? ($quest->max_value != "" ? $quest->max_value : null) : null
//            );
//            //error_log("\n\nInsert of " . $quest->shortname);
//            //error_log($sql);
//            //var_error_log($paramarray);
//            $inserted = $inserted + mysql_cmd_with_prepare($sql, "ssssiiiiiiiiii", $paramarray);
//            $questionid = mysqli_insert_id($linki);
//            $options = [];
//            $useoptatob = true;
//            if (property_exists($quest, "options")) {
//                $optstring = base64_decode($quest->options);
//                if (mb_substr($optstring, 0, 7) == "nobtoa:") {
//                    $useoptatob = false;
//                    $optstring = mb_substr($optstring, 7);
//                }
//                $options  = json_decode($optstring);
//            }
//            //error_log("\n\nOptions:\n");
//            //error_log("\nsql='" . $optinssql . "'");
//            //var_error_log($options);
//            $optord = 1;
//            $optdisplayorder = 10;
//            foreach ($options as $opt) {
//                if ($useoptatob) {
//                    $optparamarray = array(
//                        $questionid, $optord,
//                        property_exists($opt, "value") ? base64_decode($opt->value) : "",
//                        $optdisplayorder,
//                        property_exists($opt, "optionshort") ? base64_decode($opt->optionshort) : "",
//                        property_exists($opt, "optionhover") ? base64_decode($opt->optionhover) : "",
//                        property_exists($opt, "allowothertext") ? $opt->allowothertext : 0
//                    );
//                } else {
//                    $optparamarray = array(
//                        $questionid, $optord,
//                        property_exists($opt, "value") ? $opt->value : "",
//                        $optdisplayorder,
//                        property_exists($opt, "optionshort") ? $opt->optionshort : "",
//                        property_exists($opt, "optionhover") ? $opt->optionhover : "",
//                        property_exists($opt, "allowothertext") ? $opt->allowothertext : 0
//                    );
//                }
//                $optinserted = $optinserted + mysql_cmd_with_prepare($optinssql, "iisissi", $optparamarray);
//                //error_log("options inserted now " . $optinserted);
//                $optord = $optord + 1;
//                $optdisplayorder = $optdisplayorder + 10;
//            }
//        }
//    }

//    // update existing rows (those with id >= 0)
//    $updated = 0;
//    $optupdated = 0;
//    $sql = <<<EOD
//        UPDATE SurveyQuestionConfig SET
//            shortname = ?,
//            description = ?,
//            prompt = ?,
//            hover = ?,
//            display_order = ?,
//            typeid = ?,
//            required = ?,
//            publish = ?,
//            privacy_user = ?,
//            searchable = ?,
//            ascending = ?,
//            display_only = ?,
//            min_value = ?,
//            max_value = ?
//        WHERE questionid = ?;
//EOD;
//    $optsql = <<<EOD
//        UPDATE SurveyQuestionOptionConfig SET
//            value = ?, display_order = ?, optionshort = ?, optionhover = ?, allowothertext = ?
//        WHERE questionid = ? AND ordinal = ?;
//EOD;
//    foreach ($questions as $quest) {
//        $id = (int) $quest->questionid;
//        //error_log("\n\nupdate loop " . $id);
//        if ($id >= 0) {
//            //error_log("\n\nUpdate Processing question id: " . $id);

//            $paramarray = array(
//                property_exists($quest, "shortname") ? $quest->shortname : "",
//                property_exists($quest, "description") ? base64_decode($quest->description) : null,
//                property_exists($quest, "prompt") ? base64_decode($quest->prompt) : "",
//                property_exists($quest, "hover") ? base64_decode($quest->hover) : null,
//                property_exists($quest, "display_order") ? $quest->display_order: null,
//                property_exists($quest, "typeid") ? (int) $quest->typeid: 60,
//                property_exists($quest, "required") ? $quest->required : 1,
//                property_exists($quest, "publish") ? $quest->publish : 0,
//                property_exists($quest, "privacy_user") ? $quest->privacy_user : 0,
//                property_exists($quest, "searchable") ? $quest->searchable : 0,
//                property_exists($quest, "ascending") ? $quest->ascending : 1,
//                property_exists($quest, "display_only") ? $quest->display_only : 0,
//                property_exists($quest, "min_value") ? (strlen($quest->min_value) > 0 ? $quest->min_value : null) : null,
//                property_exists($quest, "max_value") ? (strlen($quest->max_value) > 0 ? $quest->max_value : null) : null,
//                $id
//            );
//            //error_log("\n\nupdate of " . $id . "\n" . $sql);
//            //var_error_log($paramarray);
//            $updated = $updated + mysql_cmd_with_prepare($sql, "ssssiiiiiiiiiii", $paramarray);
//            $options = [];
//            $useoptatob = true;
//            if (property_exists($quest, "options")) {
//                $optstring = $quest->options;
//                //error_log("\n\nquestion options = '" . $optstring . "'\n\n");
//                if (mb_strlen($optstring) > 3) {
//                    $optstring = base64_decode($optstring);
//                    //error_log("\n\ndecoded optstring = '" . $optstring . "'\n\n");
//                    if (mb_substr($optstring, 0, 7) == "nobtoa:") {
//                        $useoptatob = false;
//                        $optstring = mb_substr($optstring, 7);
//                    }
//                    $options  = json_decode($optstring);
//                    //error_log("\n\npost json decode\n");
//                    //var_error_log($options);
//                }
//            }
//            $optdisplayorder = 10;
//            $idsFound = "";

//            // Delete options no longer needed
//            foreach ($options as $opt) {
//                $opt->display_order = $optdisplayorder;
//                $optdisplayorder = $optdisplayorder + 10;

//                $ord = (int) $opt->ordinal;
//                if ($ord > 0) {
//                    $idsFound = $idsFound . ',' . $ord;
//                }
//            }
//            $optdelsql = "DELETE FROM SurveyQuestionOptionConfig WHERE questionid = ?";
//            if (mb_strlen($idsFound) >= 2) {
//                $optdelsql = $optdelsql . " and ordinal NOT IN (" . mb_substr($idsFound, 1) . ")";
//            }
//            $optdelsql = $optdelsql . ";";
//            //error_log($optdelsql);
//            $paramarray = array($id);
//            //var_error_log($paramarray);
//            $optdeleted = $optdeleted + mysql_cmd_with_prepare($optdelsql, "i", $paramarray);

//            // get new max ordinal
//            $optord = 0;
//            $maxsql = "SELECT MAX(ordinal) AS max FROM SurveyQuestionOptionConfig WHERE questionid = ?;";
//            $paramarray = array($id);
//            $result = mysqli_query_with_prepare_and_exit_on_error($maxsql, "i", $paramarray);
//            while ($row = mysqli_fetch_assoc($result)) {
//                $optord = $row["max"];
//            }
//            if ($optord == null) {
//                $optord = 0;
//            }
//            $optord = $optord + 1;

//            // Update existing options
//            foreach ($options as $opt) {
//                if ($opt->ordinal >= 0) {
//                    if ($useoptatob) {
//                        $paramarray = array(
//                            property_exists($opt, "value") ? base64_decode($opt->value) : "",
//                            $opt->display_order,
//                            property_exists($opt, "optionshort") ? base64_decode($opt->optionshort) : "",
//                            property_exists($opt, "optionhover") ? base64_decode($opt->optionhover) : "",
//                            property_exists($opt, "allowothertext") ? $opt->allowothertext : 0,
//                            $id, $opt->ordinal
//                        );
//                    } else {
//                        $paramarray = array(
//                            property_exists($opt, "value") ? $opt->value : "",
//                            $opt->display_order,
//                            property_exists($opt, "optionshort") ? $opt->optionshort : "",
//                            property_exists($opt, "optionhover") ? $opt->optionhover : "",
//                            property_exists($opt, "allowothertext") ? $opt->allowothertext : 0,
//                            $id, $opt->ordinal
//                        );
//                    }
//                    //error_log("\n\n" . $optsql);
//                    //var_error_log($paramarray);
//                    $optupdated = $optupdated + mysql_cmd_with_prepare($optsql, "sisssii", $paramarray);
//                }
//            }

//            // Insert new options
//            foreach ($options as $opt) {
//                if ($opt->ordinal < 0) {
//                    if ($useoptatob) {
//                        $paramarray = array(
//                            $id, $optord,
//                            property_exists($opt, "value") ? base64_decode($opt->value) : "",
//                            $opt->display_order,
//                            property_exists($opt, "optionshort") ? base64_decode($opt->optionshort) : "",
//                            property_exists($opt, "optionhover") ? base64_decode($opt->optionhover) : "",
//                            property_exists($opt, "allowothertext") ? $opt->allowothertext : 0
//                            );
//                    } else {
//                        $paramarray = array(
//                           $id, $optord,
//                           property_exists($opt, "value") ? $opt->value : "",
//                           $opt->display_order,
//                           property_exists($opt, "optionshort") ? $opt->optionshort : "",
//                           property_exists($opt, "optionhover") ? $opt->optionhover : "",
//                           property_exists($opt, "allowothertext") ? $opt->allowothertext : 0
//                           );
//                    }
//                    //error_log("\n\n" . $optinssql);
//                    //var_error_log($paramarray);
//                    $optinserted = $optinserted + mysql_cmd_with_prepare($optinssql, "iisissi", $paramarray);
//                    $optord = $optord + 1;
//                }
//            }
//        }
//    }
//    $message = "";
//    if ($deleted > 0) {
//        $message = ", " . $deleted . " questions deleted";
//    }
//    if ($inserted > 0) {
//        $message = $message . ", " . $inserted . " questions inserted";
//    }
//    if ($updated > 0) {
//        $message = $message . ", " . $updated . " questions updated";
//    }
//    if ($optdeleted > 0) {
//        $message = $message . ", " . $optdeleted . " options deleted";
//    }
//    if ($optinserted > 0) {
//        $message = $message . ", " . $optinserted . " options inserted";
//    }
//     if ($optupdated > 0) {
//        $message = $message . ", " . $optupdated . " options updated";
//    }
//   if (mb_strlen($message) > 2) {
//        $message = "<p>Database changes: " . mb_substr($message, 2) .  "</p>";
//        echo "message = '" . $message . "';\n";
//    }

    // get updated survey now with the id's in it
    fetch_table($tablename);
}

function fetch_table($tablename) {
    $db = DBDB;
    //error_log("table = " . $tablename);
    // json of schema and table contents
    $query=<<<EOD
        SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,  COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = '$db' and TABLE_NAME = '$tablename'
        ORDER BY ORDINAL_POSITION;
EOD;
    $result = mysqli_query_exit_on_error($query);
    $schema = array();
    $displayorder_found = false;
    $prikey = '';
    while ($row = $result->fetch_assoc()) {
        $schema[] = $row;
        if ($row["COLUMN_NAME"] == 'display_order')
            $displayorder_found = true;
        if ($row["COLUMN_KEY"] == 'PRI')
            $prikey = $prikey . $row["COLUMN_NAME"] . ",";
    }

    $prikey = substr($prikey, 0, -1);

	mysqli_free_result($result);

    // table select - special for Room Has Set - due to needing to build selects
    if ($tablename == 'RoomHasSet') {
        // get values for editor select for roomid
        $rooms = array();
        $query = "SELECT roomid, roomname FROM Rooms ORDER BY display_order;";
        $result = mysqli_query_exit_on_error($query);
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
        mysqli_free_result($result);

        // get values for editor select for roomid
        $roomsets = array();
        $query = "SELECT roomsetid, roomsetname FROM RoomSets ORDER BY display_order;";
        $result = mysqli_query_exit_on_error($query);
        while ($row = $result->fetch_assoc()) {
           $roomsets[] = $row;
        }
        mysqli_free_result($result);
    }
	
    $query="SELECT * FROM $tablename ";
    if ($displayorder_found)
        $query = $query . "ORDER BY display_order;";
    else if ($prikey != ",")
        $query = $query . "ORDER BY " . $prikey . ";";

	$result = mysqli_query_exit_on_error($query);
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
	mysqli_free_result($result);
    echo "tabledata = " . json_encode($rows) . ";\n";
    echo "tableschema = " . json_encode($schema) . ";\n";
    if ($tablename == 'RoomHasSet') {
        echo "rooms_select = " . json_encode($rooms) . ";\n";
        echo "roomsets_select = " . json_encode($roomsets) . ";\n";
    }
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "") {
    exit();
}

switch ($ajax_request_action) {
    case "fetchtable":
        $tablename = getString("tablename");
        fetch_table($tablename);
        break;
    case "updatetable":
        $tablename = getString("tablename");
        update_table($tablename);
        break;
    default:
        exit();
}

?>
