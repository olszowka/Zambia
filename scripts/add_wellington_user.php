<?php
// Fields required parsed
//	email
//	firstname
//	lastname
//	permroleids
// 
// optional fields parsed
// 	badgename (defaults to 'firstname lastname'
// 	address1
// 	address2
// 	city
// 	state
// 	zip
// 	country
//
// Creates
// 	Wellington
// 		user
// 		reservation
// 		claim
// 		order
// 		dc_contact
// 	Zambia
// 		Participants
// 			badgeid, invalid password
// 		CongoDump
// 			badgeid
// 			firstname
// 			lastname
// 			badgename
// 			email
// 			regtype
// 		UserHasPermissionRoles
// 			badgeid
// 			permroleids
// 
// Expects reg sync to pick up other wellington data to Zambia
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
$pgconn = pg_connect(WELLINGTONPROD);
if (!$pgconn) {
    echo "Unable to connect to Wellington";
    exit();
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

// get membership id of nonmember:
$pgquery = <<<EOD
SELECT id FROM public.memberships WHERE name = 'non_member'
EOD;
//echo "pg query=" . $pgquery . "\n\n";

$result = pg_query($pgconn, $pgquery);
if (!$result) {
    echo "Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING);
    exit();
}

$nonmemberid = -1;
while ($row = pg_fetch_assoc($result)) {
    $nonmemberid = $row["id"];
}
pg_free_result($result);
if ($nonmemberid <= 0) {
	echo "Wellington setup error: membershiptype non_member does not exist in memberships\n\n";
	exit();
}

//echo "non_memberid = " . $nonmemberid . "\n\n";

$contents = fread($filehandle, filesize($filename));
fclose($filehandle);
error_reporting (E_ERROR);
$userRawArr = (new parseCSV($contents)) -> data;
error_reporting (E_ERROR || E_WARNING);
$lineNum = 1;
$successes = 0;
//$CongoDumpFields = array_flip(array('firstname', 'lastname', 'badgename', 'phone', 'email', 'postaddress1',
//    'postaddress2', 'postcity', 'poststate', 'postzip', 'postcountry', 'regtype'));
foreach ($userRawArr as $row) {
    $lineNum++;
// required fields
//	email
    if (empty($row['email'])) {
        echo "email field required.  Missing on line $lineNum\n";
        continue;
    }
    $email = trim($row['email']);
//	firstname
    if (empty($row['firstname'])) {
        echo "firstname field required.  Missing on line $lineNum\n";
        continue;
    }
    $firstname = trim($row['firstname']);
//	lastname
    if (empty($row['lastname'])) {
        echo "lastname field required.  Missing on line $lineNum\n";
        continue;
    }
    $lastname = trim($row['lastname']);
//	permroleids
    if (empty($row['permroleids'])) {
        echo "permroleids field required.  Missing on line $lineNum\n";
        continue;
    }
    $permroleids = trim($row['permroleids']);

// optional fields parset
// 	badgename (defaults to 'firstname lastname'
    if (array_key_exists('badgename', $row))
	    $badgename = trim($row['badgename']);
    else
        $badgename = '';
    if ($badgename == '')
	$badgename = trim($firstname . ' ' . $lastname);

// 	address1
    if (array_key_exists('address1', $row))
	$address1 = trim($row['address1']);
    else
	$address1 = '';
// 	address2
    if (array_key_exists('address2', $row))
	$address2 = trim($row['address2']);
    else
	$address2 = '';
// 	city
    if (array_key_exists('city', $row))
	$city = trim($row['city']);
    else
	$city = '';
// 	state
    if (array_key_exists('state', $row))
	$state = trim($row['state']);
    else
	$state = '';
// 	zip
    if (array_key_exists('zip', $row))
	$zip = trim($row['zip']);
    else
	$zip = '';
// 	country
    if (array_key_exists('country', $row))
	$country = trim($row['country']);
    else
	$country = '';

// now the users table insert
    $pginsert = 'INSERT INTO public.users(email, created_at, updated_at, sign_in_count, hugo_download_counter) values($1, now(), now(), 0, 0) RETURNING id;';
    $result = pg_prepare($pgconn, "users", $pginsert);
    //echo "user prepare from $pginsert='" . $result . "'\n\n";
    pg_free_result($result);

// execute the insert statement
    $result = pg_execute($pgconn, "users", array($email));
    //echo "user execute from $pginsert='" . $result . "'\n\n";
    $userid = -1;
    while ($row = pg_fetch_assoc($result)) {
	$userid = $row["id"];
    }
    if ($userid <= 0) {
	echo "error inserting user " . $email . ", skipping row " . $lineNum . "\n";
	continue;
    }
    echo "User id " . $userid . " added\n\n";
    pg_free_result($result);

// now for the reservation, which sets the membership number (badgeid)
    $pginsert = <<<EOD
INSERT INTO public.reservations(created_at, updated_at, state, membership_number)
SELECT now(), now(), 'paid', max(membership_number)+1
FROM public.reservations
RETURNING id, membership_number
EOD;
    $result = pg_query($pgconn, $pginsert);
    if (!$result) {
	echo "Wellington error inserting reservation for " . $lineNum . ": " . pg_result_error($result, PGSQL_STATUS_STRING);
	exit();
    }

    $membership_number = -1;
    $reservationid = -1;
    while ($row = pg_fetch_assoc($result)) {
	$reservationid = $row["id"];
	$membership_number = $row["membership_number"];
    }
    if ($membership_number <= 0) {
    	echo "Error inserting reservation, no membership number (badgeid) returned for " . $lineNum . "\n\n";
	exit();
    }
    echo "$userid added as badgeid $membership_number\n\n";
    pg_free_result($result);

// now claim
    $pginsert = <<<EOD
INSERT INTO public.claims(user_id, reservation_id, created_at, updated_at, active_from)
VALUES ($1, $2, now(), now(), now())
RETURNING id
EOD;
    $result = pg_prepare($pgconn, "claims", $pginsert);
    //echo "claims prepare from $pginsert='" . $result . "'\n\n";

// execute the insert statement
    $result = pg_execute($pgconn, "claims", array($userid, $reservationid));
    //echo "claims execute from $pginsert='" . $result . "'\n\n";
    $claimid = -1;
    while ($row = pg_fetch_assoc($result)) {
	$claimid = $row["id"];
    }
    if ($claimid <= 0) {
	echo "error inserting claim for " . $lineNum . "\n\n";
	exit();
    }
    pg_free_result($result);

// Now for order
    $pginsert = <<<EOD
INSERT INTO public.orders(reservation_id, membership_id,  created_at, updated_at, active_from)
VALUES ($1, $2, now(), now(), now())
RETURNING id
EOD;
    $result = pg_prepare($pgconn, "orders", $pginsert);
    //echo "orders prepare from $pginsert='" . $result . "'\n\n";
    pg_free_result($result);

// execute the insert statement
    $result = pg_execute($pgconn, "orders", array($reservationid, $nonmemberid));
    //echo "orders execute from $pginsert='" . $result . "'\n\n";
    $orderid = -1;
    while ($row = pg_fetch_assoc($result)) {
	$orderid = $row["id"];
    }
    if ($orderid <= 0) {
	echo "error inserting order for " . $lineNum . "\n\n";
	exit();
    }
    pg_free_result($result);

// now for contact (dc_contact)
    $pginsert = "INSERT INTO public.dc_contacts(claim_id, first_name, last_name, preferred_first_name, preferred_last_name, badge_title, publication_format, created_at, updated_at";
    $args = array($claimid, $firstname, $lastname, $firstname, $lastname, $badgename);
    if ($address1 <> '') 
    	$pginsert .= ",address_line_1";
    if ($address2 <> '')
    	$pginsert .= ",address_line_2";
    if ($city <> '')
    	$pginsert .= ",city";
    if ($country <> '')
    	$pginsert .= ",country";
    if ($zip <> '')
    	$pginsert .= ",postal";
    if ($state <> '')
    	$pginsert .= ",province";
    $pginsert .= ")\nVALUES ($1, $2, $3, $4, $5, $6, 'send_me_email', now(), now()";
    $index = 6;
    if ($address1 <> '') {
    	$index++;
	$args[] = $address1;
    	$pginsert .= ",$" . $index;
    }
    if ($address2 <> '') {
    	$index++;
	$args[] = $address2;
    	$pginsert .= ",$" . $index;
    }
    if ($city <> '') {
    	$index++;
	$args[] = $city;
    	$pginsert .= ",$" . $index;
    }
    if ($country <> '') {
    	$index++;
	$args[] = $country;
    	$pginsert .= ",$" . $index;
    }
    if ($zip <> '') {
    	$index++;
	$args[] = $zip;
    	$pginsert .= ",$" . $index;
    }
    if ($state <> '') {
    	$index++;
	$args[] = $state;
    	$pginsert .= ",$" . $index;
    }
    $pginsert .= ") returning id;";
    //echo "dc_contacts insert = '" . $pginsert . "'\n\n";
    //echo "args array = ";
    //var_error_log($args);
    $result = pg_prepare($pgconn, "dc_contacts", $pginsert);
    //echo "dc_contacts prepare from $pginsert='" . $result . "'\n\n";
    pg_free_result($result);

// execute the insert statement
    $result = pg_execute($pgconn, "dc_contacts", $args);
    //echo "dc_contacts execute from $pginsert='" . $result . "'\n\n";
    $contactid = -1;
    while ($row = pg_fetch_assoc($result)) {
	$contactid = $row["id"];
    }
    if ($contactid <= 0) {
	echo "error inserting contact for " . $lineNum . "\n\n";
	exit();
    }
    pg_free_result($result);

// now for Zambia
    $query = <<<EOD
INSERT into Participants (badgeid, password) VALUES ('$membership_number', 'invalid');
EOD;
    $result = mysqli_query($linki, $query);
    if (!$result) {
        echo "Failure inserting into Participants.\n";
        echo $query . '\n';
        exit(-1);
    }
    $query = <<<EOD
INSERT into CongoDump (badgeid) VALUES ('$membership_number');
EOD;
    $result = mysqli_query($linki, $query);
    if (!$result) {
        echo "Failure inserting into CongoDump.\n";
        echo $query . '\n';
        exit(-1);
    }
    if ($permroleids != '') {
        $permRoleIdsArr = explode(',', $permroleids);
        $permRoleStmntsArr = array_map(
            function($permRoleId) use ($membership_number) {
                $tpermRoleId = trim($permRoleId);
                return "('$membership_number', '$tpermRoleId')";
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


