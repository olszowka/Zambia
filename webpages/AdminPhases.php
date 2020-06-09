<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Administer Phases";
require_once('StaffCommonCode.php');
staff_header($title);
if (isLoggedIn() && $loginPageStatus != 'Login' && may_I("AdminPhases")) {
?>
<h2>Current Zambia Phase Status</h2>
<?php
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

	echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('AdminPhases.xsl', $paramArray, $resultXML);
}
staff_footer();
?>