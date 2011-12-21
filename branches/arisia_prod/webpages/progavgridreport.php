<?php
	global $message_error;
    $title="Program AV Grid";
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="progavgridreport.php";
    $queryArray["rooms"] = <<<EOD
SELECT
		R.roomname,
		R.roomid
	FROM
		Rooms R
	WHERE
		R.roomid in
			(SELECT DISTINCT SCH.roomid
				FROM
						Schedule SCH
				   JOIN Sessions S USING (sessionid)
				   JOIN SessionHasService SHS USING (sessionid)
				WHERE
					SHS.serviceid in (2,7)
			)
	ORDER BY
        R.display_order;
EOD;
	$queryArray["times"] = <<<EOD
SELECT DISTINCT DATE_FORMAT(ADDTIME("$ConStartDatim",SCH.starttime),"%a %l:%i %p") as starttimeFMT, SCH.starttime
	FROM
			Schedule SCH
	   JOIN Sessions S USING (sessionid)
	   JOIN SessionHasService SHS USING (sessionid)
	WHERE
		SHS.serviceid in (2,7)
	ORDER BY
		SCH.starttime
EOD;
	$queryArray["sessions"] = <<<EOD
SELECT SCH.starttime, SCH.sessionid, SCH.roomid, DATE_FORMAT(S.duration,"%l:%i") as duration, S.title, TR.trackname, TY.typename 
	FROM
			Schedule SCH
	   JOIN Sessions S USING (sessionid)
	   JOIN SessionHasService SHS USING (sessionid)
	   JOIN Tracks TR USING (trackid)
	   JOIN Types TY USING (typeid)
	WHERE
		SHS.serviceid in (2,7)
	ORDER BY
		SCH.starttime
EOD;
	$queryArray["services"] = <<<EOD
SELECT SCH.sessionid, SVCS.servicename 
	FROM
			Schedule SCH
	   JOIN SessionHasService SHS USING (sessionid)
	   JOIN Services SVCS USING (serviceid)
	WHERE
		SHS.serviceid in (2,7)
	ORDER BY
		SCH.sessionid
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
	    exit();
	    }
	staff_header($title);
	date_default_timezone_set('US/Eastern');
	echo "<p align=center> Generated: ".date("D M j G:i:s T Y")."</p>\n";
	echo "<p>All scheduled services in grid format</p>\n";
	echo "<p style=\"font-size:10.5px\">Hover over sessionid for more info.</p>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('reportxsl/progavgrid.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers does not support empty div, iframe, script and textarea tags
    staff_footer();
?>
