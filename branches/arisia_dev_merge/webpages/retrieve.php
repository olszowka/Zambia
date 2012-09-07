<?php
function retrieve_select_from_db($trackidlist,$statusidlist,$typeidlist,$sessionid,$divisionid,$searchtitle){
    global $result;
    global $link, $message2;
    require_once('db_functions.php');
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $query=<<<EOB
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
		roomname
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
// The following three lines are for debugging only
//    error_log("zambia - retrieve: trackidlist: $tracklist");
//    error_log("retrieve: statusid: $status");
//    error_log("retrieve: typeid: $type");

    if (($trackidlist!=0) and ($trackidlist!="")) {
         $query.=" AND TR.trackid in ($trackidlist)";
         }

    if (($statusidlist!=0) and ($statusidlist!='')) {
         $query.=" AND SS.statusid in ($statusidlist)";
         }

    if (($typeidlist!=0) and ($typeidlist!='')) {
         $query.=" AND S.typeid in ($typeidlist)";
         }

    if (($sessionid!=0) and ($sessionid!='')) {
         $query.=" AND S.sessionid = $sessionid";
         }

    if (($divisionid!=0) and ($divisionid!='')) {
         $query.=" AND S.divisionid = $divisionid";
         }

    if ($searchtitle!='') {
         $searchtitle=mysql_real_escape_string($searchtitle,$link);
         $query.=" AND S.title like \"%$searchtitle%\"";
         }
    //error_log("retrieve: $query");
    //echo($query." <BR>\n");
    prepare_db();
    $result=mysql_query($query,$link);
    if (!$result) {
         $message2=mysql_error($link);
         return (-3);
         }
    return(0);
}
?>
