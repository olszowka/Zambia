<?php
// Copyright (c) 2005-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
global $header_section, $message_error, $title;
$title = "General Interests";
// This can be a participant or a staff page
require('PartCommonCode.php'); // initialize db; check login;
require_once('StaffHeader.php');
require_once('StaffFooter.php');
// set $badgeid from session

if (!may_I('general_interests')) {
    $message_error = "You do not currently have permission to view this page.<br>\n";
    RenderError($message_error);
    exit();
}
$edit_badgeid = getInt('edit_badgeid');
if ($edit_badgeid === false) {
    $edit_badgeid = $badgeid;
    participant_header($title, false, 'Normal', 'bs5');
} else {
    $header_section = HEADER_STAFF;
    if (!may_I('edit_participant_responses')) {
        $message_error = "You do not have permission to access this page.";
        StaffRenderErrorPage($title, $message_error, 'bs5');
        exit();
    }
    staff_header($title, 'bs4');
}
$db_updated = false;
$yespanels = getString('yespanels');
if (!is_null($yespanels)) {
    // post from form submission
    if (!may_I('my_gen_int_write')) {
        $message_error = "You do not currently have permission to perform this action.<br>\n";
        RenderError($message_error);
        exit();
    }
    $yespanels = if_null_default($yespanels, '');
    $nopanels = if_null_default(getString('nopanels'), '');
    $yespeople = if_null_default(getString('yespeople'), '');
    $nopeople = if_null_default(getString('nopeople'), '');
    $otherroles = if_null_default(getString('otherroles'), '');
    $query = <<<EOD
REPLACE INTO ParticipantInterests (badgeid, yespanels, nopanels, yespeople, nopeople, otherroles)
    VALUES (?, ?, ?, ?, ?, ?);
EOD;
    $queryParamsArr = array($edit_badgeid, $yespanels, $nopanels, $yespeople, $nopeople, $otherroles);
    mysqli_query_with_prepare_and_exit_on_error($query, 'ssssss', $queryParamsArr);
    // Just assume it exited on error correctly

    $query = <<<EOD
SELECT max(roleid) FROM Roles;
EOD;
    if (!$result = mysqli_query_exit_on_error($query)) {
        $message = "Error querying database.  Database not updated.";
        RenderError($message);
        exit();
    }
    $maxRoleId = mysqli_fetch_array($result, MYSQLI_NUM)[0];
    $rolesArr = array();
    for ($i = 1; $i < $maxRoleId; $i++) {
        if (getInt("willdorole$i") !== false) {
            $rolesArr[] = $i;
        }
    }
    $query = <<<EOD
DELETE FROM ParticipantHasRole WHERE badgeid = ? ;
EOD;
    mysqli_query_with_prepare_and_exit_on_error($query, 's', array($edit_badgeid));
    // Just assume it exited on error correctly
    if (count($rolesArr) > 0) {
        $query = <<<EOD
INSERT INTO ParticipantHasRole (badgeid, roleid)
    VALUES 
EOD;
        $queryValsArr = array();
        foreach ($rolesArr as $roleid) {
            $queryValsArr[] = "('$edit_badgeid', $roleid)";
        }
        $query .= implode(',', $queryValsArr) . ';';
        if (!mysqli_query_exit_on_error($query)) {
            $message = "Error updating database.  Database not updated.";
            RenderError($message);
            exit();
        }
    } // if there are any new roles to insert
    $db_updated = true;
}

$querySQLArr = array();
$queryParamTypesArr = array();
$queryParamsArr = array();

$querySQLArr['participantinterests'] = <<<EOD
SELECT
        yespanels, nopanels, yespeople, nopeople, otherroles
    FROM
        ParticipantInterests
    WHERE
        badgeid = ?;
EOD;
$queryParamTypesArr['participantinterests'] = 's';
$queryParamsArr['participantinterests'] = array($edit_badgeid);

$querySQLArr['participantroles'] = <<<EOD
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
                        badgeid = ?
                    ) as PHR USING (roleid)
    ORDER BY
        R.display_order;
EOD;
$queryParamTypesArr['participantroles'] = 's';
$queryParamsArr['participantroles'] = array($edit_badgeid);

if (!$resultXML = mysql_prepare_query_XML($querySQLArr, $queryParamTypesArr, $queryParamsArr)) {
    exit(); // Should have exited already
}
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
$resultXML = appendCustomTextArrayToXML($resultXML);

echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
$paramArray = array();
$paramArray['readonly'] = may_I('my_gen_int_write') ? '0' : '1';
$paramArray['db_updated'] = $db_updated ? '1' : '0';
if ($edit_badgeid != $badgeid) {
    $query = <<<EOD
SELECT
        firstname, lastname
     FROM
         CongoDump
     WHERE
         badgeid = ?;
EOD;
    $query_param_array = array($edit_badgeid);
    $result = mysqli_query_with_prepare_and_exit_on_error($query, 's', $query_param_array);
    $row = mysqli_fetch_assoc($result);
    $paramArray['EditParticipantName'] = $row['firstname'] . ' ' . $row['lastname'];
    $paramArray['EditBadgeId'] = $edit_badgeid;
    $paramArray['readonly'] = '0';
}
RenderXSLT('my_interests.xsl', $paramArray, $resultXML);
participant_footer();
?>
