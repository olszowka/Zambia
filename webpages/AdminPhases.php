<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title, $linki, $session;
$title = "Administer Phases";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;
staff_header($title);
if (isLoggedIn() && may_I("AdminPhases")) {
	if (isset($_POST["PostCheck"])) {
		$priorValues = interpretControlString($_POST["control"], $_POST["controliv"]);

		if ($priorValues["getSessionID"] !=  session_id()) {
            $message = "Session expired, no Phases updated";
        } else {

			$prefix = "select_phase_";
			$query = <<<EOD
UPDATE Phases
	SET current = ?
	WHERE phaseid = ? AND current <> ?;
EOD;

			$key = "";
			$value = "";
			$phaseid = "";
			$param_repeat_arr = array();

			foreach ($_POST as $key => $value) {
				if (substr($key, 0, strlen($prefix)) == "select_phase_") {
					$phaseid = substr($key, strlen($prefix));

					if ($priorValues[$phaseid] != $value) {
						$param_arr = array();

						$param_arr[] = $value;
						$param_arr[] = $phaseid;
						$param_arr[] = $value;
						$param_repeat_arr[] = $param_arr;
					}
				}
			}

			if (count($param_repeat_arr) > 0) {
				$rows = mysql_cmd_with_prepare_multi($query, "iii", $param_repeat_arr);
				if (is_null($rows)) {
					return;
				}
			} else {
				$rows = 0;
			}

			if ($rows > 0) {
				if ($rows == 1) {
					$message = "1 Phase updated";
				} else {
					$message = "$rows Phases updated";
				}
			} else {
				$message = "No chages to update";
			}
        }
    }

// Start of display portion
	$paramArray = array();

	$query=<<<EOD
SELECT
		phaseid, phasename, notes, current
	FROM
			Phases
	WHERE
			implemented = 1
	ORDER BY display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$resultXML = mysql_result_to_XML("phase_info", $result);

	mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        $PriorArray[$row["phaseid"]] = $row["current"];
    }
	mysqli_free_result($result);
	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('AdminPhases.xsl', $paramArray, $resultXML);
}
staff_footer();
?>