<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03
global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Edit Custom Text";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;
$textcontents = 'hidden-empty';
$selected = '';

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
	if (isset($_POST["PostCheck"])) {
		$priorValues = interpretControlString($_POST["control"], $_POST["controliv"]);

		if ($priorValues["getSessionID"] !=  session_id()) {
            $message = "Session expired, no text updated";
        } else {
			$selected = $_POST["customtextid"];
			if ($selected == '-1') {
                $selected = '';
            }
			if ($selected != '') {

				$textcontents = $_POST["textcontents"];
				if (substr($textcontents, 0, 3) == '<p>') {
					$textcontents = mb_ereg_replace('/^<p>/i', '', $textcontents);
					$textcontents = mb_ereg_replace('/<\/p>\s*$/i', '', $textcontents);
				}


				$origcontents = $priorValues[$selected];

				if ($origcontents != $textcontents) {
					$query = <<<EOD
UPDATE CustomText
    SET textcontents = ?
    WHERE customtextid = ?;
EOD;

					$upd_array = array($textcontents, $selected);
					$rows = mysql_cmd_with_prepare($query, "si", $upd_array);
					if (is_null($rows)) {
						return;
					}

					if ($rows == 1) {
						$message = "Custom Text Updated";
					} else {
						$message = "No chages to update-rows";
					}
                } else {
                    $message = "No chages to update-select";
                }
			} else {
				$message = "No chages to update-unchanged";
            }
        }
    }

// Start of display portion
	$paramArray = array();

	$query=<<<EOD
SELECT
		customtextid, page, tag, textcontents
	FROM
			CustomText
	ORDER BY page ASC, tag ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$resultXML = mysql_result_to_XML("custom_text", $result);

	mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $PriorArray[$row["customtextid"]] = $row["textcontents"];
    }
	mysqli_free_result($result);

	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];
	$paramArray["selected"] = $selected;
	$paramArray["initialtext"] = $textcontents;

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	// following line for debugging only
	// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('EditCustomText.xsl', $paramArray, $resultXML);
}
staff_footer();
?>