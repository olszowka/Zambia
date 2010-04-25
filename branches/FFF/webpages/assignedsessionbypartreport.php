<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="assignedsessionbypartreport.php";
    $title="Assigned Session by Participant";
    $description="<P>Shows who has been assigned to each session ordered by badgeid.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1";

    $query = <<<EOD
SELECT
    P.Badgeid, 
    P.Pubsname, 
    if ((moderator=1), 'Yes', ' ') as 'Moderator',
    concat('<a href=StaffAssignParticipants.php?selsess=',Sessions.sessionid,'>', Sessions.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',Sessions.sessionid,'>',Sessions.title,'</a>') Title
  FROM
      ParticipantOnSession, 
      Sessions, 
      Participants P
  WHERE
    ParticipantOnSession.badgeid=P.badgeid and
    ParticipantOnSession.sessionid=Sessions.sessionid 
  ORDER BY
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
