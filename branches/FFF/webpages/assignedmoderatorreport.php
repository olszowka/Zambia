<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="assignedmoderatorreport.php";
    $title="Assigned Moderator by Session";
    $description="<P>Shows who has been assigned to moderate each session (sorted by track then sessionid).</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1";

    $query = <<<EOD
SELECT
    Trackname,
    P.Pubsname, 
    P.Badgeid, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',title,'</a>') as Title,
    Statusname 
  FROM
      ParticipantOnSession, 
      Sessions S, 
      Participants P,
      Tracks, 
      SessionStatuses 
  WHERE
    ParticipantOnSession.badgeid=P.badgeid and
    ParticipantOnSession.sessionid=S.sessionid and
    Tracks.trackid=S.trackid and
    S.statusid=SessionStatuses.statusid and
    moderator=1 
  ORDER BY
    trackname, 
    S.sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
