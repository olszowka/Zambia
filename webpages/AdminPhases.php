<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title, $linki;
$title = "Administer Phases";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;
staff_header($title);
if (isLoggedIn() && may_I("AdminPhases")) {
	if (isset($_POST["PostCheck"])) {
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
				$param_arr = array();
				$phaseid = substr($key, strlen($prefix));
				$param_arr[] = $value;
				$param_arr[] = $phaseid;
				$param_arr[] = $value;
				$param_repeat_arr[] = $param_arr;
            }

		}

		$rows = mysql_query_with_prepare_multi($query, "iii", $param_repeat_arr);
		if (is_null($rows)) {
			return;
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

	$queryArray["phase_info"]=<<<EOD
SELECT
		phaseid, phasename, notes, current
	FROM
			Phases
	WHERE
			implemented = 1
	ORDER BY display_order ASC;
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
		RenderError($message_error);
		exit();
	}

    $paramArray = array();
	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('AdminPhases.xsl', $paramArray, $resultXML);
}
staff_footer();
?>