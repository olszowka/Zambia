#!/usr/local/bin/php -q
<?php
//This page is intended to be hit from a cron job only.
//Need to add some code to prevent it from being accessed any other way, but leave it exposed for now for testing.
global $linki;
error_reporting(E_ERROR);
require_once('webpages/db_functions.php'); //reset connection to db and check if logged in
if (prepare_db_and_more() === false) {
	echo "Unable to connect to database.\nNo further execution possible.\n";
	exit(1);
};
$now = new DateTime();
$regdb=REG_DBNAME;
$conid=REG_CONID;
echo "Reg Sync started at " . $now->format('Y-m-d H:i:s') . "\n";

$sql = <<<EOD
set collation_connection = utf8mb4_general_ci;
EOD;
mysqli_query_exit_on_error($sql);

$sql = <<<EOD
UPDATE $regdb.perinfo r
JOIN Participants P ON (P.badgeid = CAST(r.id AS CHAR) AND r.active='N')
SET r.active = 'Y'
WHERE r.active = 'N';
EOD;
mysqli_query_exit_on_error($sql);
$rowsact = mysqli_affected_rows($linki);
$sql = <<<EOD
UPDATE CongoDump c
JOIN (
	SELECT CAST(P.id as CHAR) id, first_name, last_name, badge_name, phone, email_addr, address, addr_2, 
	city, state, zip, country, IFNULL(RL.label, 'Not Registered') AS label
	FROM $regdb.perinfo P
	LEFT OUTER JOIN (
		SELECT P1.id, M.label
		FROM $regdb.perinfo P1
		LEFT OUTER JOIN $regdb.reg R ON (R.perid = P1.id AND R.conid = $conid)
		LEFT OUTER JOIN $regdb.memList M ON (R.memID = M.id AND M.conid = $conid)
		) RL ON (RL.id = P.id)
	WHERE P.active = 'Y'
) R ON c.badgeid = R.id
SET
        c.firstname = R.first_name,
        c.lastname = R.last_name,
        c.badgename = R.badge_name,
        c.phone = R.phone,
        c.email = R.email_addr,
        c.postaddress1 = R.address,
        c.postaddress2 = R.addr_2,
        c.postcity = R.city,
        c.poststate = R.state,
        c.postzip = R.zip,
        c.postcountry = R.country,
        c.regtype = R.label
WHERE (
	IFNULL(c.firstname, '') != IFNULL(R.first_name,'') OR
	IFNULL(c.lastname,'') != IFNULL(R.last_name,'') OR
	IFNULL(c.badgename,'') != IFNULL(R.badge_name,'') OR
	IFNULL(c.phone,'') != IFNULL(R.phone,'') OR
	IFNULL(c.email,'') != IFNULL(R.email_addr,'') OR
	IFNULL(c.postaddress1,'') != IFNULL(R.address,'') OR
	IFNULL(c.postaddress2,'') != IFNULL(R.addr_2,'') OR
	IFNULL(c.postcity,'') != IFNULL(R.city,'') OR
	IFNULL(c.poststate,'') != IFNULL(R.state,'') OR
	IFNULL(c.postzip,'') != IFNULL(R.zip,'') OR
	IFNULL(c.postcountry,'') != IFNULL(R.country,'') OR
	IFNULL(c.regtype,'') != IFNULL(R.label,'')
);
EOD;
mysqli_query_exit_on_error($sql);
$rows = mysqli_affected_rows($linki);

$now = new DateTime();
echo "Reg Sync run complete with $rowsact activated and $rows participants updated at " . $now->format('Y-m-d H:i:s') . "\n";
exit(0);
?>
