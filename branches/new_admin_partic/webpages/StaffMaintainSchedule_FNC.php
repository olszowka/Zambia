<?php
function getRoomsForScheduler() {
	global $message_error;
	$queryArray["rooms"]=<<<EOD
		SELECT
				roomid, roomname
			FROM
				Rooms
			WHERE
				is_scheduled = 1
			ORDER BY
				display_order;
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderErrorAjax($message_error);
        exit();
        }
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('xsl/getRoomsForScheduler.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
}