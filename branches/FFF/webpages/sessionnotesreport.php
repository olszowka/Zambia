<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessionnotesreport.php";
    $title="Session Notes";
    $description="<P>Interesting info on a Session for sessions whose status is one of EditMe, Brainstorm, Vetted, Assigned, or Scheduled.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as 'Session<BR>id',
    Trackname, 
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    if (invitedguest,'yes','no') as 'Invited Guest?',
    servicenotes as 'Hotel and Tech notes', 
    notesforprog as 'Notes for Programming' 
  FROM
      Tracks T, 
      Sessions S, 
      SessionStatuses SS 
  WHERE
    T.trackid=S.trackid and
    SS.statusid=S.statusid and
    SS.statusname in ('EditMe', 'Brainstorm', 'Vetted', 'Assigned', 'Scheduled') and
    (invitedguest=1 or notesforprog is not NULL or servicenotes is not NULL)
  ORDER BY
   S.sessionid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
