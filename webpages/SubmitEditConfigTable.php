<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('StaffCommonCode.php');

$schema_loaded = false;
$schema = array();
$displayorder_found = false;
$prikey = '';

function fetch_schema($tablename) {
    global $schema, $displayorder_found, $prikey, $schema_loaded;

    if ($schema_loaded == false) {
        $db = DBDB;
        //error_log("table = " . $tablename);
        // json of schema and table contents
        $query=<<<EOD
            SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH,  COLUMN_KEY, EXTRA FROM INFORMATION_SCHEMA.COLUMNS
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
        $schema_loaded = true;
    }
}

function update_table($tablename) {
    global $linki, $message_error, $schema, $displayorder_found, $prikey, $schema_loaded;
    //error_log("\n\nin update table:\n");
    //error_log("string loaded: " . getString("tabledata"));
    $rows = json_decode(base64_decode(getString("tabledata")));
    $tablename = getString("tablename");
    $indexcol = getString("indexcol");
    error_log("table: $tablename");
    error_log("indexcol: $indexcol");
    var_error_log($rows);

    fetch_schema($tablename);
    // reset display order to match new order and find which rows to delete
    $idsFound = "";
    $display_order = 10;
    foreach ($rows as $row) {
        $row->display_order = $display_order;
        $display_order = $display_order + 10;
        $id = (int) $row->$indexcol;
        if ($id) {
            $idsFound = $idsFound . ',' . $id;
        }
    }
    error_log($idsFound);

    // delete the ones no longer in the JSON uploaded, check for none uploaded
    if (mb_strlen($idsFound) < 2) {
        $sql = "DELETE FROM $tablename WHERE $indexcol >= 0;";
    } else {
        $sql = "DELETE FROM $tablename WHERE $indexcol NOT IN (" . mb_substr($idsFound, 1) . ");";
    }
    error_log("\ndelete unused rows = '" . $sql . "'");

    if (!mysqli_query_exit_on_error($sql)) {
        exit(); // Should have exited already.
    }
    $deleted = mysqli_affected_rows($linki);

    // insert new rows (those with id < 0)
    $inserted = 0;
    $fieldcount = 0;
    $datatype = "";
    $sql = "INSERT INTO $tablename (";
    foreach($schema as $col) {
        //var_error_log($col);
        if ($col['EXTRA'] != 'auto_increment') {
                $sql .= $col['COLUMN_NAME'] . ',';
                $datatype .= strpos($col['DATA_TYPE'], 'int') != false ? 'i' : 's';
                $fieldcount++;
        }
    }
    if ($fieldcount > 0) {
        $sql = substr($sql, 0, -1) . " VALUES (";
        for ($i = 0; $i < $fieldcount; $i++)
            $sql .= "?,";
        $sql = substr($sql, 0, -1) . ");";

        $paramarray = array();
        foreach ($rows as $row) {
            $id = (int) $row->$indexcol;
            if ($id < 0) {
                foreach($schema as $col) {
                    if ($col['EXTRA'] != 'auto_increment') {
                        $name = $col['COLUMN_NAME'];
                        $paramarray[] = $row->$name;
                    }
                }

                error_log("\n\nInsert of " . $id);
                error_log($sql);
                var_error_log($paramarray);
                $inserted = $inserted + mysql_cmd_with_prepare($sql, $datatype, $paramarray);
            }
        }
    }

    // update existing rows (those with id >= 0)
    $updated = 0;

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
//            }

    $message = "";
    if ($deleted > 0) {
        $message = ", " . $deleted . " rows deleted";
    }
    if ($inserted > 0) {
        $message = $message . ", " . $inserted . " rows inserted";
    }
    if ($updated > 0) {
        $message = $message . ", " . $updated . " rows updated";
    }

   if (mb_strlen($message) > 2) {
        $message = "<p>Database changes: " . mb_substr($message, 2) .  "</p>";
        echo "message = '" . $message . "';\n";
    }

    // get updated survey now with the id's in it
    fetch_table($tablename);
}

function fetch_table($tablename) {
    global $schema, $displayorder_found, $prikey;
    $db = DBDB;
    //error_log("table = " . $tablename);
    // json of schema and table contents
    fetch_schema($tablename);

    // get the foreign keys
    $query = <<<EOD
SELECT TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = '$db' AND (TABLE_NAME = '$tablename' OR REFERENCED_TABLE_NAME = '$tablename')
    AND CONSTRAINT_NAME != 'PRIMARY'
ORDER BY COLUMN_NAME, TABLE_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
EOD;
    $foreign_keys = array();
    $referenced_columns = array();
    $result = mysqli_query_exit_on_error($query);
    while ($row = $result->fetch_assoc()) {
        if (strcasecmp($row["TABLE_NAME"], $tablename) == 0) {
            // table refers to another table for one of its fields;
            $referenced_columns[] = $row["COLUMN_NAME"] . ":" . $row["REFERENCED_TABLE_NAME"] . "." . $row["REFERENCED_COLUMN_NAME"];
        } else {
            // table is referenced by another table
            $foreign_keys[] = $row["REFERENCED_COLUMN_NAME"] . ":" . $row["TABLE_NAME"] . "."  . $row["COLUMN_NAME"];
        }
    }
    //mysqli_free_result($result);
    //error_log("referenced columns");
    //var_error_log($referenced_columns);
    //error_log("foreign keys");
    //var_error_log($foreign_keys);

    $withclause = "";
    $joinclause = "";
    $curfield = "";
    $mycurname = "";
    $union = "";
    $occurs = "";

    if (count($foreign_keys) > 0 ) {
        // Build CTE's for getting count of foreign key usage
        foreach ($foreign_keys as $key) {
            $colonpos = strpos($key, ':');
            $mycolname = substr($key, 0, $colonpos);
            $periodpos = strpos($key, '.');
            $reftable = substr($key, $colonpos + 1, $periodpos - ($colonpos + 1));
            $reffield = substr($key, $periodpos + 1);
            //error_log("Referenced: '$key'");
            //error_log("colname: '$mycolname'");
            //error_log("reftable: '$reftable'");
            //error_log("reffield: '$reffield'");
            if ($reffield != $curfield) {
                $union = "";
                if ($withclause == "")
                    $withclause = "WITH Ref" . $reffield . " AS (\n";
                else {
                    $withclause .= "), SUM$curfield AS (\nSELECT $curfield, SUM(occurs) AS occurs FROM Ref$curfield GROUP BY $curfield\n), Ref" . $reffield . " AS (\n";
                    $joinclause .= "LEFT OUTER JOIN SUM$curfield ON ($tablename.$mycurname = SUM$curfield.$curfield)\n";
                    if ($occurs != "")
                        $occurs .= "+";
                    $occurs .= "SUM$curfield.occurs";
                }

                $mycurname = $mycolname;
                $curfield = $reffield;
            }
            $withclause .= "$union SELECT '$reftable', $reffield, COUNT(*) AS occurs FROM $reftable\n";
            $union = "UNION ALL";
        }
        $withclause .= "), SUM$curfield AS (\nSELECT $curfield, SUM(occurs) AS occurs FROM Ref$curfield GROUP BY $curfield\n)\n";
        $joinclause .= "LEFT OUTER JOIN SUM$curfield ON ($tablename.$mycurname = SUM$curfield.$curfield)\n";
        if ($occurs != "")
            $occurs .= "+";
        $occurs .= "SUM$curfield.occurs";
        $occurs = "CASE WHEN $occurs IS NULL THEN 0 ELSE $occurs END AS Usage_Count";
    }
    else
        $occurs = "0 AS Usage_Count";

    $refstring = "";
    foreach($referenced_columns as $key) {
        // table select - get select list for field that is a foreign key to another table
        $colonpos = strpos($key, ':');
        $colname = substr($key, 0, $colonpos);
        $periodpos = strpos($key, '.');
        $reftable = substr($key, $colonpos + 1, $periodpos - ($colonpos + 1));
        $reffield = substr($key, $periodpos + 1);

        //error_log("Referenced: '$key'");
        //error_log("colname: '$colname'");
        //error_log("reftable: '$reftable'");
        //error_log("reffield: '$reffield'");
        $namefield = str_replace("id", "name", $reffield);
        $data = array();
        $query = "SELECT $reffield AS id, $namefield AS name FROM $reftable ORDER BY display_order;";
        $result = mysqli_query_exit_on_error($query);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        mysqli_free_result($result);
        $refstring .= $colname . "_select = " . json_encode($data) . "\n";
    }

    $query="$withclause SELECT $occurs, $tablename.* FROM $tablename\n$joinclause";
    if ($displayorder_found)
        $query = $query . "ORDER BY display_order;";
    else if ($prikey != ",")
        $query = $query . "ORDER BY " . $prikey . ";";

    //error_log($query);
	$result = mysqli_query_exit_on_error($query);
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
	mysqli_free_result($result);
    echo "tabledata = " . json_encode($rows) . ";\n";
    echo "tableschema = " . json_encode($schema) . ";\n";
    echo $refstring;
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
