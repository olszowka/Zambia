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
		try {
            $prefix = "select_phase_";
			$upd_query = <<<EOD
UPDATE Phases
SET current = ?
WHERE phaseid = ? AND current <> ?;
EOD;

            $key = "";
            $value = "";
            $phaseid = "";

            mysqli_autocommit($linki, FALSE); //turn on transactions
            $stmt = mysqli_prepare($linki, $upd_query);
            mysqli_stmt_bind_param($stmt, "iii", $value, $phaseid, $value);

		    foreach ($_POST as $key => $value) {
			    if (substr($key, 0, strlen($prefix)) == "select_phase_") {
				    $phaseid = substr($key, strlen($prefix));
                    mysqli_stmt_execute($stmt);
					$rows = $rows + mysqli_affected_rows($linki);
                }
            }

			mysqli_stmt_close($stmt);
			mysqli_commit($linki);
        }
        catch(Exception $e) {
			mysqli_rollback($linki); //remove all queries from queue if error (undo)
			RenderError($e->getMessage());
			/*throw $e;*/
        }

        mysqli_autocommit($linki, TRUE); //turn off transactions + commit queued queries

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