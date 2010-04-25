<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="assignedsessionreport.php";
    $title="Assigned Session by Session";
    $description="<P>Shows who has been assigned to each session. (Sorted by track and then sessionid.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1";

    $query = <<<EOD
SELECT 
    Trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    P.pubsname,
    if ((moderator=1), 'Yes', ' ') as 'Moderator',
    Statusname 
  FROM
      ParticipantOnSession POS,
      Sessions S,
      Participants P,
      Tracks T,
      SessionStatuses SS
  WHERE
    POS.badgeid=P.badgeid and
    POS.sessionid=S.sessionid and
    T.trackid=S.trackid and
    S.statusid=SS.statusid 
  ORDER BY
    trackname, 
    S.sessionid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
