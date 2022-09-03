<?php
// Copyright (c) 2011-2022 Peter Olszowka. All rights reserved. See copyright document for more details.

/*
 * mysql_cmd_with_prepare_multi:
 *  as a single transaction, prepare an insert/update/delete statement, execute one or more data value sets, and return the number of rows affected
 *  query = valid mysql insert, udpate or delete statement with ? for parameter binding
 *  type_string = datatypes of the specific ? values in the update statement
 *  param_repeat_arr = array of objects to update
 *      contains one row per execute
 *          each row contains one element per ? in the update statement, datatype based on type_string value
 */
function mysql_cmd_with_prepare_multi($query, $type_string, $param_repeat_arr) {
    global $mysqli;

	$rows = 0;
	$message_error = "";
    $mysqli->autocommit(FALSE); //turn on transactions
    try {
        if (!$mysqli->begin_transaction()) {
            throw new ErrorException("DB begin transaction statement failed.");
        }
        if (!$mysqli_stmt = $mysqli->prepare($query)) {
            throw new ErrorException("DB prepare statement failed.");
        }
        foreach ($param_repeat_arr as $param_arr) {
            if (!$mysqli_stmt->bind_param($type_string, ...$param_arr)) {
                throw new ErrorException("DB bind param statement failed.");
            }
            if (!$mysqli_stmt->execute()) {
                throw new ErrorException("DB execute statement failed.");
            }
			$rows = $rows + $mysqli_stmt->affected_rows;
        }
        if (!$mysqli_stmt->close()) {
            throw new ErrorException("DB close statement failed.");
        }
        if (!$mysqli->commit()) {
            throw new ErrorException("DB commit transaction statement failed.");
        }
    }
    catch (Exception $e) {
        $mysqli->rollback(); //remove all queries from queue if error (undo)
        $message_error = log_mysqli_error_new($query, "");
        RenderError($message_error);
    }
    $mysqli->autocommit(TRUE); //turn off transactions

	if ($message_error != "") {
        return NULL;
    }

	return $rows;
}

/*
 * mysql_cmd_with_prepare:
 *  prepare an insert/update/delete statement, execute one data value set, and return the number of rows affected
 *  query = valid mysql insert/udpate/delete statement with ? for parameter binding
 *  type_string = datatypes of the specific ? values in the update statement
 *  param_arr = array of elements per ? in the update statement, datatype based on type_string value
 */
function mysql_cmd_with_prepare($query, $type_string, $param_arr) {
    global $linki;

	$rows = 0;
	$message_error = "";
    try {
        $stmt = mysqli_prepare($linki, $query);
        mysqli_stmt_bind_param($stmt, $type_string, ...$param_arr);
        mysqli_stmt_execute($stmt);
        // $foo = mysqli_info($linki);
		$rows = $rows + mysqli_affected_rows($linki);
        mysqli_stmt_close($stmt);
    }
    catch (Exception $e) {
        $message_error = log_mysqli_error($query, "");
        RenderError($message_error);
    }

	if ($message_error != "") {
        return NULL;
    }

	return $rows;
}

function mysql_prepare_query_XML($query_array, $parmtype_array, $param_array) {
	global $linki, $message_error;
	$xml = new DomDocument("1.0", "UTF-8");
	$doc = $xml -> createElement("doc");
	$doc = $xml -> appendChild($doc);
    foreach ($query_array as $name=>$query) {
        $query = trim($query);
        $parama = $param_array[$name];
        $params = $parmtype_array[$name];
        $result = mysqli_query_with_prepare_and_exit_on_error($query, $params, $parama);
        $queryNode = $xml -> createElement("query");
        $queryNode = $doc -> appendChild($queryNode);
        $queryNode->setAttribute("queryName", $name);
        if ($result !== false) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rowNode = $xml->createElement("row");
                $rowNode = $queryNode->appendChild($rowNode);
                foreach ($row as $fieldname => $fieldvalue) {
                    if ($fieldvalue !== "" && $fieldvalue !== null) {
                        $rowNode->setAttribute($fieldname, $fieldvalue);
                    }
                }
            }
        }
        mysqli_free_result($result);
    }
	return $xml;
}

function mysql_query_XML($query_array) {
	global $linki, $message_error;
	$xml = new DomDocument("1.0", "UTF-8");
	$doc = $xml -> createElement("doc");
	$doc = $xml -> appendChild($doc);
	$multiQueryStr = "";
    foreach ($query_array as $query) {
        $query = trim($query);
        $multiQueryStr .= $query;
        if (substr($query, -1, 1) !== ";") {
            $multiQueryStr .= ";";
        }
    }
    $status = mysqli_multi_query($linki, $multiQueryStr);
    $queryNo = 0;
    $queryNameArr = array_keys($query_array);
    do {
        if ($queryNo !== 0) {
            $status = mysqli_next_result($linki);
        }
        if (!$status) {
            $message_error = $multiQueryStr . "<br />";
            $message_error .= "Error with query number " . ($queryNo + 1) . " <br />";
            $message_error .= mysqli_error($linki) . "<br />";
            error_log($multiQueryStr);
            error_log("Error with query number " . ($queryNo + 1));
            error_log(mysqli_error($linki));
            return false;
        }
        $queryNode = $xml -> createElement("query");
        $queryNode = $doc -> appendChild($queryNode);
        $queryNode->setAttribute("queryName", $queryNameArr[$queryNo]);
        $result = mysqli_store_result($linki);
        if ($result !== false) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rowNode = $xml->createElement("row");
                $rowNode = $queryNode->appendChild($rowNode);
                foreach ($row as $fieldname => $fieldvalue) {
                    if ($fieldvalue !== "" && $fieldvalue !== null) {
                        $rowNode->setAttribute($fieldname, $fieldvalue);
                    }
                }
            }
            mysqli_free_result($result);
        }
        $queryNo++;
    } while (mysqli_more_results($linki));
	return $xml;
}

function mysql_result_to_XML($queryName, $result) {
	$xml = new DomDocument("1.0", "UTF-8");
	$doc = $xml -> createElement("doc");
	$doc = $xml -> appendChild($doc);

    $queryNode = $xml -> createElement("query");
    $queryNode = $doc -> appendChild($queryNode);
    $queryNode->setAttribute("queryName", $queryName);
    while ($row = mysqli_fetch_assoc($result)) {
        $rowNode = $xml->createElement("row");
        $rowNode = $queryNode->appendChild($rowNode);
        foreach ($row as $fieldname => $fieldvalue) {
            if ($fieldvalue !== "" && $fieldvalue !== null) {
                $rowNode->setAttribute($fieldname, $fieldvalue);
            }
        }
    }

	return $xml;
}

function log_mysqli_error($query, $additional_error_message) {
    global $linki;
    $result = "";
    error_log("mysql query error in {$_SERVER["SCRIPT_FILENAME"]}");
    if (!empty($query)) {
        error_log($query);
        $result = $query . "<br>\n";
    }
    $query_error = mysqli_error($linki);
    if (!empty($query_error)) {
        error_log($query_error);
        $result .= $query_error . "<br>\n";
    }
    if (!empty($additional_error_message)) {
        error_log($additional_error_message);
        $result .= $additional_error_message . "<br>\n";
    }
    return $result;
}

function log_mysqli_error_new($query, $additional_error_message) {
    global $mysqli;
    $result = "";
    error_log("mysql query error in {$_SERVER["SCRIPT_FILENAME"]}");
    if (!empty($query)) {
        error_log($query);
        $result = $query . "<br>\n";
    }
    $query_error = $mysqli->error;
    if (!empty($query_error)) {
        error_log($query_error);
        $result .= $query_error . "<br>\n";
    }
    if (!empty($additional_error_message)) {
        error_log($additional_error_message);
        $result .= $additional_error_message . "<br>\n";
    }
    return $result;
}

function mysqli_query_exit_on_error($query) {
    return mysqli_query_with_error_handling($query, true);
}

function mysqli_query_with_error_handling($query, $exit_on_error = false, $ajax = false) {
    global $linki, $message_error;
    $result = mysqli_query($linki, $query);

    if (!$result) {
        $message_error = log_mysqli_error($query, "");
        if ($exit_on_error) {
            RenderError($message_error, $ajax); // will exit script
        }
    }
    return $result;
}

function mysqli_query_with_prepare_and_exit_on_error($query, $type_string, $param_arr) {
    return mysqli_query_with_prepare_and_error_handling($query, $type_string, $param_arr, true);
}

function mysqli_query_with_prepare_and_error_handling($query, $type_string, $param_arr, $exit_on_error = false, $ajax = false) {
    global $message_error, $mysqli;

    try {
        $statement = $mysqli->stmt_init();
        if (!$statement->prepare($query)) {
            //$message_error = $mysqli->error;
            throw new ErrorException("DB prepare statement failed.");
        };
        $statement->bind_param($type_string, ...$param_arr);
        if (!$statement->execute()) {
            $message_error = log_mysqli_error_new($query, "");
            if ($exit_on_error) {
                RenderError($message_error, $ajax);
            }
            return false;
        };
        $result = $statement->get_result();
        $statement->close();
    } catch (Exception $e) {
        $message_error = log_mysqli_error_new($query, "");
        if ($exit_on_error) {
            RenderError($message_error, $ajax);
        }
        return false;
    }
    return $result;
}

function rollback_mysqli($exit_on_error = false, $ajax = false) {
    global $linki;
    if (mysqli_rollback($linki)) {
        return true;
    }
    $message_error = log_mysqli_error("<ROLLBACK>", "");
    if ($exit_on_error) {
        RenderError($message_error, $ajax); // will exit script
    }
    return false;
}

function populateCustomTextArray() {
    global $customTextArray, $title;
    $customTextArray = array();
    $query = "SELECT tag, textcontents FROM CustomText WHERE page = \"$title\";";
    if (!$result = mysqli_query_with_error_handling($query))
        return false;
    while ($row = mysqli_fetch_assoc($result)) {
        $customTextArray[$row["tag"]] = $row["textcontents"];
    }
    mysqli_free_result($result);
    return true;
}

// Function prepare_db_and_more()
// Opens database channel; Do both procedural and class versions populating both global variables
if (!include ('../db_name.php'))
	include ('./db_name.php'); // scripts which rely on this file (db_functions.php) may run from a different directory
function prepare_db_and_more() {
    global $con_start_php_timestamp, $linki, $fatalError, $mysqli;
    $linki = mysqli_connect(DBHOSTNAME, DBUSERID, DBPASSWORD, DBDB);
    if (!$linki) {
        $fatalError = true;
        return false;
    }
    $mysqli = new mysqli(DBHOSTNAME, DBUSERID, DBPASSWORD, DBDB);
    if (mysqli_connect_errno()) {
        $fatalError = true;
        return false;
    }
    date_default_timezone_set(PHP_DEFAULT_TIMEZONE);
    if (!mysqli_set_charset($linki, "utf8")) {
        $fatalError = true;
        return false;
    }
    if (!$mysqli->set_charset("utf8")) {
        $fatalError = true;
        return false;
    };
    $con_start_php_timestamp = date_create_from_format("Y-m-d H:i:s", CON_START_DATIM);
    if ($con_start_php_timestamp === false) {
        $fatalError = true;
        RenderError("Con start date (CON_START_DATIM) not configured correctly. Further execution not possible.");
        return false; // Should have exited anyway
    }
    if (DB_DEFAULT_TIMEZONE != "") {
        $query = "SET time_zone = '" . DB_DEFAULT_TIMEZONE . "';";
        mysqli_query_exit_on_error($query);
    }
    return true;
}

/*
 * push_query_arrays:
 *  Use to build up an update query from the page query parameters which are populated
 *  $value -- value field to be updated to including (""); should be NULL if not to be updated
 *  $field_name -- name of column in db table
 *  $param_type -- 's' or 'i'; string or integer
 *  $max_len -- for strings only; integer maximum length permitted by db column
 *  $query_portion_arr -- array to collect portions of query in "foo = bar" format
 *  $query_param_arr -- array of parameters to be sent to prepared statement executor
 *  $query_param_type_str -- string of parameter types to be sent to prepared statement executor
 */
function push_query_arrays($value, $field_name, $param_type, $max_len, &$query_portion_arr, &$query_param_arr, &$query_param_type_str) {
    if (!empty($value) || $value === '') {
        if ($param_type === 's' && mb_strlen($value) > $max_len && $max_len !== 0) {
            $message_error = "$field_name field is greater than maximum length of $max_len.  Db not updated.";
            RenderErrorAjax($message_error);
            exit();
        }
        $query_portion_arr[] = "$field_name = ?";
        $query_param_arr[] = $value;
        $query_param_type_str .= $param_type;
    }
}

// The table SessionEditHistory has a timestamp column which is automatically set to the
// current timestamp by MySQL.
function record_session_history($sessionid, $badgeid, $name, $email, $editcode, $statusid) {
	global $linki;
	$name = mysqli_real_escape_string($linki, $name);
	$email = mysqli_real_escape_string($linki, $email);
	$query = <<<EOD
INSERT INTO SessionEditHistory
    SET
        sessionid = $sessionid,
        badgeid = "$badgeid",
        name = "$name",
        email_address = "$email",
        sessioneditcode = $editcode,
        statusid = $statusid;
EOD;
	return mysqli_query_with_error_handling($query);
}

// Function get_name_and_email(&$name, &$email)
// Gets name and email from db if they are available and not already set
// returns FALSE if error condition encountered.  Error message in global $message_error
function get_name_and_email(&$name, &$email) {
    global $badgeid;
    if (!empty($name)) {
        return true;
    }
    if (isset($_SESSION['name'])) {
        $name = $_SESSION['name'];
        $email = $_SESSION['email'];
        //error_log("get_name_and_email found a name in the session variables.");
        return true;
    }
    if (may_I('Staff') || may_I('Participant')) { //name and email should be found in db if either set
        $query = "SELECT pubsname FROM Participants WHERE badgeid = '$badgeid';";
		if (!$result = mysqli_query_with_error_handling($query)) {
            return false;
        }
        $name = mysqli_fetch_row($result)[0];
		mysqli_free_result($result);
        if ($name === '') {
            $name = ' '; //if name is null or '' in db, set to ' ' so it won't appear unpopulated in query above
        }
        $query = "SELECT badgename, email FROM CongoDump WHERE badgeid = \"$badgeid\";";
		if (!$result = mysqli_query_with_error_handling($query)) {
            return false;
        }
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);
        if ($name === ' ') {
            $name = $row[0];
        }
        $email = $row[1];
    }
    return true; //return TRUE even if didn't retrieve from db because there's nothing to be done
}

// Function populate_select_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control.
//
function populate_select_from_table($table_name, $default_value, $option_0_text, $default_flag) {
    // set $default_value=-1 for no default value (note not really supported by HTML)
    // set $default_value=0 for initial value to be set as $option_0_text
    // otherwise the initial value will be equal to the row whose id == $default_value
    // assumes id's in the table start at 1
    // if $default_flag is true, the option 0 will always appear.
    // if $default_flag is false, the option 0 will only appear when $default_value is 0.
    if ($default_value == 0) {
        echo "<option value=\"0\" selected>$option_0_text</option>\n";
    } elseif ($default_flag) {
        echo "<option value=\"0\">$option_0_text</option>\n";
    }
    $query = "Select * FROM $table_name ORDER BY display_order;";
    if (!$result = mysqli_query_with_error_handling($query)) {
        return false;
    }
    while (list($option_value, $option_name) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<option value=\"$option_value\"";
        if ($option_value == $default_value) {
            echo " selected=\"selected\"";
        }
        echo ">$option_name</option>\n";
    }
    mysqli_free_result($result);
    return true;
}

// Function populate_select_from_query(...)
// Reads parameters (see below) and a specified query for the db.
// Outputs HTML of the "<OPTION>" values for a Select control.
//
function populate_select_from_query($query, $default_value, $option_0_text, $default_flag) {
    // set $default_value=-1 for no default value (note not really supported by HTML)
    // set $default_value=0 for initial value to be set as $option_0_text
    // otherwise the initial value will be equal to the row whose id == $default_value
    // assumes id's in the table start at 1
    // if $default_flag is true, the option 0 will always appear.
    // if $default_flag is false, the option 0 will only appear when $default_value is 0.
    if ($default_value == 0) {
        echo "<option value=\"0\" selected>$option_0_text</option>\n";
    } elseif ($default_flag) {
        echo "<option value=\"0\">$option_0_text</option>\n";
    }
    $result = mysqli_query_with_error_handling($query);
    while (list($option_value, $option_name) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<option value=\"$option_value\"";
        if ($option_value == $default_value)
            echo " selected";
        echo ">$option_name</option>\n";
    }
    mysqli_free_result($result);
}

// Function populate_multiselect_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control with
// multiple enabled.
//
function populate_multiselect_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1 '
    // skipset is array of integers of values of id from table to preselect
    // error_log("Zambia->populate_multiselect_from_table->\$skipset: ".print_r($skipset,TRUE)."\n"); // only for debugging
    if ($skipset == "") {
        $skipset = array(-1);
    }
    $query = "SELECT * FROM $table_name ORDER BY display_order;";
    $result = mysqli_query_with_error_handling($query);
    while (list($option_value, $option_name) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<option value=\"$option_value\"";
        if (array_search($option_value, $skipset) !== FALSE) {
            echo " selected=\"selected\"";
        }
        echo ">$option_name</option>\n";
    }
    mysqli_free_result($result);
}

// Function populate_multisource_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control associated
// with the *source* of an active update box.
//
function populate_multisource_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1 '
    // skipset is array of integers of values of id from table not to include
    if ($skipset == "") {
        $skipset = array(-1);
    }
    $query = "SELECT * FROM $table_name ORDER BY display_order;";
    $result = mysqli_query_with_error_handling($query);
    while (list($option_value, $option_name) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if (array_search($option_value, $skipset) === false) {
            echo "<option value=\"$option_value\" >$option_name</option>\n";
        }
    }
    mysqli_free_result($result);
}

// Function populate_multidest_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control associated
// with the *destination* of an active update box.
//
function populate_multidest_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1                        '
    // skipset is array of integers of values of id from table to include
    // in "dest" because they were skipped from "source"
    if ($skipset == "") {
        $skipset = array(-1);
    }
    $query = "SELECT * FROM $table_name ORDER BY display_order;";
    $result = mysqli_query_with_error_handling($query);
    while (list($option_value, $option_name) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if (array_search($option_value, $skipset) !== false) {
            echo "<option value=\"$option_value\" >$option_name</option>\n";
        }
    }
    mysqli_free_result($result);
}

// Function update_session()
// Takes data from global $session array and updates
// the tables Sessions, SessionHasFeature, SessionHasService and SessionHasTag.
//
function update_session() {
    $sessionf = filter_session(); // reads global $session array and returns sanitized copy
    $id = $sessionf["id"];

    $query=<<<EOD
UPDATE Sessions SET
        trackid="{$sessionf["track"]}",
        typeid="{$sessionf["type"]}",
        divisionid="{$sessionf["divisionid"]}",
        pubstatusid="{$sessionf["pubstatusid"]}",
        languagestatusid="{$sessionf["languagestatusid"]}",
        pubsno="{$sessionf["pubno"]}",
        title="{$sessionf["title"]}",
        secondtitle="{$sessionf["secondtitle"]}",
        pocketprogtext="{$sessionf["pocketprogtext"]}",
        progguidhtml="{$sessionf["progguidhtml"]}",
        progguiddesc="{$sessionf["progguiddesc"]}",
        persppartinfo="{$sessionf["persppartinfo"]}",
        duration="{$sessionf["duration"]}",
        estatten={$sessionf["estatten"]},
        kidscatid="{$sessionf["kidscatid"]}",
        signupreq={$sessionf["signupreq"]},
        invitedguest={$sessionf["invitedguest"]},
        roomsetid="{$sessionf["roomsetid"]}",
        notesforpart="{$sessionf["notesforpart"]}",
        servicenotes="{$sessionf["servnotes"]}",
        statusid="{$sessionf["status"]}",
        notesforprog="{$sessionf["notesforprog"]}",
        meetinglink="{$sessionf["mlink"]}"
    WHERE
        sessionid = $id;
EOD;
    if (!mysqli_query_with_error_handling($query)) {
        return false;
    }
    $query = "DELETE FROM SessionHasFeature WHERE sessionid = $id;";
    if (!mysqli_query_with_error_handling($query)) {
        return false;
    }
    if (!empty($sessionf["features"])) {
        $query = "INSERT INTO SessionHasFeature (sessionid, featureid) VALUES ";
        foreach ($sessionf["features"] as $feature) {
            $query .= "($id, $feature),";
        }
        $query = substr($query, 0, -1) . ";"; // drop trailing comma
        if (!mysqli_query_with_error_handling($query)) {
            return false;
        }
    }
    $query = "DELETE FROM SessionHasService WHERE sessionid = $id;";
    if (!mysqli_query_with_error_handling($query)) {
        return false;
    }
    if (!empty($sessionf["services"])) {
        $query = "INSERT INTO SessionHasService (sessionid, serviceid) VALUES ";
        foreach ($sessionf["services"] as $service) {
            $query .= "($id, $service),";
        }
        $query = substr($query, 0, -1) . ";"; // drop trailing comma
        if (!mysqli_query_with_error_handling($query)) {
            return false;
        }
    }
    $query = "DELETE FROM SessionHasTag WHERE sessionid = $id;";
    if (!mysqli_query_with_error_handling($query)) {
        return false;
    }
    if (!empty($sessionf["tags"])) {
        $query = "INSERT INTO SessionHasTag (sessionid, tagid) VALUES ";
        foreach ($sessionf["tags"] as $tag) {
            $query .= "($id, $tag),";
        }
        $query = substr($query, 0, -1) . ";"; // drop trailing comma
        if (!mysqli_query_with_error_handling($query)) {
            return false;
        }
    }
    return true;
}

// Function get_next_session_id()
// Reads Session table from db to determine next unused value
// of sessionid.
//
function get_next_session_id() {
    global $linki;
    $result = mysqli_query_with_error_handling("SELECT MAX(sessionid) FROM Sessions;");
    if (!$result) {
        return "";
    }
    list($maxid) = mysqli_fetch_array($result, MYSQLI_NUM);
    mysqli_free_result($result);
    if (!$maxid) {
        return "1";
    }
    return $maxid + 1;
}

// Function insert_session()
// Takes data from global $session array and creates new rows in
// the tables Sessions, SessionHasFeature, and SessionHasService.
//
function insert_session() {
    global $linki;
    $sessionf = filter_session(); // reads global $session array and returns sanitized copy
    $id = $sessionf["id"];

    $query=<<<EOD
INSERT INTO Sessions SET
        trackid="{$sessionf["track"]}",
        typeid="{$sessionf["type"]}",
        divisionid="{$sessionf["divisionid"]}",
        pubstatusid="{$sessionf["pubstatusid"]}",
        languagestatusid="{$sessionf["languagestatusid"]}",
        pubsno="{$sessionf["pubno"]}",
        title="{$sessionf["title"]}",
        secondtitle="{$sessionf["secondtitle"]}",
        pocketprogtext="{$sessionf["pocketprogtext"]}",
        progguiddesc="{$sessionf["progguiddesc"]}",
        progguidhtml="{$sessionf["progguidhtml"]}",
        meetinglink="{$sessionf["mlink"]}",
        persppartinfo="{$sessionf["persppartinfo"]}",
        duration="{$sessionf["duration"]}",
        estatten={$sessionf["estatten"]},
        kidscatid="{$sessionf["kidscatid"]}",
        signupreq={$sessionf["signupreq"]},
        invitedguest={$sessionf["invitedguest"]},
        roomsetid="{$sessionf["roomsetid"]}",
        notesforpart="{$sessionf["notesforpart"]}",
        servicenotes="{$sessionf["servnotes"]}",
        statusid="{$sessionf["status"]}",
        notesforprog="{$sessionf["notesforprog"]}"
EOD;
    if (!mysqli_query_with_error_handling($query)) {
        return false;
    }
    $id = mysqli_insert_id($linki);
    if (!empty($sessionf["features"])) {
        $query = "INSERT INTO SessionHasFeature (sessionid, featureid) VALUES ";
        foreach ($sessionf["features"] as $feature) {
            $query .= "($id, $feature),";
        }
        $query = substr($query, 0, -1) . ";"; // drop trailing comma
        if (!mysqli_query_with_error_handling($query)) {
            return false;
        }
    }
    if (!empty($sessionf["services"])) {
        $query = "INSERT INTO SessionHasService (sessionid, serviceid) VALUES ";
        foreach ($sessionf["services"] as $service) {
            $query .= "($id, $service),";
        }
        $query = substr($query, 0, -1) . ";"; // drop trailing comma
        if (!mysqli_query_with_error_handling($query)) {
            return false;
        }
    }
    if (!empty($sessionf["tags"])) {
        $query = "INSERT INTO SessionHasTag (sessionid, tagid) VALUES ";
        foreach ($sessionf["tags"] as $tag) {
            $query .= "($id, $tag),";
        }
        $query = substr($query, 0, -1) . ";"; // drop trailing comma
        if (!mysqli_query_with_error_handling($query)) {
            return false;
        }
    }
    return $id;
}

// Function filter_session()
// Takes data from global $session array returns array with filtered data
//
function filter_session() {
    global $linki, $session;
    $session2 = array();
    $session2["track"] = filter_var($session["track"], FILTER_SANITIZE_NUMBER_INT);
    $session2["type"] = filter_var($session["type"], FILTER_SANITIZE_NUMBER_INT);
    $session2["divisionid"] = filter_var($session["divisionid"], FILTER_SANITIZE_NUMBER_INT);
    $session2["pubstatusid"] = filter_var($session["pubstatusid"], FILTER_SANITIZE_NUMBER_INT);
    $session2["languagestatusid"] = filter_var($session["languagestatusid"], FILTER_SANITIZE_NUMBER_INT);
    $session2["pubno"] = mysqli_real_escape_string($linki, $session["pubno"]);
    $session2["title"] = mysqli_real_escape_string($linki, $session["title"]);
    $session2["secondtitle"] = mysqli_real_escape_string($linki, $session["secondtitle"]);
    $session2["pocketprogtext"] = mysqli_real_escape_string($linki, $session["pocketprogtext"]);
    $session2["progguiddesc"] = mysqli_real_escape_string($linki, $session["progguiddesc"]);
    $session2["progguidhtml"] = mysqli_real_escape_string($linki, $session["progguidhtml"]);
    if (MEETING_LINK === TRUE)
        $session2["mlink"] = mysqli_real_escape_string($linki, $session["mlink"]);
    else
        $session2["mlink"] = "";
    $session2["persppartinfo"] = mysqli_real_escape_string($linki, $session["persppartinfo"]);
    if (DURATION_IN_MINUTES === TRUE) {
        $session2["duration"] = conv_min2hrsmin($session["duration"]);
    } else {
        $session2["duration"] = mysqli_real_escape_string($linki, $session["duration"]);
    }
    $session2["estatten"] = empty($session["atten"]) ? "NULL" : strval(filter_var($session["atten"], FILTER_SANITIZE_NUMBER_INT));
    $session2["kidscatid"] = filter_var($session["kids"], FILTER_SANITIZE_NUMBER_INT);
    $session2["signupreq"] = empty($session["signup"]) ? "0" : "1";
    $session2["invitedguest"] = empty($session["invguest"]) ? "0" : "1";
    $session2["roomsetid"] = filter_var($session["roomset"], FILTER_SANITIZE_NUMBER_INT);
    $session2["pocketprogtext"] = mysqli_real_escape_string($linki, $session["pocketprogtext"]);
    $session2["notesforpart"] = mysqli_real_escape_string($linki, $session["notesforpart"]);
    $session2["servnotes"] = mysqli_real_escape_string($linki, $session["servnotes"]);
    $session2["status"] = filter_var($session["status"], FILTER_SANITIZE_NUMBER_INT);
    $session2["notesforprog"] = mysqli_real_escape_string($linki, $session["notesforprog"]);
    $session2["id"] = filter_var($session["sessionid"], FILTER_SANITIZE_NUMBER_INT);

    if (!empty($session["featdest"])) {
        $session2["features"] = array();
        foreach ($session["featdest"] as $feature) {
            $session2["features"][] = filter_var($feature, FILTER_SANITIZE_NUMBER_INT);
        }
    }
    if (!empty($session["servdest"])) {
        $session2["services"] = array();
        foreach ($session["servdest"] as $service) {
            $session2["services"][] = filter_var($service, FILTER_SANITIZE_NUMBER_INT);
        }
    }
    if (!empty($session["tagdest"])) {
        $session2["tags"] = array();
        foreach ($session["tagdest"] as $tag) {
            $session2["tags"][] = filter_var($tag, FILTER_SANITIZE_NUMBER_INT);
        }
    }

    return $session2;
}

// Function retrieve_session_from_db()
// Reads Sessions, SessionHasFeature, SessionHasService and SessionHasTag tables
// from db and returns array $session or FALSE.
// If necessary, populates global $message_error
//
function retrieve_session_from_db($sessionid) {
    global $message_error;
    $session = array();
    $query = <<<EOD
SELECT
        sessionid, trackid, typeid, divisionid, pubstatusid, languagestatusid, pubsno,
        title, secondtitle, pocketprogtext,
        CASE WHEN ISNULL(progguiddesc) THEN progguidhtml ELSE progguiddesc END AS progguiddesc,
        CASE WHEN ISNULL(progguidhtml) THEN progguiddesc ELSE progguidhtml END AS progguidhtml,
        persppartinfo, duration, estatten, kidscatid, signupreq, roomsetid, notesforpart,
        servicenotes, statusid, notesforprog, warnings, invitedguest, ts, meetinglink
    FROM
        Sessions
    WHERE
        sessionid = $sessionid;
EOD;
    if (!$result = mysqli_query_with_error_handling($query)) {
        $message_error = "Error retrieving record from database. <br />\n$message_error";
        return false;
    }
    $rows = mysqli_num_rows($result);
    if ($rows != 1) {
        $message_error = "Session record with id = $sessionid not found (or error with Session primary key). <br />\n$message_error";
        return false;
    }
    $sessionarray = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $session["sessionid"] = $sessionarray["sessionid"];
    $session["track"] = $sessionarray["trackid"];
    $session["type"] = $sessionarray["typeid"];
    $session["divisionid"] = $sessionarray["divisionid"];
    $session["pubstatusid"] = $sessionarray["pubstatusid"];
    $session["languagestatusid"] = $sessionarray["languagestatusid"];
    $session["pubno"] = $sessionarray["pubsno"];
    $session["title"] = $sessionarray["title"];
    $session["secondtitle"] = $sessionarray["secondtitle"];
    $session["pocketprogtext"] = $sessionarray["pocketprogtext"];
    $session["progguiddesc"] = $sessionarray["progguiddesc"];
    $session["progguidhtml"] = $sessionarray["progguidhtml"];
    $session["persppartinfo"] = $sessionarray["persppartinfo"];
    $timearray = parse_mysql_time_hours($sessionarray["duration"]);
    if (DURATION_IN_MINUTES === TRUE) {
        $session["duration"] = " " . strval(60 * $timearray["hours"] + $timearray["minutes"]);
    } else {
        $session["duration"] = " " . $timearray["hours"] . ":" . sprintf("%02d", $timearray["minutes"]);
    }
    $session["atten"] = $sessionarray["estatten"];
    $session["kids"] = $sessionarray["kidscatid"];
    $session["signup"] = $sessionarray["signupreq"];
    $session["roomset"] = $sessionarray["roomsetid"];
    $session["notesforpart"] = $sessionarray["notesforpart"];
    $session["servnotes"] = $sessionarray["servicenotes"];
    $session["status"] = $sessionarray["statusid"];
    $session["notesforprog"] = $sessionarray["notesforprog"];
    $session["invguest"] = $sessionarray["invitedguest"];
    $session["mlink"] = $sessionarray["meetinglink"];
    mysqli_free_result($result);
    $query = "SELECT featureid FROM SessionHasFeature WHERE sessionid = $sessionid;";
    if (!$result = mysqli_query_with_error_handling($query)) {
        $message_error = "Error retrieving record from database. <br />\n$message_error";
        return false;
    }
    $session["featdest"] = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $session["featdest"][] = $row[0];
    }
    mysqli_free_result($result);
    $query = "SELECT serviceid FROM SessionHasService WHERE sessionid = $sessionid;";
    if (!$result = mysqli_query_with_error_handling($query)) {
        $message_error = "Error retrieving record from database. <br />\n$message_error";
        return false;
    }
    $session["servdest"] = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $session["servdest"][] = $row[0];
    }
    mysqli_free_result($result);
    $query = "SELECT tagid FROM SessionHasTag WHERE sessionid = $sessionid;";
    if (!$result = mysqli_query_with_error_handling($query)) {
        $message_error = "Error retrieving record from database. <br />\n$message_error";
        return false;
    }
    $session["tagdest"] = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $session["tagdest"][] = $row[0];
    }
    mysqli_free_result($result);
    return $session;
}

// Function isLoggedIn()
// Reads the session variables and checks password in db to see if user is
// logged in.  Returns true if logged in or false if not.  Assumes db already
// connected on $linki.

/* The script will check login status.  If user is logged in
   it will pass control to script (???) to implement edit my contact info.
   If user not logged in, it will pass control to script (???) to
   log user in. */
/* check login script, included in db_connect.php. */

function isLoggedIn() {
    global $message_error;
    if (!isset($_SESSION['badgeid']) || !isset($_SESSION['hashedPassword'])) {
        return false;
    }

    $query = "SELECT password FROM Participants WHERE badgeid = '{$_SESSION['badgeid']}';";
    if (!$result = mysqli_query_with_error_handling($query)) {
        unset($_SESSION['badgeid']);
        unset($_SESSION['hashedPassword']);
        // kill incorrect session variables.
        return ""; //falsy
    }

    if (mysqli_num_rows($result) != 1) {
        unset($_SESSION['badgeid']);
        unset($_SESSION['hashedPassword']);
        // kill incorrect session variables.
        $message_error = "Incorrect number of rows returned when fetching password from db. $message_error";
        return ""; //falsy
    }

    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    mysqli_free_result($result);

    $db_pass = $row[0];

    if (!hash_equals($_SESSION['hashedPassword'], $db_pass)) {
    // kill incorrect session variables.
        unset($_SESSION['badgeid']);
        unset($_SESSION['hashedPassword']);
        $message2 = "Incorrect userid or password.";
        return false;
    } else {
        return true;
    }
}

// Function retrieveParticipant()
// Reads Participants tables
// from db and returns array $participant.
//
function retrieveParticipant($badgeid) {
    global $message_error;
    if (empty($message_error)) {
        $message_error = "";
    }
    $query = <<<EOD
SELECT
        pubsname, password, bestway, interested, bio, share_email
    FROM
        Participants
    WHERE
        badgeid='$badgeid';
EOD;
    if (!$result = mysqli_query_with_error_handling($query)) {
        return false;
    }
    $rows = mysqli_num_rows($result);
    if ($rows != 1) {
        $message_error = "Participant rows retrieved: $rows $message_error";
        return false;
    }
    $participant_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    return $participant_array;
}

// Function retrieveFullParticipant()
// Reads CongoDump table from db and calls retrieveParticipant()
// to return combined results
function retrieveFullParticipant($badgeid) {
    global $message_error;
    if (empty($message_error)) {
        $message_error = "";
    }
    $query = <<<EOD
SELECT
        badgeid,
        firstname,
        lastname,
        badgename,
        phone,
        email,
        postaddress1,
        postaddress2,
        postcity,
        poststate,
        postzip,
        postcountry
    FROM
        CongoDump
    WHERE
        badgeid = '$badgeid';
EOD;
    if (!$result = mysqli_query_with_error_handling($query)) {
        return false;
    };
    $rows = mysqli_num_rows($result);
    if ($rows != 1) {
        $message_error = "$rows rows returned for badgeid: $badgeid when 1 expected. $message_error";
        return false;
    };
    if (!$participant_array = retrieveParticipant($badgeid)) {
        return false;
    };
    if (empty(DEFAULT_USER_PASSWORD)) {
        $participant_array["chpw"] = false;
    } else {
        $participant_array["chpw"] = password_verify(DEFAULT_USER_PASSWORD, $participant_array["password"]);
    }
    $participant_array["password"] = "";
    $participant_array = array_merge($participant_array, mysqli_fetch_array($result, MYSQLI_ASSOC));
    mysqli_free_result($result);
    return $participant_array;
}

// Function retrieve_participantAvailability_from_db()
// Reads ParticipantAvailability and ParticipantAvailabilityTimes tables
// from db to populate global array $partAvail.
// Returns $participantAvailability array or false if error
//
function retrieve_participantAvailability_from_db($badgeid, $exit_on_error = false) {
    global $message_error;
    $query = <<<EOD
SELECT
        badgeid,
        maxprog,
        preventconflict,
        otherconstraints,
        numkidsfasttrack
    FROM
        ParticipantAvailability
    WHERE
        badgeid = "$badgeid";
EOD;
    if (!$result = mysqli_query_with_error_handling($query, $exit_on_error)) {
        return false;
    }
    $rows = mysqli_num_rows($result);
    if ($rows > 1) {
        $message_error = "Found $rows rows for participant with badgeid:$badgeid.  Expected 1.";
        $message_error = log_mysqli_error($query, $message_error);
        if ($exit_on_error) {
            RenderError($message_error); // will exit script
        }
        return false;
    }
    if ($rows == 1) {
        $participantAvailability = mysqli_fetch_array($result, MYSQLI_ASSOC);
    } else {
        $participantAvailability = array('badgeid' => $badgeid, 'maxprog' => '', 'preventconflict' => '',
            'otherconstraints' => '', 'numkidsfasttrack'=>'');
    }
    mysqli_free_result($result);

    if (CON_NUM_DAYS > 1) {
        $query = "SELECT badgeid, day, maxprog FROM ParticipantAvailabilityDays where badgeid=\"$badgeid\";";
        if (!$result = mysqli_query_with_error_handling($query, $exit_on_error)) {
            return false;
        }
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $i = $row["day"];
                $participantAvailability["maxprogday$i"] = $row["maxprog"];
            }
        }
        mysqli_free_result($result);
    }
    $query = <<<EOD
SELECT
        badgeid,
        availabilitynum,
        TIME_FORMAT(starttime, '%T') AS starttime,
        TIME_FORMAT(endtime, '%T') AS endtime
    FROM
        ParticipantAvailabilityTimes
	WHERE
	    badgeid="$badgeid"
    ORDER BY
        starttime;
EOD;
    if (!$result = mysqli_query_with_error_handling($query, $exit_on_error)) {
        return false;
    }
    $i = 1;
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $participantAvailability["starttimestamp_$i"] = $row["starttime"];
        $participantAvailability["endtimestamp_$i"] = $row["endtime"];
        $i++;
    }
    return $participantAvailability;
}

// Function set_permission_set($badgeid)
// Performs complicated join to get the set of permission atoms available to the user
// Stores them in global variable $permission_set
//
function set_permission_set($badgeid) {
    global $message_error;
    $_SESSION['permission_set'] = array();
// First do simple permissions
    $query = <<<EOD
SELECT DISTINCT
        permatomtag
    FROM
                  PermissionAtoms PA
             JOIN Permissions P USING (permatomid)
        LEFT JOIN Phases PH ON P.phaseid = PH.phaseid AND PH.current = TRUE
        LEFT JOIN UserHasPermissionRole UHPR ON P.permroleid = UHPR.permroleid AND UHPR.badgeid='$badgeid'
    WHERE
            (PH.phaseid IS NOT NULL OR P.phaseid IS NULL)
        AND (UHPR.badgeid IS NOT NULL OR P.badgeid='$badgeid');
EOD;
    if (!$result = mysqli_query_with_error_handling($query, true)) {
        return false;
    }
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $_SESSION['permission_set'][] = $row[0];
    }
    mysqli_free_result($result);
// Second, do <<specific>> permissions
//    $_SESSION['permission_set_specific'] = array();
//    $query = <<<EOD
//SELECT DISTINCT
//        permatomtag, elementid
//    FROM
//        PermissionAtoms PA
//        JOIN Permissions P USING(permatomid),
//        Phases PH,
//        PermissionRoles PR,
//        UserHasPermissionRole UHPR
//    WHERE
//            (   (UHPR.badgeid='$badgeid' AND UHPR.permroleid = P.permroleid)
//              OR P.badgeid='$badgeid' )
//        AND
//            (P.phaseid IS NULL
//            OR (P.phaseid = PH.phaseid AND PH.current = TRUE))
//        AND
//            PA.elementid IS NOT NULL;
//EOD;
//    if (!$result = mysqli_query_with_error_handling($query, true)) {
//        return false;
//    }
//    $rows = mysqli_num_rows($result);
//    if ($rows == 0) {
//        mysqli_free_result($result);
//        return true;
//    };
//    for ($i = 0; $i < $rows; $i++) {
//        $_SESSION['permission_set_specific'][] = mysqli_fetch_array($result, MYSQLI_ASSOC);
//    };
//    mysqli_free_result($result);
    return true;
}

//function get_idlist_from_db($table_name, $id_col_name, $desc_col_name, $desc_col_match);
// Returns a string with a list of id's from a configuration table

function get_idlist_from_db($table_name, $id_col_name, $desc_col_name, $desc_col_match) {
    $query = "SELECT GROUP_CONCAT($id_col_name) from $table_name where ";
    $query.= "$desc_col_name in ($desc_col_match)";
    $result = mysqli_query_with_error_handling($query);
    $retval = mysqli_fetch_row($result)[0];
    mysqli_free_result($result);
    return $retval;
}

// Function get_sstatus()
// Populates the global sstatus array from the database

function get_sstatus() {
    $sstatus = array();
    $query = "SELECT statusid, may_be_scheduled, validate FROM SessionStatuses;";
    $result = mysqli_query_exit_on_error($query);
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $statusid = $row['statusid'];
        $may_be_scheduled = $row['may_be_scheduled'] == 1;
        $validate = $row['validate'] == 1;
        $sstatus[$statusid] = array('may_be_scheduled' => $may_be_scheduled, 'validate' => $validate);
    }
    return $sstatus;
}

function survey_programmed() {
    $query = "SELECT COUNT(*) questions FROM SurveyQuestionConfig";
    $result = mysqli_query_exit_on_error($query);
    $questions = mysqli_fetch_row($result)[0];
    if (isset($questions))
           return $questions > 0;
    return false;
}

function my_escape_string($str_to_esc) {
    global $mysqli;

    return $mysqli->real_escape_string($str_to_esc);
}
?>
