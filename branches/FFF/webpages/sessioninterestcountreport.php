<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessioninterestcountreport.php";
    $title="Session Interest Report (counts)";
    $description="<P>For each session, show number of participants who have put it on their interest list. (Excludes invited guest sessions.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    T.trackname as Track, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as 'Session<BR>ID',
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    count(PSI.badgeid) as 'Number<BR>of<BR>Participants' 
  FROM
      Sessions AS S 
    JOIN Tracks AS T ON S.trackid=T.trackid 
    LEFT JOIN ParticipantSessionInterest AS PSI ON S.sessionid=PSI.sessionid 
  WHERE
    T.selfselect=1 and
    statusid in (2,3,7) 
  GROUP BY
    T.trackid, 
    S.sessionid 
  ORDER BY
    T.display_order, 
    S.sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
