<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03
global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Edit Demographics";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
	if (isset($_POST["PostCheck"])) {
		$priorValues = interpretControlString($_POST["control"], $_POST["controliv"]);

		if ($priorValues["getSessionID"] !=  session_id()) {
            $message = "Session expired, no text updated";
        } else {
//            $selected = $_POST["customtextid"];
//            if ($selected == '-1') {
//                $selected = '';
//            }
//            if ($selected != '') {

//                $textcontents = $_POST["textcontents"];
//                if (mb_substr($textcontents, 0, 3) == '<p>') {
//                    $textcontents = mb_ereg_replace('/^<p>/i', '', $textcontents);
//                    $textcontents = mb_ereg_replace('/<\/p>\s*$/i', '', $textcontents);
//                }


//                $origcontents = $priorValues[$selected];

//                if ($origcontents != $textcontents) {
//                    $query = <<<EOD
//UPDATE CustomText
//    SET textcontents = ?
//    WHERE customtextid = ?;
//EOD;

//                    $upd_array = array($textcontents, $selected);
//                    $rows = mysql_cmd_with_prepare($query, "si", $upd_array);
//                    if (is_null($rows)) {
//                        return;
//                    }

//                    if ($rows == 1) {
//                        $message = "Custom Text Updated";
//                    } else {
//                        $message = "No chages to update-rows";
//                    }
//                } else {
//                    $message = "No chages to update-select";
//                }
//            } else {
//                $message = "No chages to update-unchanged";
//            }
        }
    }

// Start of display portion
	$paramArray = array();

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

	$query=<<<EOD
	SELECT
		typeid, shortname, description
	FROM DemographicTypes
	WHERE current = 1
	ORDER BY display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$resultXML = mysql_result_to_XML("demographictypes", $result);
	mysqli_free_result($result);

	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];
	$paramArray["config"] = $Config;

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	// following line for debugging only
	// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('EditDemographics.xsl', $paramArray, $resultXML);
}
staff_footer();
?>