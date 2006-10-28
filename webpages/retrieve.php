<?php
function retrieve_select_from_db($track,$status){
    global $result;
    global $link, $message2; 
    require_once('db_functions.php');

    $query="SELECT sessionid, trackname, title, concat( if(left(duration,2)=00, '', if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), concat(left(duration,2),'hr '))), if(date_format(duration,'%i')=00, '', if(left(date_format(duration,'%i'),1)=0, concat(right(date_format(duration,'%i'),1),'min'), concat(date_format(duration,'%i'),'min')))) duration, estatten, pocketprogtext,persppartinfo from Sessions, Tracks, SessionStatuses WHERE Sessions.trackid=Tracks.trackid AND Sessions.statusid=SessionStatuses.statusid "; 

// The following two lines are for debugging only
//  echo "trackid: $track\n";
//  echo "statusid: $status\n";
// The two commented-out lines below with echo are for debugging only

  if (($track!=0) and ($track!="")) {
//    echo "track set\n";
    $query.=" AND Tracks.trackid=\"".$track."\"";
  }

  if (($status!=0) and ($status!='')) {
//    echo "status set\n";
    $query.=" AND SessionStatuses.statusid=\"".$status."\"";
    }

  prepare_db();
  $result=mysql_query($query,$link);
  if (!$result) {
    $message2=mysql_error($link);
    return (-3);
    }
    return(0);
}
?>
