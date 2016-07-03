<?php
// One needs to configure and install a pair of keys to ssh into cm db host machine to use this script
define("log_file_with_path", "/Users/peterolszowka/czi.log");
define("error_file_with_path", "/Users/peterolszowka/czi_error.log");
define("ssh_error_file_with_path", "/Users/peterolszowka/czi_ssh_error.log");
define("cmdb_host", "app1.arisia.org");
define("cmdb_host_username", "petero");
define("cmdb_host_public_key_file", "/Users/peterolszowka/.ssh/arisia_rsa.pub");
define("cmdb_host_private_key_file", "/Users/peterolszowka/.ssh/arisia_rsa");
define("cmdb_tunnel_port", 3307);
define("cmdb_mysql_host_ip", "127.0.0.1");
define("cmdb_mysql_host_port", 3306);
define("cmdb_userid", "root");
define("cmdb_password", "SLC!emens1910");
define("cmdb_name", "convention_master");
define("cm_event_id", "28");
define("zambia_db_host", "127.0.0.1");
define("zambia_old_db_name", "zambia_lunacon_dev");
define("zambia_old_db_user", "zambia_admin");
define("zambia_old_db_password", "zambia");
define("zambia_new_db_name", "zambia_arisia_prod");
define("zambia_new_db_user", "zambia_admin");
define("zambia_new_db_password", "zambia");
define("default_zambia_password_md5_for_login", "4cb9c8a8048fd02294477fcb1a41191a");
define("default_zambia_password_md5_for_not_login", "maynotlogin");
date_default_timezone_set('America/New_York');

$zambia_flags_array = array(
	"Z_Staff" => array(
		"may_log_in" => true,
		"permission_roles" => array("Staff")
	),
	"Z_Participant" => array(
		"may_log_in" => true,
		"permission_roles" => array("Program Participant")
	),
	"Z_Childs_Part" => array(
		"may_log_in" => true,
		"permission_roles" => array("Children's Services Participant")
	),
	"Z_Event" => array(
		"may_log_in" => false,
		"permission_roles" => array("Event Participant")
	),
	"Z_EventsOrg" => array(
		"may_log_in" => false,
		"permission_roles" => array("Event Organizer")
	),
	"Z_Larp" => array(
		"may_log_in" => false,
		"permission_roles" => array("LARP")
	),
	"Z_Larp_Org" => array(
		"may_log_in" => false,
		"permission_roles" => array("LARP Organizer")
	),
	"Z_Tabletop" => array(
		"may_log_in" => false,
		"permission_roles" => array("Tabletop")
	)
);

// **** End of configuration ****

$log_file_handle = fopen(log_file_with_path, "a");
$error_file_handle = fopen(error_file_with_path, "a");
fwrite($log_file_handle , strftime("%F %T")." Begin processing.\n");

$cm_event_id = cm_event_id;
$default_zambia_password_md5_for_not_login = default_zambia_password_md5_for_not_login;

function myquery($connection, $query_string) {
	$result = $connection->query($query_string);
	if ($result === false) {
		fwrite($error_file_handle, strftime("%F %T")." Query failed.\n");
		fwrite($error_file_handle, $query_string."\n");
		fwrite($log_file_handle, " Failed.\n");
		die();
	} else {
		return $result;
	}
}

function array_fetch_for_db($link, $array, $index) {
	if (isset($array[$index])) {
		return mysqli_real_escape_string($link, $array[$index]);
	} else {
		return "";
	}
}

// Walk $zambia_flags_array to
// 1) get list of cm Zambia flags which permit log in
// 2) get list of relevant Zambia permission role names

$zambia_flags_log_in_array = array();
$zambia_roles_array = array();
foreach ($zambia_flags_array as $zambia_flag => $details_array) {
	if ($details_array["may_log_in"] === true) {
		$zambia_flags_log_in_array[] = $zambia_flag;
	}
	$zambia_roles_array = array_merge($zambia_roles_array, $details_array["permission_roles"]);
}
$zambia_roles_array = array_unique($zambia_roles_array);
$zambia_roles_predicate = "\"" . implode("\",\"",$zambia_roles_array) . "\"";

shell_exec("ssh -f -L ".cmdb_tunnel_port.":".cmdb_mysql_host_ip.":".cmdb_mysql_host_port." ".cmdb_host_username."@".cmdb_host." sleep 60 >> ".ssh_error_file_with_path);
$cmdb_connection = mysqli_connect(cmdb_mysql_host_ip, cmdb_userid, cmdb_password, cmdb_name, cmdb_tunnel_port);
if (!$cmdb_connection) {
    fwrite($error_file_handle, strftime("F T")."Failed to connect to cm_db MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . "\n");
	fwrite($log_file_handle, " Failed.\n");
	die();
}
$zambia_old_db_connection = mysqli_connect(zambia_db_host, zambia_old_db_user, zambia_old_db_password, zambia_old_db_name);
if (!$zambia_old_db_connection) {
    fwrite($error_file_handle, strftime("F T")."Failed to connect to zambia_old_db MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . "\n");
	fwrite($log_file_handle, " Failed.\n");
	die();
}
$zambia_new_db_connection = mysqli_connect(zambia_db_host, zambia_new_db_user, zambia_new_db_password, zambia_new_db_name);
if (!$zambia_new_db_connection) {
    fwrite($error_file_handle, strftime("F T")."Failed to connect to zambia_new_db MySQL: (" . mysqli_connect_errno() . ") " . mysqli_connect_error() . "\n");
	fwrite($log_file_handle, " Failed.\n");
	die();
}

// Lookup field_id's for Zambia flags in CM db
$zambia_flag_cm_titles_predicate = "\"" . implode ( "\",\"", array_keys($zambia_flags_array)) . "\"";
$result = myquery($cmdb_connection, "SELECT field_id, title FROM fields_defined WHERE event_id=\"*\" AND title IN ($zambia_flag_cm_titles_predicate)");
$zambia_flag_field_ids_arr = array();
while ($row = $result->fetch_assoc()) {
	$zambia_flags_array[$row["title"]]["cm_field_id"] = $row["field_id"];
	$zambia_flag_field_ids_arr[] = $row["field_id"];
}
mysqli_free_result($result);
$zambia_flag_field_ids_predicate = "\"" . implode("\",\"",$zambia_flag_field_ids_arr) . "\"";

// Lookup badgeids and flag field ids for registrants in CM db with one or more Zambia flags set to "Y"
$query = <<<EOD
SELECT DISTINCT
		fa.uid, fd.title
	FROM
			 fields_applied fa
		JOIN fields_defined fd USING (field_id)
	WHERE
			fa.event_id="$cm_event_id"
		AND fd.event_id="*"
		AND fa.field_id IN ($zambia_flag_field_ids_predicate)
		AND value="Y"
EOD;
$result = myquery($cmdb_connection, $query);
$cm_participants_array = array();
$cm_uids_array = array();
while ($row = $result->fetch_assoc()) {
	if (isset($cm_participants_array[$row["uid"]])) {
		$cm_participants_array[$row["uid"]]["cm_flag_field_title_array"][] = $row["title"];
	} else {
		$cm_participants_array[$row["uid"]] = array("cm_flag_field_title_array" => array($row["title"]));
		$cm_uids_array[] = $row["uid"];
	}
}
mysqli_free_result($result);

// TODO:
// Perform query to find "deceased" records and remove from $cm_participants_array & $cm_uids_array.
// Also add to deceased_uids_array

$cm_uids_predicate = "\"" . implode("\",\"",$cm_uids_array) . "\"";

// Lookup various fields for all registrants found
// firstname, lastname, & badgename
$query = <<<EOD
SELECT
		uid, rl_first AS firstname, rl_last AS lastname, fan_name AS badgename
	FROM
		registrant
	WHERE
		uid IN ($cm_uids_predicate)
EOD;
$result = myquery($cmdb_connection, $query);
while ($row = $result->fetch_assoc()) {
	$cm_participants_array[$row["uid"]]["firstname"] = $row["firstname"];
	$cm_participants_array[$row["uid"]]["lastname"] = $row["lastname"];
	$cm_participants_array[$row["uid"]]["badgename"] = $row["badgename"];
}
mysqli_free_result($result);

// Lookup email address for all registrants found
$query = <<<EOD
SELECT
		a.uid, a.email_address AS email
	FROM
			registrant_email a
		JOIN (
				SELECT uid, min(email_uid) AS email_uid
					FROM registrant_email
					WHERE uid IN ($cm_uids_predicate) GROUP BY uid
			) AS b USING (uid, email_uid)
EOD;
$result = myquery($cmdb_connection, $query);
while ($row = $result->fetch_assoc()) {
	$cm_participants_array[$row["uid"]]["email"] = $row["email"];
}
mysqli_free_result($result);

// Lookup phone for all registrants found
$query = <<<EOD
SELECT
		a.uid, a.contact_number AS phone
	FROM
		(SELECT
				uid, contact_number,
				CASE contact_name
					WHEN "Primary" THEN 1
					WHEN "Phone" THEN 2
					WHEN "Mobile" THEN 3
					WHEN "Cell" THEN 4
					WHEN "Home" THEN 5
					WHEN "" THEN 6
					WHEN "Secondary" THEN 7
					WHEN "Work" THEN 8
					WHEN "Other" THEN 9
					WHEN "Parental Unit" THEN 10
					WHEN "Emergency Contact" THEN 11
					ELSE 12
					END
					AS phone_priority
				FROM
					registrant_phone
				WHERE uid IN ($cm_uids_predicate)
		) AS a
		JOIN (
				SELECT uid, min(CASE contact_name
							WHEN "Primary" THEN 1
							WHEN "Phone" THEN 2
							WHEN "Mobile" THEN 3
							WHEN "Cell" THEN 4
							WHEN "Home" THEN 5
							WHEN "" THEN 6
							WHEN "Secondary" THEN 7
							WHEN "Work" THEN 8
							WHEN "Other" THEN 9
							WHEN "Parental Unit" THEN 10
							WHEN "Emergency Contact" THEN 11
							ELSE 12
							END)
							AS phone_priority
					FROM registrant_phone
					WHERE uid IN ($cm_uids_predicate)
					GROUP BY uid
			) AS b USING (uid, phone_priority);
EOD;
$result = myquery($cmdb_connection, $query);
while ($row = $result->fetch_assoc()) {
	$cm_participants_array[$row["uid"]]["phone"] = $row["phone"];
}
mysqli_free_result($result);

// Lookup address for all registrants found
// Fields: postaddress1, postaddress2, postcity, poststate, postzip, postcountry
$query = <<<EOD
SELECT
		a.uid, a.addy_1 AS postaddress1, a.addy_2 AS postaddress2, a.city AS postcity, a.state AS poststate,
		a.zip AS postzip, a.country AS postcountry
	FROM
			registrant_address a
		JOIN (
				SELECT uid, min(address_uid) AS address_uid
					FROM registrant_address
					WHERE uid IN ($cm_uids_predicate) GROUP BY uid
			) AS b USING (address_uid)
EOD;
$result = myquery($cmdb_connection, $query);
while ($row = $result->fetch_assoc()) {
	$cm_participants_array[$row["uid"]]["postaddress1"] = $row["postaddress1"];
	$cm_participants_array[$row["uid"]]["postaddress2"] = $row["postaddress2"];
	$cm_participants_array[$row["uid"]]["postcity"] = $row["postcity"];
	$cm_participants_array[$row["uid"]]["poststate"] = $row["poststate"];
	$cm_participants_array[$row["uid"]]["postzip"] = $row["postzip"];
	$cm_participants_array[$row["uid"]]["postcountry"] = $row["postcountry"];
}
mysqli_free_result($result);

// Lookup reg type for all registrants found
$query = <<<EOD
SELECT
		uid, current_membership_type AS regtype
	FROM
		events_attended
	WHERE
			event_id = "$cm_event_id"
		AND uid IN ($cm_uids_predicate)
EOD;
$result = myquery($cmdb_connection, $query);
while ($row = $result->fetch_assoc()) {
	$cm_participants_array[$row["uid"]]["regtype"] = $row["regtype"];
}
mysqli_free_result($result);

// TODO:
// Figure out if data from cmdb has changed and stop now if not.

// Lookup permroleids for permrolenames from $zambia_flags_array and add permroleids to $zambia_flags_array
$query = <<<EOD
SELECT
		permroleid, permrolename
	FROM
		PermissionRoles
	WHERE
		permrolename IN ($zambia_roles_predicate)
EOD;
$result = myquery($zambia_new_db_connection, $query);
$zambia_roles_array = array();
$all_zambia_role_ids_array = array();
while ($row = $result->fetch_assoc()) {
	$zambia_roles_array[$row["permrolename"]] = $row["permroleid"];
	$all_zambia_role_ids_array[] = $row["permroleid"];
}
mysqli_free_result($result);
foreach ($zambia_flags_array as $zambia_flag => $zambia_flag_array) {
	$zambia_flags_array[$zambia_flag]["permission_role_ids"] = array();
	foreach ($zambia_flag_array["permission_roles"] as $permission_role_name) {
		$zambia_flags_array[$zambia_flag]["permission_role_ids"][] = $zambia_roles_array[$permission_role_name];
	}
}

// Find which registrants already exist in new Zambia db
$query = <<<EOD
SELECT
		badgeid, password
	FROM
		Participants
EOD;
$result = myquery($zambia_new_db_connection, $query);
$all_current_participants = array();
while ($row = $result->fetch_assoc()) {
	if (isset($cm_participants_array[$row["badgeid"]])) {
		$cm_participants_array[$row["badgeid"]]["already_created"] = true;
		$cm_participants_array[$row["badgeid"]]["password"] = $row["password"];
	}
	$all_current_participants[$row["badgeid"]] = $row["password"];
}
mysqli_free_result($result);

// Collect participants who need to be created in new db, get data, and do insert.
// Also analyze participant data to determine who should be able to log in and
// which permission role each should have
$participants_to_insert_array = array();
$participants_to_cancel_login_array = array();
foreach ($cm_participants_array as $badgeid => $participant_array) {
	if (!isset($participant_array["already_created"])) {
		$participants_to_insert_array[] = $badgeid;
	}
	$cm_participants_array[$badgeid]["may_log_in"] = (count(array_intersect($zambia_flags_log_in_array, $participant_array["cm_flag_field_title_array"])) > 0);
	if (!$cm_participants_array[$badgeid]["may_log_in"] && isset($participant_array["already_created"])) {
		$participants_to_cancel_login_array[] = $badgeid;
	}
	$cm_participants_array[$badgeid]["zambia_role_id_array"] = array();
	foreach ($participant_array["cm_flag_field_title_array"] as $cm_flag_field_title) {
		foreach ($zambia_flags_array[$cm_flag_field_title]["permission_roles"] as $permission_role_name) {
			$cm_participants_array[$badgeid]["zambia_role_id_array"][] = $zambia_roles_array[$permission_role_name];
		}
	}
	$cm_participants_array[$badgeid]["zambia_role_id_array"] = array_unique($cm_participants_array[$badgeid]["zambia_role_id_array"]);
}
$participants_to_insert_predicate = "\"" . implode("\",\"",$participants_to_insert_array) . "\"";
$query = <<<EOD
SELECT
		badgeid, pubsname, bio, staff_notes
	FROM
		Participants
	WHERE
		badgeid IN ($participants_to_insert_predicate)
EOD;
$result = myquery($zambia_old_db_connection, $query);
while ($row = $result->fetch_assoc()) {
	$cm_participants_array[$row["badgeid"]]["pubsname"] = $row["pubsname"];
	$cm_participants_array[$row["badgeid"]]["bio"] = $row["bio"];
	$cm_participants_array[$row["badgeid"]]["staff_notes"] = $row["staff_notes"];
}
mysqli_free_result($result);

// Insert new participants with appropriate password to Participants (all fields including pubsname, bio, & staff_notes)
if (count($participants_to_insert_array) > 0) {
	$query = <<<EOD
	INSERT INTO Participants (badgeid, password, pubsname, bio, staff_notes)
		VALUES 
EOD;
	foreach ($participants_to_insert_array as $badgeid) {
		$query .= "(\"" . mysqli_real_escape_string($zambia_new_db_connection, $badgeid ) . "\",\"";
		$query .= mysqli_real_escape_string($zambia_new_db_connection, $cm_participants_array[$badgeid]["may_log_in"]?default_zambia_password_md5_for_login:default_zambia_password_md5_for_not_login ) . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "pubsname") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "bio") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "staff_notes") . "\"),";
	}
	$query = substr($query, 0, -1); // remove trailing comma
	$result = myquery($zambia_new_db_connection, $query);

	// Insert new participants to CongoDump
	$query = <<<EOD
	INSERT INTO CongoDump (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype)
		VALUES 
EOD;
	foreach ($participants_to_insert_array as $badgeid) {
		$query .= "(\"" . mysqli_real_escape_string($zambia_new_db_connection, $badgeid ) . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "firstname") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "lastname") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "badgename") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "phone") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "email") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "postaddress1") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "postaddress2") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "postcity") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "poststate") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "postzip") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "postcountry") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $cm_participants_array[$badgeid], "regtype") . "\"),";
	}
	$query = substr($query, 0, -1); // remove trailing comma
	$result = myquery($zambia_new_db_connection, $query);
	fwrite($log_file_handle, "Inserted ".count($participants_to_insert_array)." new user(s).\n");
}

// Reset passwords for every participant not allowed to log in
if (count($participants_to_cancel_login_array) > 0) {
	$participants_to_cancel_login_predicate = "\"" . implode("\",\"",$participants_to_cancel_login_array) . "\"";
	$query = <<<EOD
	UPDATE Participants SET password = "$default_zambia_password_md5_for_not_login"
	WHERE badgeid IN ($participants_to_cancel_login_predicate)
EOD;
	$result = myquery($zambia_new_db_connection, $query);
}

// Update CongoDump for all non-new participants
$any_non_new_participants = false;
	$query = <<<EOD
	REPLACE CongoDUMP (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, postcountry, regtype)
		VALUES 
EOD;
foreach ($cm_participants_array as $badgeid => $participant_array) {
	if (isset($participant_array["already_created"])) {
		$any_non_new_participants = true;
		$query .= "(\"" . mysqli_real_escape_string($zambia_new_db_connection, $badgeid ) . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "firstname") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "lastname") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "badgename") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "phone") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "email") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "postaddress1") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "postaddress2") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "postcity") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "poststate") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "postzip") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "postcountry") . "\",\"";
		$query .= array_fetch_for_db($zambia_new_db_connection, $participant_array, "regtype") . "\"),";
	}
}
if ($any_non_new_participants) {
	$query = substr($query, 0, -1); // remove trailing comma
	$result = myquery($zambia_new_db_connection, $query);
}

// Clear out permission roles for relevant role id's
$all_zambia_role_ids_predicate = "\"" . implode("\",\"",$all_zambia_role_ids_array) . "\"";
$query = <<<EOD
DELETE FROM UserHasPermissionRole WHERE permroleid IN ($all_zambia_role_ids_predicate)
EOD;
$result = myquery($zambia_new_db_connection, $query);

// Create new permission roles for all participants
$query = <<<EOD
INSERT INTO UserHasPermissionRole (badgeid, permroleid)
	VALUES 
EOD;
foreach ($cm_participants_array as $badgeid => $participant_array) {
	foreach ($participant_array["zambia_role_id_array"] as $zambia_roleid) {
		$query .= "(\"" . mysqli_real_escape_string($zambia_new_db_connection, $badgeid ) . "\",";
		$query .= $zambia_roleid . "),";
	}
}
$query = substr($query, 0, -1); // remove trailing comma
$result = myquery($zambia_new_db_connection, $query);
	
// TODO:
// Deceased participants will have no permission roles;  Have array of them, but don't delete due to foreign keys and
// possible need for program to reassign panel participants;  Set to not attending.

//print_r($cm_participants_array);
//echo("\n");
fwrite($log_file_handle, "Completed.\n");
?>