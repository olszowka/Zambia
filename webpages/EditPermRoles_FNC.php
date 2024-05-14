<?php
// Created 2021-01-11 by Peter Olszowka
// Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
function fetchMyEditableRoles($loggedInUserBadgeid) {
    $query = <<<EOD
SELECT DISTINCT
        PA.permatomid, PA.elementid
    FROM
             PermissionAtoms PA
        JOIN Permissions P USING (permatomid)
        JOIN UserHasPermissionRole UHPR USING (permroleid)
    WHERE
            UHPR.badgeid = ?
        AND PA.permatomtag = 'EditUserPermRoles';
EOD;
    $result = mysqli_query_with_prepare_and_exit_on_error($query, "s", array($loggedInUserBadgeid));
    if (!$result) {
        exit(); // should have exited already
    }
    $functionResult = array();
    $functionResult['mayIEditAllRoles'] = false;
    $functionResult['rolesIMayEditArr'] = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $role = $row['elementid'];
        if (is_null($role)) {
            $functionResult['mayIEditAllRoles'] = true;
        } else {
            $functionResult['rolesIMayEditArr'][] = $role;
        }
    }
    mysqli_free_result($result);
    return $functionResult;
}
