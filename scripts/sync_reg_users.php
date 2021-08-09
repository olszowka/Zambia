<?php
use function UI\quit;
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
    echo "Could not connect to mysql.\n";
    exit(-1);
}

$pgconn = pg_connect(WELLINGTONPROD);
if (!$pgconn) {
    echo "Unable to connect to Wellington";
    exit();
}

$successes = 0;
$badgeids = [];
$cgdata = [];

$csfetch = "select * from CongoDump where badgeid RLIKE '^[0-9]*$';";
$results = mysqli_query_with_error_handling($csfetch);
$inclause = '';
while ($row = mysqli_fetch_assoc($results)) {
     $badgeids[$row["badgeid"]] = 1;
     if ($inclause != '')
        $inclause .= ',';
     $inclause .= $row["badgeid"];
     $cgdata[$row["badgeid"]] = $row;
     // now store the entire row

}
$inclause = " IN (" . $inclause . ")";

mysqli_free_result($results);

$pgquery = <<<EOD
SELECT
        r.membership_number AS badgeid,
        CASE
                WHEN COALESCE(ct.preferred_last_name, '') <> '' THEN ct.preferred_last_name
                ELSE ct.last_name
        END AS lastname,
        CASE
                WHEN COALESCE(ct.preferred_first_name, '') <> '' THEN ct.preferred_first_name
                ELSE ct.first_name
        END AS firstname,
        CASE
                WHEN ct.badge_title <> '' THEN ct.badge_title
                ELSE TRIM(
                        CASE
                                WHEN COALESCE(ct.preferred_first_name, '') <> '' THEN ct.preferred_first_name
                                ELSE ct.first_name
                        END || ' ' ||
                        CASE
                                WHEN COALESCE(ct.preferred_last_name, '') <> '' THEN ct.preferred_last_name
                                ELSE ct.last_name
                        END
                )
        END AS badgename,
		u.email AS email,
		ct.address_line_1 as postaddress1,
		ct.address_line_2 as postaddress2,
        ct.city as postcity, ct.province AS poststate, ct.postal AS postzip,
		ct.country as postcountry,
        m.name AS regtype
FROM public.reservations r
JOIN public.claims cl ON (cl.reservation_id = r.id AND cl.active_to IS NULL)
JOIN public.dc_contacts ct ON (cl.id = ct.claim_id)
JOIN public.users u ON (cl.user_id = u.id)
JOIN public.orders o ON (r.id = o.reservation_id AND o.active_to IS NULL)
JOIN public.memberships m ON (o.membership_id = m.id)
WHERE
        r.membership_number $inclause
ORDER BY
        last_name, first_name
EOD;
//echo "pg query=" . $pgquery . "\n\n";

$result = pg_query($pgconn, $pgquery);
if (!$result) {
    echo "Wellington query error" . pg_result_error($result, PGSQL_STATUS_STRING);
    exit();
}

$diffs = [];
while ($row = pg_fetch_assoc($result)) {
    $changes = '';
    $cdrow = $cgdata[$row["badgeid"]];
    foreach ($row as $key => $pgvalue) {
        if ($cdrow[$key] != $pgvalue)
            $changes .= ', ' . $key . '= "' . mysqli_real_escape_string($linki, $pgvalue) . '"';
    }

    if ($changes != '') {
        $updcmd = "UPDATE CongoDump SET " . mb_substr($changes, 1) . " WHERE badgeid = '" . mysqli_real_escape_string($linki, $row["badgeid"]) . "';";
        //echo $updcmd . "\n";
        mysqli_query_with_error_handling($updcmd);
        $successes += mysqli_affected_rows($linki);
    }
}

echo "Successfully synced $successes users/participants at " . strftime('%c') . "\n";