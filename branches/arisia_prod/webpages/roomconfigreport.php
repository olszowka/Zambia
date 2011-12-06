<?php
	global $message_error;
	$title = "Room Configuration Report";
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
	$_SESSION['return_to_page']="roomconfigreport.php";
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
	$queryArray["rooms"]=<<<EOD
		SELECT
	            R.roomid, R.roomname, R.height, R.dimensions, R.area, R.function, R.floor, R.notes,
				DATE_FORMAT(ADDTIME('$ConStartDatim',R.opentime1),'%a %l:%i %p') opentime1,
				DATE_FORMAT(ADDTIME('$ConStartDatim',R.closetime1),'%a %l:%i %p') closetime1,
				DATE_FORMAT(ADDTIME('$ConStartDatim',R.opentime2),'%a %l:%i %p') opentime2,
				DATE_FORMAT(ADDTIME('$ConStartDatim',R.closetime2),'%a %l:%i %p') closetime2,
				DATE_FORMAT(ADDTIME('$ConStartDatim',R.opentime3),'%a %l:%i %p') opentime3,
				DATE_FORMAT(ADDTIME('$ConStartDatim',R.closetime3),'%a %l:%i %p') closetime3,
				IF(R.is_scheduled,'Yes','No') AS is_scheduled,
				IF(EXISTS (SELECT * FROM Schedule SCH WHERE SCH.roomid = R.roomid),'Yes','No') AS scheduled
		    FROM
	            Rooms R
		    ORDER BY
		        R.display_order;
EOD;
	$queryArray["roomsets"]=<<<EOD
		SELECT R.roomid, RS.roomsetname, RHS.capacity
			FROM
					 Rooms R
				JOIN RoomHasSet RHS using (roomid)
				JOIN RoomSets RS using (roomsetid)
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	date_default_timezone_set('US/Eastern');
	echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
	echo "<P>List all configurable information associated with rooms. [On Demand]</P>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('reportxsl/roomconfig.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers does not support empty div, iframe, script and textarea tags
	staff_footer();
?>
