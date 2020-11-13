<?php
/**
 * This file is a temporary shim to enable MySQL 5.x code to run under MySQL 7.x, which has no mysql functions.
 * It is not complete; it only implements what Zambia needs.
 */
define('MYSQL_ASSOC', MYSQLI_ASSOC);
define('MYSQL_NUM',   MYSQLI_NUM);
define('MYSQL_BOTH',  MYSQLI_BOTH);

function mysql_affected_rows($link_identifier) { return mysqli_affected_rows($link_identifier); }

function mysql_connect($host, $userid, $password) { return mysqli_connect($host, $userid, $password); }

function mysql_errno($link) { return mysqli_errno($link); }

function mysql_error($link) { return mysqli_error($link); }

function mysql_fetch_array($result, $result_type) { return mysqli_fetch_array($result, $result_type); }

function mysql_fetch_assoc($result) { return mysqli_fetch_assoc($result); }

function mysql_fetch_object($result) { return mysqli_fetch_object($result); }

function mysql_field_name($result, $i) {
    $obj = mysqli_fetch_field_direct($result, $i);
    return $obj? $obj->name : $obj;
}

/**
 * Not exact - will return empty string on failure, but mysql_info returns false.
 */
function mysql_info($link) { return mysqli_info($link); }

function mysql_insert_id($link) { return mysqli_insert_id($link); }

function mysql_num_fields($result) { return mysqli_num_fields($result); }

function mysql_num_rows($result) { return mysqli_num_rows($result); }

function mysql_query($query, $link) { return mysqli_query($link, $query); }

# mysql_query_XML - defined inside Zambia
# mysql_query_exit_on_error - defined inside Zambia
# mysql_query_with_error_handling - defined inside Zambia

/**
 * mysql_real_escape_string does not require the link, but mysqli does.
 */
function mysql_real_escape_string($unescaped_string, $link_identifier = null) {
    global $link;
    if ($link_identifier == null) {
        $link_identifier = $link;
    }
    return mysqli_real_escape_string($link_identifier, $unescaped_string);
}

/**
 * This one is complicated, since mysql_result can take a column name
 * but mysqli has no direct equivalent.
 *
 * @param mysqli_result $result
 * @param int           $row    - row number
 * @param mixed         $field  - optional: field number or name
 *
 * @return string
 */
function mysql_result($result, int $row, $field = 0) {
    if (!mysqli_data_seek($result, $row)) {
        syslog(LOG_ERR, "Could not seek to row $row");
        return null;
    }
    if (!is_numeric($field)) {
        $data = mysqli_fetch_assoc($result);
        // Not sure whether assoc. array is upper or lower case
        $value = $data[$field]?? $data[strtoupper($field)];
    } else {
        $data = mysqli_fetch_array($result);
        return $data[$field];
    }
}

function mysql_select_db($dbname, $link) { return mysqli_select_db($link, $dbname); }

function mysql_set_charset($charset, $link) { return mysqli_set_charset($link, $charset); }