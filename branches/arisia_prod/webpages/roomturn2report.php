<?php
	global $message_error;
	$title = "Room Turn Report 2";
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
	$_SESSION['return_to_page']="roomturn2report.php";
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
	$queryArray["report"]=<<<EOD
		SELECT
		            DATE_FORMAT(ADDTIME("$ConStartDatim",SCH.starttime),"%a %l:%i %p") AS starttime,
		            SCH.roomid, R.roomname, S.sessionid, S.title, RS.roomsetname, S.servicenotes
		    FROM
		            Schedule SCH
		        JOIN
		            Sessions S USING (sessionid)
		        JOIN        
		            Rooms R USING (roomid)
		        JOIN
		            RoomSets RS USING (roomsetid)
		    WHERE
		           S.typeid=3
		        OR S.trackid=14
		    ORDER BY
		        R.display_order, SCH.starttime;
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	date_default_timezone_set('US/Eastern');
	echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
	echo "<P>List of all Sessions with track or type of \"room turn\" (Sorted by room, then time.) [On Demand]</P>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('reportxsl/roomturn2.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers does not support empty div, iframe, script and textarea tags
	staff_footer();
?>
