<?php
// Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function retrieve_select_from_db($trackidlist, $statusidlist, $typeidlist, $sessionid, $divisionid, $searchtitle) {
    global $linki;
    require_once('db_functions.php');
    $ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
    $query = <<<EOB
SELECT
		sessionid,
		trackname,
		typename,
		title,
		concat( if(left(duration,2)=00, '', if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), 
			concat(left(duration,2),'hr '))), if(date_format(duration,'%i')=00, '', if(left(date_format(duration,'%i'),1)=0, 
			concat(right(date_format(duration,'%i'),1),'min'), concat(date_format(duration,'%i'),'min')))) AS duration,
		estatten,
		progguiddesc,
		persppartinfo,
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime,
		roomname,
		SS.statusname
	FROM 
				Sessions S
		   JOIN Tracks TR using (trackid)
		   JOIN Types TY using (typeid)
		   JOIN SessionStatuses SS using (statusid)
	  LEFT JOIN Schedule SCH using (sessionid)
	  LEFT JOIN Rooms R using (roomid)
	WHERE 
		1 = 1
EOB;
    if (($trackidlist != 0) and ($trackidlist != "")) {
        $query .= " AND TR.trackid in ($trackidlist)";
    }

    if (($statusidlist != 0) and ($statusidlist != '')) {
        $query .= " AND SS.statusid in ($statusidlist)";
    }

    if (($typeidlist != 0) and ($typeidlist != '')) {
        $query .= " AND S.typeid in ($typeidlist)";
    }

    if (($sessionid != 0) and ($sessionid != '')) {
        $query .= " AND S.sessionid = $sessionid";
    }

    if (($divisionid != 0) and ($divisionid != '')) {
        $query .= " AND S.divisionid = $divisionid";
    }

    if ($searchtitle != '') {
        $searchtitle = mysqli_real_escape_string($linki, $searchtitle);
        $query .= " AND S.title like \"%$searchtitle%\"";
    }
    return(mysqli_query_exit_on_error($query));
}

?>
