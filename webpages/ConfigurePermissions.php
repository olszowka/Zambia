<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
$title = "Configure Permissions";
require_once('StaffCommonCode.php');

if (isLoggedIn() && may_I("ConfigurePermissions")) {
    staff_header($title, 'bs5');

    $atoms = array();
    $query = "SELECT permatomid, permatomtag, elementid, page, notes, display_order FROM PermissionAtoms ORDER BY display_order, permatomtag, elementid;";
    $result = mysqli_query_exit_on_error($query);
    while ($row = mysqli_fetch_assoc($result)) {
        $atoms[] = array(
            "permatomid" => intval($row["permatomid"]),
            "permatomtag" => $row["permatomtag"],
            "elementid" => is_null($row["elementid"]) ? null : intval($row["elementid"]),
            "page" => $row["page"],
            "notes" => $row["notes"],
            "display_order" => is_null($row["display_order"]) ? null : intval($row["display_order"]),
        );
    }
    mysqli_free_result($result);

    $roles = array();
    $query = "SELECT permroleid, permrolename, notes, display_order FROM PermissionRoles ORDER BY display_order, permrolename;";
    $result = mysqli_query_exit_on_error($query);
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = array(
            "permroleid" => intval($row["permroleid"]),
            "permrolename" => $row["permrolename"],
            "notes" => $row["notes"],
            "display_order" => is_null($row["display_order"]) ? null : intval($row["display_order"]),
        );
    }
    mysqli_free_result($result);

    $phases = array();
    $query = "SELECT phaseid, phasename, notes, current, implemented, display_order FROM Phases ORDER BY display_order, phasename;";
    $result = mysqli_query_exit_on_error($query);
    while ($row = mysqli_fetch_assoc($result)) {
        $phases[] = array(
            "phaseid" => intval($row["phaseid"]),
            "phasename" => $row["phasename"],
            "notes" => $row["notes"],
            "current" => boolval($row["current"]),
            "implemented" => boolval($row["implemented"]),
            "display_order" => is_null($row["display_order"]) ? null : intval($row["display_order"]),
        );
    }
    mysqli_free_result($result);

    // Role-based grants only; badgeid-based per-participant overrides are unused in practice
    // and out of scope for this page.
    $permissions = array();
    $query = "SELECT permissionid, permatomid, phaseid, permroleid FROM Permissions WHERE permroleid IS NOT NULL ORDER BY permatomid, permroleid, phaseid;";
    $result = mysqli_query_exit_on_error($query);
    while ($row = mysqli_fetch_assoc($result)) {
        $permissions[] = array(
            "permissionid" => intval($row["permissionid"]),
            "permatomid" => intval($row["permatomid"]),
            "phaseid" => is_null($row["phaseid"]) ? null : intval($row["phaseid"]),
            "permroleid" => intval($row["permroleid"]),
        );
    }
    mysqli_free_result($result);

    // The roles the current user holds, so the client can block edits that would revoke
    // the current user's own access to this page (see PermissionMatrix.tsx / PhasesTab.tsx).
    $currentUserRoleIds = array();
    $query = "SELECT permroleid FROM UserHasPermissionRole WHERE badgeid = ?;";
    $result = mysqli_query_with_prepare_and_exit_on_error($query, "s", array($badgeid));
    while ($row = mysqli_fetch_assoc($result)) {
        $currentUserRoleIds[] = intval($row["permroleid"]);
    }
    mysqli_free_result($result);

    $bootstrapData = array(
        "atoms" => $atoms,
        "roles" => $roles,
        "phases" => $phases,
        "permissions" => $permissions,
        "currentUserRoleIds" => $currentUserRoleIds,
    );
    ?>
    <script id="configure-permissions-data" type="application/json"><?php
        echo json_encode($bootstrapData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    ?></script>
    <div id="configure-permissions-root" class="container-fluid mt-3"></div>
    <?php
    staff_footer();
} else {
    $message_error = "You do not currently have permission to view this page.<br>\n";
    StaffRenderErrorPage($title, $message_error, 'bs5');
}
?>
