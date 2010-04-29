<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="assignedsessioncountsreport.php";
    $title="Assigned Session by Session (counts)";
    $description="<P>How many people are assinged to each session? (Sorted by track then sessionid.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1";

    $query = <<<EOD
SELECT 
    Trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',Sessions.sessionid,'>', Sessions.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',Sessions.sessionid,'>',Sessions.title,'</a>') Title,
    Statusname, 
    count(badgeid) as NumAssigned 
  FROM
      ParticipantOnSession, 
      Sessions, 
      Tracks, 
      SessionStatuses  
  WHERE
    ParticipantOnSession.sessionid=Sessions.sessionid and
    Tracks.trackid=Sessions.trackid and
    Sessions.statusid=SessionStatuses.statusid
  GROUP BY
    sessionid 
  ORDER BY
    trackname,
    sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
