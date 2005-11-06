<?php
function retrieve_select_from_db($track,$status){
    global $result;
    global $link, $message2; 
    require_once('db_functions.php');

    $query="SELECT sessionid, trackname, title, duration, estatten, pocketprogtext,persppartinfo from Sessions, Tracks, SessionStatuses WHERE Sessions.trackid=Tracks.trackid AND Sessions.statusid=SessionStatuses.statusid ";


  echo "trackid: $track\n";
  echo "statusid: $status\n";

  if (($track!=0) and ($track!="")) {
    echo "track set\n";
    $query.=" AND Tracks.trackid=\"".$track."\"";
  }

  if (($status!=0) and ($status!='')) {
    echo "status set\n";
    $query.=" AND SessionStatuses.statusid=\"".$status."\"";
    }

  prepare_db();
  $result=mysql_query($query,$link);
  if (!$result) {
    $message2=mysql_error($link);
    return (-3);
    }

  $rows=mysql_num_rows($result);
  if ($rows!=1) {
    $message2=$rows;
    return (-2);
    }
}
?>
