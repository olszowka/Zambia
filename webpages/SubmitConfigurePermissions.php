<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Start here.  Should be AJAX requests only
global $returnAjaxErrors, $return500errors;
$returnAjaxErrors = true;
$return500errors = true;
require_once('StaffCommonCode.php'); // will check for staff privileges

function toggle_permission() {
    $permatomid = getInt("permatomid", null);
    $permroleid = getInt("permroleid", null);
    $phaseidParam = getString("phaseid");
    $grant = getInt("grant", null);
    if (is_null($permatomid) || is_null($permroleid) || is_null($grant)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $phaseid = ($phaseidParam === "" || is_null($phaseidParam)) ? null : intval($phaseidParam);

    if ($grant) {
        if (is_null($phaseid)) {
            $query = "INSERT IGNORE INTO Permissions (permatomid, phaseid, permroleid) VALUES (?, NULL, ?);";
            $rows = mysql_cmd_with_prepare($query, "ii", array($permatomid, $permroleid));
        } else {
            $query = "INSERT IGNORE INTO Permissions (permatomid, phaseid, permroleid) VALUES (?, ?, ?);";
            $rows = mysql_cmd_with_prepare($query, "iii", array($permatomid, $phaseid, $permroleid));
        }
    } else {
        if (is_null($phaseid)) {
            $query = "DELETE FROM Permissions WHERE permatomid = ? AND permroleid = ? AND phaseid IS NULL;";
            $rows = mysql_cmd_with_prepare($query, "ii", array($permatomid, $permroleid));
        } else {
            $query = "DELETE FROM Permissions WHERE permatomid = ? AND permroleid = ? AND phaseid = ?;";
            $rows = mysql_cmd_with_prepare($query, "iii", array($permatomid, $permroleid, $phaseid));
        }
    }
    if (is_null($rows)) {
        RenderErrorAjax("Unable to update database");
        exit();
    }
    echo json_encode(array());
    exit();
}

function add_role() {
    $permrolename = getString("permrolename");
    $notes = getString("notes");
    $display_order = getInt("display_order", 0);
    if (empty($permrolename)) {
        RenderErrorAjax("Role name is required.");
        exit();
    }
    global $mysqli;
    $query = "INSERT INTO PermissionRoles (permrolename, notes, display_order) VALUES (?, ?, ?);";
    $rows = mysql_cmd_with_prepare($query, "ssi", array($permrolename, $notes, $display_order));
    if (is_null($rows)) {
        RenderErrorAjax("Unable to add role");
        exit();
    }
    $json_return = array(
        "role" => array(
            "permroleid" => intval($mysqli->insert_id),
            "permrolename" => $permrolename,
            "notes" => $notes,
            "display_order" => $display_order,
        ),
    );
    echo json_encode($json_return);
    exit();
}

function update_role() {
    $permroleid = getInt("permroleid", null);
    $permrolename = getString("permrolename");
    $notes = getString("notes");
    $display_order = getInt("display_order", 0);
    if (is_null($permroleid) || empty($permrolename)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $query = "UPDATE PermissionRoles SET permrolename = ?, notes = ?, display_order = ? WHERE permroleid = ?;";
    $rows = mysql_cmd_with_prepare($query, "ssii", array($permrolename, $notes, $display_order, $permroleid));
    if (is_null($rows)) {
        RenderErrorAjax("Unable to update role");
        exit();
    }
    echo json_encode(array());
    exit();
}

function delete_role() {
    $permroleid = getInt("permroleid", null);
    if (is_null($permroleid)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $usageQuery = <<<EOD
SELECT
    (SELECT COUNT(*) FROM Permissions WHERE permroleid = ?) +
    (SELECT COUNT(*) FROM UserHasPermissionRole WHERE permroleid = ?) AS usage_count;
EOD;
    $result = mysqli_query_with_prepare_and_exit_on_error($usageQuery, "ii", array($permroleid, $permroleid));
    $row = mysqli_fetch_assoc($result);
    if (intval($row["usage_count"]) > 0) {
        RenderErrorAjax("This role is still in use and cannot be deleted.");
        exit();
    }
    $rows = mysql_cmd_with_prepare("DELETE FROM PermissionRoles WHERE permroleid = ?;", "i", array($permroleid));
    if (is_null($rows)) {
        RenderErrorAjax("Unable to delete role");
        exit();
    }
    echo json_encode(array());
    exit();
}

// Reassigns display_order to 10, 20, 30, ... in the given order, same convention as the
// Configuration Table Editor's drag-to-reorder behavior (see SubmitEditConfigTable.php).
function reorder_roles() {
    $orderedIds = getArrayOfInts("ordered_ids", null);
    if (is_null($orderedIds)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $displayOrder = 10;
    $paramRepeatArr = array();
    foreach ($orderedIds as $id) {
        $paramRepeatArr[] = array($displayOrder, intval($id));
        $displayOrder += 10;
    }
    $rows = mysql_cmd_with_prepare_multi("UPDATE PermissionRoles SET display_order = ? WHERE permroleid = ?;", "ii", $paramRepeatArr);
    if (is_null($rows)) {
        RenderErrorAjax("Unable to update role order");
        exit();
    }
    echo json_encode(array());
    exit();
}

function add_phase() {
    $phasename = getString("phasename");
    $notes = getString("notes");
    $current = getInt("current", 0);
    $implemented = getInt("implemented", 0);
    $display_order = getInt("display_order", 0);
    if (empty($phasename)) {
        RenderErrorAjax("Phase name is required.");
        exit();
    }
    $query = "INSERT INTO Phases (phasename, notes, current, implemented, display_order) VALUES (?, ?, ?, ?, ?);";
    $rows = mysql_cmd_with_prepare($query, "ssiii", array($phasename, $notes, $current, $implemented, $display_order));
    if (is_null($rows)) {
        RenderErrorAjax("Unable to add phase");
        exit();
    }
    global $mysqli;
    $json_return = array(
        "phase" => array(
            "phaseid" => intval($mysqli->insert_id),
            "phasename" => $phasename,
            "notes" => $notes,
            "current" => boolval($current),
            "implemented" => boolval($implemented),
            "display_order" => $display_order,
        ),
    );
    echo json_encode($json_return);
    exit();
}

function update_phase() {
    $phaseid = getInt("phaseid", null);
    $phasename = getString("phasename");
    $notes = getString("notes");
    $current = getInt("current", 0);
    $implemented = getInt("implemented", 0);
    $display_order = getInt("display_order", 0);
    if (is_null($phaseid) || empty($phasename)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $query = "UPDATE Phases SET phasename = ?, notes = ?, current = ?, implemented = ?, display_order = ? WHERE phaseid = ?;";
    $rows = mysql_cmd_with_prepare($query, "ssiiii", array($phasename, $notes, $current, $implemented, $display_order, $phaseid));
    if (is_null($rows)) {
        RenderErrorAjax("Unable to update phase");
        exit();
    }
    echo json_encode(array());
    exit();
}

function delete_phase() {
    $phaseid = getInt("phaseid", null);
    if (is_null($phaseid)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $result = mysqli_query_with_prepare_and_exit_on_error(
        "SELECT COUNT(*) AS usage_count FROM Permissions WHERE phaseid = ?;",
        "i",
        array($phaseid)
    );
    $row = mysqli_fetch_assoc($result);
    if (intval($row["usage_count"]) > 0) {
        RenderErrorAjax("This phase is still in use and cannot be deleted.");
        exit();
    }
    $rows = mysql_cmd_with_prepare("DELETE FROM Phases WHERE phaseid = ?;", "i", array($phaseid));
    if (is_null($rows)) {
        RenderErrorAjax("Unable to delete phase");
        exit();
    }
    echo json_encode(array());
    exit();
}

function reorder_phases() {
    $orderedIds = getArrayOfInts("ordered_ids", null);
    if (is_null($orderedIds)) {
        RenderErrorAjax("Internal error.");
        exit();
    }
    $displayOrder = 10;
    $paramRepeatArr = array();
    foreach ($orderedIds as $id) {
        $paramRepeatArr[] = array($displayOrder, intval($id));
        $displayOrder += 10;
    }
    $rows = mysql_cmd_with_prepare_multi("UPDATE Phases SET display_order = ? WHERE phaseid = ?;", "ii", $paramRepeatArr);
    if (is_null($rows)) {
        RenderErrorAjax("Unable to update phase order");
        exit();
    }
    echo json_encode(array());
    exit();
}

if (!isLoggedIn()) {
    RenderErrorAjax("You are not logged in or your session has expired.");
    exit();
}

if (!may_I('ConfigurePermissions')) {
    RenderErrorAjax("You do not currently have permission to perform this action.");
    exit();
}

$ajax_request_action = getString("ajax_request_action");
if (is_null($ajax_request_action)) {
    RenderErrorAjax("Internal error.");
    exit();
}

switch ($ajax_request_action) {
    case "toggle_permission":
        toggle_permission();
        break;
    case "add_role":
        add_role();
        break;
    case "update_role":
        update_role();
        break;
    case "delete_role":
        delete_role();
        break;
    case "reorder_roles":
        reorder_roles();
        break;
    case "add_phase":
        add_phase();
        break;
    case "update_phase":
        update_phase();
        break;
    case "delete_phase":
        delete_phase();
        break;
    case "reorder_phases":
        reorder_phases();
        break;
    default:
        RenderErrorAjax("Internal error.");
}
exit();
?>
