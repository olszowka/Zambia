<?php
global $linki;
if (!file_exists('../db_name.php')) {
    $path = realpath('../');
    echo "File with db credentials not found: $path/db_name.php \n";
    exit(-1);
}
require ('../webpages/db_functions.php');
require ('../webpages/data_functions.php');
if (!prepare_db_and_more()) {
    echo "Could not connect to db.\n";
    exit(-1);
}
require ('../webpages/external/parseCSV0.4.3beta/parsecsv.lib.php');
if (!isset($argv) || !is_array($argv)) {
    echo "PHP is not configured to parse command line parameters.\n";
    exit(-1);
}
if (empty($argv[1])) {
    echo "usage: php -f import_sessions.php sessions_file.csv\n";
    exit(-1);
}
$filename = $argv[1];
$path = realpath('./');
if (!file_exists($filename)) {
    echo "File not found: $path/$filename\n";
    exit(-1);
}
if (!$filehandle = fopen($filename, 'r')) {
    echo "Error opening file: $path/$filename\n";
    exit(-1);
}
$contents = fread($filehandle, filesize($filename));
fclose($filehandle);
error_reporting (E_ERROR);
$SessionRawArr = (new parseCSV($contents)) -> data;
error_reporting (E_ERROR || E_WARNING);
$lineNum = 1;
$successes = 0;
$sessionFields = array('trackid','typeid','divisionid','pubstatusid','languagestatusid','title','progguiddesc','persppartinfo','duration','estatten','kidscatid','signupreq',
    'roomsetid','notesforpart','servicenotes','statusid','notesforprog','invitedguest');
$RequiredFields = array();
foreach ($sessionFields as $sfield) {
    $RequiredFields[$sfield] =  ($sfield != 'persppartinfo' && $sfield != 'notesforpart' && $sfield != 'servicenotes' && $sfield != 'notesforprog');
}
$sessionInsQuery = 'INSERT INTO Sessions(' . implode(',', $sessionFields) . ') VALUES(' . str_repeat('?,', sizeof($sessionFields) -1) . '?);';
$sessionInsParamsType = str_repeat('s', sizeof($sessionFields));
$sessionTagsQuery = 'INSERT INTO SessionHasTag(sessionid, tagid) VALUES(?, ?);';
$sessionTagParamsType = 'ii';
//echo "$sessionInsQuery\n";
//echo "$sessionInsParamsType\n";
//
// load mappings
//
// tags
$taglist = array();
$sql = 'SELECT tagid, lower(tagname) tagname from Tags;';
$result = mysqli_query_exit_on_error($sql);
while ($row = mysqli_fetch_assoc($result)) {
    $taglist[$row['tagname']] = $row['tagid'];
}
mysqli_free_result($result);
//
// types
//
$typelist = array();
$sql = 'SELECT typeid, lower(typename) typename from Types;';
$result = mysqli_query_exit_on_error($sql);
while ($row = mysqli_fetch_assoc($result)) {
    $typelist[$row['typename']] = $row['typeid'];
}
mysqli_free_result($result);
//
// Divisions
//
$divisionlist = array();
$sql = 'SELECT divisionid, lower(divisionname) divisionname from Divisions;';
$result = mysqli_query_exit_on_error($sql);
while ($row = mysqli_fetch_assoc($result)) {
    $divisionlist[$row['divisionname']] = $row['divisionid'];
}
mysqli_free_result($result);
//
// RoomSets
//
$roomsetlist = array();
$sql = 'SELECT roomsetid, lower(roomsetname) roomsetname from RoomSets;';
$result = mysqli_query_exit_on_error($sql);
while ($row = mysqli_fetch_assoc($result)) {
    $roomsetlist[$row['roomsetname']] = $row['roomsetid'];
}
mysqli_free_result($result);
//
// all loaded
//

foreach ($SessionRawArr as $datarow) {
    //error_log("initial row:");
    //var_error_log($datarow);
    $lineNum++;
    $procline = true;
    $insArgs = array();
    foreach ($sessionFields as $sfield) {
        if ($RequiredFields[$sfield]) {
            if ($datarow[$sfield] === null || $datarow[$sfield] == '') {
                echo "$sfield required.  Missing on line $lineNum\n";
                $procline = false;
            }
        }
    }
    # Do mappings on types, divisions, roomsets
    $match = strtolower(trim($datarow['typeid']));
    if ($typelist[$match] !== null)
        $datarow['typeid'] = $typelist[$match];
    else {
        echo "No matching type for typeid " . $datarow['typeid'] . " on line $lineNum\n";
        $procline = false;
    }
    $match = strtolower(trim($datarow['divisionid']));
    if ($divisionlist[$match] !== null)
        $datarow['divisionid'] = $divisionlist[$match];
    else {
        echo "No matching division for divisionid " . $datarow['divisionid'] . " on line $lineNum\n";
        $procline = false;
    }
    $match = strtolower(trim($datarow['roomsetid']));
    if ($roomsetlist[$match] !== null)
        $datarow['roomsetid'] = $roomsetlist[$match];
    else {
        echo "No matching roomset for roomsetid " . $datarow['roomsetid'] . " on line $lineNum\n";
        $procline = false;
    }
    //
    // now do the insert into sessions
    //
    //error_log("updated row:");
    //var_error_log($datarow);
    if ($procline) {
        foreach ($sessionFields as $sfield) {
            if ($datarow[$sfield] === null || $datarow[$sfield] == '')
                $insArgs[] = null;
            else
                $insArgs[] = trim($datarow[$sfield]);
        }
        //error_log("Session insert row " . $lineNum);
        //var_error_log($sessionInsQuery);
        //var_error_log($sessionInsParamsType);
        //var_error_log($insArgs);
        $rows = mysql_cmd_with_prepare($sessionInsQuery, $sessionInsParamsType, $insArgs);
        if ($rows !== 1) {
            echo "Error inserting line $lineNum: " . mysqli_error($linki) . "\n";
        } else {
            $sessionid = mysqli_insert_id($linki);
     //
     //add all the tags
     //
     //first area tags

            $tags = explode(',', $datarow['areatag']);
            foreach ($tags as $tag) {
                $match = strtolower(trim($tag));
                $tagid = $taglist[$match];
                if ($tagid === null)
                    echo "invalid area tag $tag on line $lineNum\n";
                else {
                    $rows = mysql_cmd_with_prepare($sessionTagsQuery, $sessionTagParamsType, array($sessionid, $tagid));
                    if ($rows !== 1) {
                        echo "Error inserting area tag $tag ($tagid) for line $lineNum: " . mysqli_error($linki) . "\n";
                    }
                }
            }
    //
    // now topic tags
    //
            $tags = explode(',', $datarow['topictag']);
            foreach ($tags as $tag) {
                $match = strtolower(trim($tag));
                $tagid = $taglist[$match];
                if ($tagid === null)
                    echo "invalid topic tag $tag on line $lineNum\n";
                else {
                    $rows = mysql_cmd_with_prepare($sessionTagsQuery, $sessionTagParamsType, array($sessionid, $tagid));
                    if ($rows !== 1) {
                        echo "Error inserting topic tag $tag ($tagid) for line $lineNum: " . mysqli_error($linki) . "\n";
                    }
                }
            }
            $successes++;
        }
    }
}
echo "Successfully inserted $successes sessions.\n";
?>
