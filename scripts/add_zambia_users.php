<?php
// Fields parsed
//
// Participants
//      badgeid
//      password*
// CongoDump
//      badgeid
//      firstname
//      lastname
//      badgename
//      phone
//      email
//      postaddress1
//      postaddress2
//      postcity
//      poststate
//      postzip
//      postcountry
//      regtype
// UserHasPermissionRole
//      badgeid
//      permroleids
global $linki;
if (!file_exists('../db_name.php')) {
    $path = realpath('../');
    echo "File with db credentials not found: $path/db_name.php \n";
    exit(-1);
}
require ('../webpages/db_functions.php');
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
    echo "usage: php -f add_zambia_users.php file_to_parse\n";
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
$userRawArr = (new parseCSV($contents)) -> data;
error_reporting (E_ERROR || E_WARNING);
$lineNum = 1;
$successes = 0;
$CongoDumpFields = array_flip(array('firstname', 'lastname', 'badgename', 'phone', 'email', 'postaddress1',
    'postaddress2', 'postcity', 'poststate', 'postzip', 'postcountry', 'regtype'));
foreach ($userRawArr as $row) {
    $lineNum++;
    if (empty($row['badgeid'])) {
        echo "badgeid field required.  Missing on line $lineNum\n";
        continue;
    }
    $badgeid = trim($row['badgeid']);
    if (empty($row['password'])) {
        echo "password field required.  Missing on line $lineNum\n";
        continue;
    }
    $passwordHash = password_hash(trim($row['password']), PASSWORD_DEFAULT);
    $query = <<<EOD
INSERT into Participants (badgeid, password) VALUES ('$badgeid', '$passwordHash');
EOD;
    $result = mysqli_query($linki, $query);
    if (!$result) {
        echo "Failure inserting into Participants.\n";
        echo $query . '\n';
        exit(-1);
    }
    $query = <<<EOD
INSERT into CongoDump (badgeid) VALUES ('{$row['badgeid']}');
EOD;
    $result = mysqli_query($linki, $query);
    if (!$result) {
        echo "Failure inserting into CongoDump.\n";
        echo $query . '\n';
        exit(-1);
    }
    $CDFieldsFiltered = array_filter(array_intersect_key($row, $CongoDumpFields),
        function($item) { return !empty($item); });
    $CDSetStmntsArr = array_map(
        function($fieldName, $value) {
            $tvalue = trim($value);
            return ("$fieldName = '$tvalue'");
        }
        , array_keys($CDFieldsFiltered), $CDFieldsFiltered);
    $CDSetStmnts = implode(', ', $CDSetStmntsArr);
    $query = "UPDATE CongoDump SET $CDSetStmnts WHERE badgeid = '$badgeid';";
    $result = mysqli_query($linki, $query);
    if (!$result) {
        echo "Failure inserting into CongoDump.\n";
        echo $query . '\n';
        exit(-1);
    }
    if (!empty($row['permroleids'])) {
        $permRoleIdsArr = explode(',', $row['permroleids']);
        $permRoleStmntsArr = array_map(
            function($permRoleId) use ($badgeid) {
                $tpermRoleId = trim($permRoleId);
                return "('$badgeid', '$tpermRoleId')";
            }
            ,$permRoleIdsArr);
        $permRoleStmnts = implode(', ', $permRoleStmntsArr);
        $query = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES $permRoleStmnts;";
        $result = mysqli_query($linki, $query);
        if (!$result) {
            echo "Failure inserting into UserHasPermissionRoles.\n";
            echo $query . '\n';
            exit(-1);
        }
    }
    $successes++;
}
echo "Successfully entered $successes users/participants.\n";


