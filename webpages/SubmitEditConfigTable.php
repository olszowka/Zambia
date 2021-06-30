<?php
// Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check if logged in and for staff privileges

$schema_loaded = false;
$schema = array();
$displayorder_found = false;
$prikey = '';
$json_return = array();

function fetch_schema($tablename) {
    global $schema, $displayorder_found, $prikey, $schema_loaded;

    if ($schema_loaded == false) {
        $db = DBDB;
        //error_log("table = " . $tablename);
        // json of schema and table contents
        $query=<<<EOD
SELECT
        COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, COLUMN_KEY, EXTRA
    FROM
        INFORMATION_SCHEMA.COLUMNS
    WHERE
            TABLE_SCHEMA = '$db'
        AND TABLE_NAME = '$tablename'
    ORDER BY
        ORDINAL_POSITION;
EOD;
        $result = mysqli_query_exit_on_error($query);
        $schema = array();
        $displayorder_found = false;
        $prikey = '';
        while ($row = $result->fetch_assoc()) {
            $schema[] = $row;
            if ($row["COLUMN_NAME"] == 'display_order') {
                $displayorder_found = true;
            }
            if ($row["COLUMN_KEY"] == 'PRI') {
                $prikey = $prikey . $row["COLUMN_NAME"] . ",";
            }
        }

        $prikey = substr($prikey, 0, -1);

        mysqli_free_result($result);
        $schema_loaded = true;
    }
}

function update_table($tablename) {
    global $json_return, $linki, $schema, $prikey;

    if (!(may_I('ce_All') || may_I("ce_$tablename"))) {
        $message_error = "You do not have permission to view this page.";
        RenderErrorAjax($message_error);
        exit();
    }

    $rows = json_decode(base64_decode(getString("tabledata")));
    // $json_return['debug'] = print_r($rows, true);
    $tablename = getString("tablename");

    $indexcol = getString("indexcol");

    fetch_schema($tablename);
    // reset display order to match new order and find which rows to delete
    $idsFound = "";
    $display_order = 10;
    foreach ($rows as $row) {
        if ($row->display_order >= 0) {
            $row->display_order = $display_order;
            $display_order = $display_order + 10;
        }
        $id = (int) $row->$indexcol;
        if ($id) {
            $idsFound = $idsFound . ',' . $id;
        }
    }
    //error_log($idsFound);

    // delete the ones no longer in the JSON uploaded, check for none uploaded
    if (mb_strlen($idsFound) < 2) {
        $sql = "DELETE FROM $tablename WHERE $indexcol >= 0;";
    } else {
        $sql = "DELETE FROM $tablename WHERE $indexcol NOT IN (" . mb_substr($idsFound, 1) . ");";
    }
    //error_log("\ndelete unused rows = '" . $sql . "'");

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
            $datatype .= strpos($col['DATA_TYPE'], 'int') !== false ? 'i' : 's';
            $fieldcount++;
        }
    }
    if ($fieldcount > 0) {
        $sql = substr($sql, 0, -1) . ") VALUES (";
        for ($i = 0; $i < $fieldcount; $i++) {
            $sql .= "?,";
        }
        $sql = substr($sql, 0, -1) . ");";

        foreach ($rows as $row) {
            $paramarray = array();
            $id = (int) $row->$indexcol;
            if ($id < 0) {
                foreach($schema as $col) {
                    if ($col['EXTRA'] != 'auto_increment') {
                        $name = $col['COLUMN_NAME'];
                        $paramarray[] = $row->$name;
                    }
                }

                //error_log("\n\nInsert of '$id' with datatype of '$datatype'");
                //error_log($sql);
                //var_error_log($paramarray);
                $inserted = $inserted + mysql_cmd_with_prepare($sql, $datatype, $paramarray);
            }
        }
    }

    // update existing rows (those with id >= 0)
    $updated = 0;
    $datatype = "";

    $sql = "UPDATE $tablename SET\n";
    $keytype = 's';
    foreach($schema as $col) {
        if ($col['COLUMN_KEY'] != 'PRI') {
            if ($col['COLUMN_NAME'] != 'Usage_COUNT') {
                $sql .= "\t" . $col['COLUMN_NAME'] . " = ?,\n";
                $datatype .= strpos($col['DATA_TYPE'], 'int') !== false ? 'i' : 's';
                $fieldcount++;
            }
        } else {
            $keytype = strpos($col['DATA_TYPE'], 'int') !== false ? 'i' : 's';
        }
    }
    $sql = substr($sql, 0, -2) .  "\nWHERE $prikey = ?;";
    $datatype .= $keytype;;
    //error_log($sql);
    //error_log($datatype);
    foreach ($rows as $row) {
        $id = $row->$prikey;
        //error_log("\n\nUpdate Loop: " . $id);
        if ($id >= 0) {
            $paramarray = array();
            foreach($schema as $col) {
                if ($col['COLUMN_KEY'] != 'PRI') {
                    $colname = $col['COLUMN_NAME'];
                    if ($colname != 'Usage_COUNT') {
                        $paramarray[] = $row->$colname;
                    }
                }
            }
            $paramarray[] = $id;
            //error_log("\n\nupdate of '$id' with '$datatype'\n" . $sql);
            //var_error_log($paramarray);
            $updated = $updated + mysql_cmd_with_prepare($sql, $datatype, $paramarray);
        }
    }

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
        $message = "<p>Database changes: " . mb_substr($message, 2) . "</p>";
    } else {
        $message = "";
    }

    fetch_table($tablename, $message);
}

function fetch_table($tablename, $message) {
    global $displayorder_found, $json_return, $schema;
    $db = DBDB;
    if (!(may_I('ce_All') || may_I("ce_$tablename"))) {
        $message_error = "You do not have permission to view this page.";
        RenderErrorAjax($message_error);
        exit();
    }

    if (strpos($tablename, ' ', 0) !== false) {
        $json_return["message"] = $tablename;
        echo json_encode($json_return) . "\n";
        return;
    }

    // json of schema and table contents
    fetch_schema($tablename);
    $json_return["tableschema"] = $schema;

    // get the foreign keys
    $query = <<<EOD
SELECT
        TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM
        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE
            TABLE_SCHEMA = '$db'
        AND (
               TABLE_NAME = '$tablename'
            OR REFERENCED_TABLE_NAME = '$tablename'
            )
        AND CONSTRAINT_NAME != 'PRIMARY'
        AND REFERENCED_COLUMN_NAME != ''
    ORDER BY
        COLUMN_NAME, TABLE_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME;
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
            $foreign_keys[] = array(
                "TABLE_NAME" => $row["TABLE_NAME"],
                "COLUMN_NAME" => $row["COLUMN_NAME"],
                "REFERENCED_TABLE_NAME" => $row["REFERENCED_TABLE_NAME"],
                "REFERENCED_COLUMN_NAME" => $row["REFERENCED_COLUMN_NAME"]);
        }
    }

    // Build CTE's for getting count of foreign key usage
    if (count($foreign_keys) > 0 ) {
        $innerQueryArray = array();
        foreach ($foreign_keys as $key) {
            $innerQueryArray[] = "SELECT ${key["COLUMN_NAME"]} AS ${key["REFERENCED_COLUMN_NAME"]}, count(*) AS occurs FROM ${key["TABLE_NAME"]} GROUP BY ${key["COLUMN_NAME"]}";
        }
        $innerQuery = implode(" UNION ALL ", $innerQueryArray);
        if (count($innerQueryArray) > 1) {
            $middleQuery = "SELECT ${key["REFERENCED_COLUMN_NAME"]}, sum(occurs) AS occurs FROM ($innerQuery) AS union1 GROUP BY ${key["REFERENCED_COLUMN_NAME"]}";
        } else {
            $middleQuery = $innerQuery;
        }
        $mainQuery = "SELECT ${key["REFERENCED_TABLE_NAME"]}.*, ifnull(occurs, 0) AS Usage_Count FROM ${key["REFERENCED_TABLE_NAME"]} LEFT JOIN ($middleQuery) AS union2 USING (${key["REFERENCED_COLUMN_NAME"]})";
    } else {
        $mainQuery = "SELECT *, 0 AS Usage_Count FROM $tablename";
    }
    if ($displayorder_found) {
        $mainQuery .= " ORDER BY display_order"; // Table data must be sent in display_order order because tabulator will send it back that way and that's how we track order changes by the user.
    }
    $mainQuery .= ";";
    // now get the data rows

    $result = mysqli_query_exit_on_error($mainQuery);
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    mysqli_free_result($result);
    $json_return["tabledata"] = $rows;

    // table select - get select list for field that is a foreign key to another table
    foreach($referenced_columns as $key) {
        $colonpos = strpos($key, ':');
        $colname = substr($key, 0, $colonpos);
        $periodpos = strpos($key, '.');
        $reftable = substr($key, $colonpos + 1, $periodpos - ($colonpos + 1));
        $reffield = substr($key, $periodpos + 1);

        $namefield = str_replace("id", "name", $reffield);
        $data = array();
        $query = "SELECT $reffield AS id, $namefield AS name FROM $reftable ORDER BY display_order;";
        $result = mysqli_query_exit_on_error($query);
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        mysqli_free_result($result);
        if (count($data) == 0) {
            if ($message != "") {
                $message .= "<br/>";
            }
            $message .= "Warning: Cannot edit this table until the table $reftable has been edited and is not empty";
        }
        $json_return[$colname . "_select"] = $data;
    }


    if ($message != "") {
        $json_return["message"] = $message;
    }
    echo json_encode($json_return) . "\n";
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "") {
    exit();
}

switch ($ajax_request_action) {
    case "fetchtable":
        $tablename = getString("tablename");
        fetch_table($tablename, "");
        break;
    case "updatetable":
        $tablename = getString("tablename");
        update_table($tablename);
        break;
    default:
        exit();
}
?>