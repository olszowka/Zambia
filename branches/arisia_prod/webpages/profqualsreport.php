<?php
	global $message_error;
	$title = "Professional Credentials Report";
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $query="SET group_concat_max_len=25000";
    if (!mysql_query_with_error_handling($query)) {
        RenderError($title,$message_error);
        exit();
        }
	$queryArray["participants"]=<<<EOD
	SELECT
			CONCAT(CD.lastname,", ",CD.firstname) AS name, P.pubsname, CD.email,
			CD.badgeid 
		FROM
				CongoDump CD
		   JOIN Participants P USING (badgeid)
		WHERE
			P.interested = 1
		ORDER BY
			CD.lastname, CD.firstname
EOD;
	$queryArray["credentials"]=<<<EOD
	SELECT
			P.badgeid, C.credentialname
		FROM
				Participants P
		   JOIN ParticipantHasCredential PHC USING (badgeid)
		   JOIN Credentials C USING (credentialid)
		WHERE
			P.interested = 1
		ORDER BY
			P.badgeid, C.display_order
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	date_default_timezone_set('US/Eastern');
	echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
	echo "<P>List of all \"interested\" participants and their self-reported professional qualifications.</P>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('xsl/profqualsreport.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers does not support empty div, iframe, script and textarea tags
	staff_footer();
?>
