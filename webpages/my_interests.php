<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "General Interests";
require('PartCommonCode.php'); // initialize db; check login;
require_once('ParticipantHeader.php');
require_once('renderMyInterests.php');
// set $badgeid from session
$query = "SELECT * FROM ParticipantInterests WHERE badgeid='$badgeid'";
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}
$rows = mysqli_num_rows($result);
if ($rows > 1) {
    $message = $query . "<br>Multiple rows returned from database where one expected. Unable to continue.";
    RenderError($message);
    exit();
}
if ($rows == 0) {
    $yespanels = "";
    $nopanels = "";
    $yespeople = "";
    $nopeople = "";
    $otherroles = "";
    $newrow = true;
} else {
    list($foo, $yespanels, $nopanels, $yespeople, $nopeople, $otherroles) = mysqli_fetch_array($result, MYSQLI_NUM);
    $newrow = false;
}
mysqli_free_result($result);
$query = <<<EOD
SELECT
        PHR.badgeid, R.roleid, R.rolename
    FROM
            Roles R
            LEFT JOIN (
                SELECT
                        badgeid, roleid
                    FROM
                        ParticipantHasRole
                    WHERE
                        badgeid='$badgeid'
                    ) as PHR USING (roleid)
    ORDER BY
        R.display_order;
EOD;

if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}
$i = 0;
$rolearray = array();
while ($rolearray[$i] = mysqli_fetch_assoc($result)) {
    $i++;
}
mysqli_free_result($result);
$error = false;
$message = "";
renderMyInterests($title, $error, $message, $rolearray);
participant_footer();
?>
