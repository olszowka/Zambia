<?php
function retrieve_select_from_db($trackidlist,$statusidlist,$typeidlist,$sessionid,$divisionid,$searchtitle){
    global $result;
    global $link, $message2; 
    require_once('db_functions.php');

    $query="SELECT sessionid, trackname, typename, title, concat( if(left(duration,2)=00, '', if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), concat(left(duration,2),'hr '))), if(date_format(duration,'%i')=00, '', if(left(date_format(duration,'%i'),1)=0, concat(right(date_format(duration,'%i'),1),'min'), concat(date_format(duration,'%i'),'min')))) duration, estatten, progguiddesc, persppartinfo from Sessions, Tracks, Types, SessionStatuses WHERE Sessions.trackid=Tracks.trackid AND Sessions.statusid=SessionStatuses.statusid AND Sessions.typeid=Types.typeid"; 

// The following three lines are for debugging only
//    error_log("zambia - retrieve: trackidlist: $tracklist");
//    error_log("retrieve: statusid: $status");
//    error_log("retrieve: typeid: $type");

    if (($trackidlist!=0) and ($trackidlist!="")) {
         $query.=" AND Tracks.trackid in ($trackidlist)";
         }

    if (($statusidlist!=0) and ($statusidlist!='')) {
         $query.=" AND SessionStatuses.statusid in ($statusidlist)";
         }

    if (($typeidlist!=0) and ($typeidlist!='')) {
         $query.=" AND Sessions.typeid in ($typeidlist)";
         }

    if (($sessionid!=0) and ($sessionid!='')) {
         $query.=" AND Sessions.sessionid = $sessionid";
         }

    if (($divisionid!=0) and ($divisionid!='')) {
         $query.=" AND Sessions.divisionid = $divisionid";
         }

    if ($searchtitle!='') {
         $searchtitle=mysql_real_escape_string($searchtitle,$link);
         $query.=" AND Sessions.title like \"%$searchtitle%\"";
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
