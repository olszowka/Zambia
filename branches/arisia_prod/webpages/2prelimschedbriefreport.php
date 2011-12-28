<?php
	global $message_error;
	$title = "Preliminary Schedule Report";
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="2prelimschedbriefreport.php";
	$queryArray["schedule"]=<<<EOD
SELECT
			DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime,
            T.trackname,
            S.sessionid,
            S.title
	FROM
			Sessions S
	   JOIN Schedule SCH USING (sessionid)
	   JOIN Tracks T USING (trackid)
	WHERE
		S.divisionid = 2
	ORDER BY
		T.trackname,
		SCH.starttime
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	date_default_timezone_set('US/Eastern');
	echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
	echo "<P>Preliminary list of all scheduled \"Programming\" sessions. [On&nbsp;Demand]</P>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('reportxsl/2prelimschedbrief.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers do not support empty div, iframe, script and textarea tags
	staff_footer();
?>
