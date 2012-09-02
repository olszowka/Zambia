<?php
function retrieve_select_from_db($trackidlist,$statusidlist,$typeidlist,$sessionid){
  require_once('db_functions.php');
  global $result;
  global $link, $message2; 
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  $query=<<<EOD
SELECT
    sessionid,
    trackname,
    typename,
    title,
    concat(if(left(duration,2)=00, '',
	      if(left(duration,1)=0,
		 concat(right(left(duration,2),1),'hr '),
		 concat(left(duration,2),'hr '))),
	   if(date_format(duration,'%i')=00, '',
	      if(left(date_format(duration,'%i'),1)=0,
		 concat(right(date_format(duration,'%i'),1),'min'),
		 concat(date_format(duration,'%i'),'min'))))
    duration,
    estatten,
    pocketprogtext,
    progguiddesc,
    persppartinfo
  FROM
      Sessions S,
      $ReportDB.Tracks TR,
      $ReportDB.Types TY,
      $ReportDB.SessionStatuses SS
  WHERE
    S.trackid=TR.trackid AND
    S.statusid=SS.statusid AND
    S.typeid=TY.typeid
EOD;

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
         $query.=" AND TY.typeid in ($typeidlist)";
         }

    if (($sessionid!=0) and ($sessionid!='')) {
         $query.=" AND S.sessionid = $sessionid";
         }
//error_log("retrieve: $query");
    prepare_db();
    $result=mysql_query($query,$link);
    if (!$result) {
         $message2=mysql_error($link);
         return (-3);
         }
    return(0);
}
?>
