<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('StaffCommonCode.php');

// gets data for a participant to be displayed.  Returns as XML
function fetch_demographicOptions() {
    global $message_error;
    $fbadgeid = getInt("badgeid");
    if (!$fbadgeid) {
        exit();
    }
    $query["fetchParticipants"] = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
    FROM
			 Participants P
		JOIN CongoDump CD ON P.badgeid = CD.badgeid
    WHERE
        P.badgeid = "$fbadgeid"
    ORDER BY
        CD.lastname, CD.firstname
EOD;
    $resultXML = mysql_query_XML($query);
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
    }
    header("Content-Type: text/xml");
    echo($resultXML->saveXML());
    exit();
}

function update_demographics() {
    global $linki, $message_error;
    $demographics = json_decode(getString("demographics"));
    // reset display order to match new order and find which rows to delete
    $idsFound = "";
    $display_order = 10;
    foreach ($demographics as $demo) {
        $demo->display_order = $display_order;
        $display_order = $display_order + 10;
        $id = (int) $demo->demographicid;
        if ($id) {
            $idsFound = $idsFound . ',' . $id;
        }
    }

// delete the ones no longer in the JSON uploaded.
    $sql = "DELETE FROM DemographicOptionConfig WHERE demographicid NOT IN (" . mb_substr($idsFound, 1) . ");";

    if (!mysqli_query_exit_on_error($sql)) {
        exit(); // Should have exited already.
    }
    $deletedopt = mysqli_affected_rows($linki);
    $sql = "DELETE FROM DemographicConfig WHERE demographicid NOT IN (" . mb_substr($idsFound, 1) . ");";

    if (!mysqli_query_exit_on_error($sql)) {
        exit(); // Should have exited already.
    }
    $deleted = mysqli_affected_rows($linki);

    // insert new rows (those with id < 0)
    $inserted = 0;
    foreach ($demographics as $demo) {
        $id = (int) $demo->demographicid;
        if ($id < 0) {
            $sql = <<<EOD
                INSERT INTO DemographicConfig (shortname, description, value, prompt,
                hover, display_order, typeid, required, publish, privacy_user, searchable, ascending, min_value, max_value)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
EOD;
            $paramarray = array(
                property_exists($demo, "shortname") ? $demo->shortname : "",
                property_exists($demo, "description") ? $demo->description : null,
                property_exists($demo, "value") ? $demo->value : "",
                property_exists($demo, "prompt") ? $demo->prompt : "",
                property_exists($demo, "hover") ? $demo->hover : null,
                property_exists($demo, "display_order") ? $demo->display_order: null,
                property_exists($demo, "typeid") ? (int) $demo->typeid: 60,
                property_exists($demo, "required") ? $demo->required : 1,
                property_exists($demo, "publish") ? $demo->publish : 0,
                property_exists($demo, "privacy_user") ? $demo->privacy_user : 0,
                property_exists($demo, "searchable") ? $demo->searchable : 0,
                property_exists($demo, "ascending") ? $demo->ascending : 1,
                property_exists($demo, "min_value") ? ($demo->min_value != "" ? $demo->min_value : null) : null,
                property_exists($demo, "max_value") ? ($demo->max_value != "" ? $demo->max_value : null) : null
            );
            $inserted = $inserted + mysql_cmd_with_prepare($sql, "sssssiiiiiiiii", $paramarray);
        }
    }

    // update existing rows (those with id >= 0)
    $updated = 0;
    foreach ($demographics as $demo) {
        $id = (int) $demo->demographicid;
        if ($id >= 0) {
            $sql = <<<EOD
               UPDATE DemographicConfig SET
                    shortname = ?,
                    description = ?,
                    value = ?,
                    prompt = ?,
                    hover = ?,
                    display_order = ?,
                    typeid = ?,
                    required = ?,
                    publish = ?,
                    privacy_user = ?,
                    searchable = ?,
                    ascending = ?,
                    min_value = ?,
                    max_value = ?
            WHERE demographicid = ?;
EOD;
            $paramarray = array(
                property_exists($demo, "shortname") ? $demo->shortname : "",
                property_exists($demo, "description") ? $demo->description : null,
                property_exists($demo, "value") ? $demo->value : "",
                property_exists($demo, "prompt") ? $demo->prompt : "",
                property_exists($demo, "hover") ? $demo->hover : null,
                property_exists($demo, "display_order") ? $demo->display_order: null,
                property_exists($demo, "typeid") ? (int) $demo->typeid: 60,
                property_exists($demo, "required") ? $demo->required : 1,
                property_exists($demo, "publish") ? $demo->publish : 0,
                property_exists($demo, "privacy_user") ? $demo->privacy_user : 0,
                property_exists($demo, "searchable") ? $demo->searchable : 0,
                property_exists($demo, "ascending") ? $demo->ascending : 1,
                property_exists($demo, "min_value") ? ($demo->min_value != "" ? $demo->min_value : null) : null,
                property_exists($demo, "max_value") ? ($demo->max_value != "" ? $demo->max_value : null) : null,
                $id
            );
            $updated = $updated + mysql_cmd_with_prepare($sql, "sssssiiiiiiiiii", $paramarray);
        }
    }

    $message = "<p>Database updates: " . $deleted . " deleted, " . $inserted . " inserted, " . $updated . " updated</p>";
    echo "var message = '" . $message . "';\n";

    // get updated demographics now with the id's in it
    fetch_demographics();
}

function fetch_demographics() {
    // get demographic config table data
    $query=<<<EOD
		SELECT JSON_ARRAYAGG(JSON_OBJECT(
			'demographicid', demographicid,
			'shortname', d.shortname,
			'description', d.description,
			'value', d.value,
			'prompt', prompt,
			'hover', hover,
			'display_order', d.display_order,
			'typeid', d.typeid,
			'typename', t.shortname,
			'required', required,
			'publish', publish,
			'privacy_user', privacy_user,
			'searchable', searchable,
			'ascending', ascending,
			'min_value', min_value,
			'max_value', max_value
			)) AS config
		FROM demographicconfig d
		JOIN demographictypes t USING (typeid)
		ORDER BY d.display_order ASC;
EOD;
    $result = mysqli_query_exit_on_error($query);
	$Config = "[]";
    while ($row = mysqli_fetch_assoc($result)) {
        $Config = $row["config"];
    }
	mysqli_free_result($result);

	if ($Config == "") {
		$Config = "[]";
    }
    echo "demographics = " . $Config . "\n";
}

// Start here.  Should be AJAX requests only
$ajax_request_action = getString("ajax_request_action");
if ($ajax_request_action == "") {
    exit();
}
//error_log("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action");
switch ($ajax_request_action) {
    case "fetch_demographics":
        fetch_demographics();
        break;
    case "fetch_demographicOptions":
        fetch_demographicOptions();
        break;
    case "update_demographics":
        update_demographics();
        break;
    default:
        exit();
}

?>
