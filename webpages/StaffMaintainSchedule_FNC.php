<?php
// Copyright (c) 2015-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
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
	if (($resultXML = mysql_query_XML($queryArray)) === false) {
	    RenderErrorAjax($message_error);
        exit();
    }
    RenderXSLT('getRoomsForScheduler.xsl', array(), $resultXML);
}