<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessionedithistoryallreport.php";
    $title="Session Edit History Report - All";
    $description="<P>For each session, show the entire edit history.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1";

    $query = <<<EOD
SELECT
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    T.trackname as 'Track', 
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title, SS.statusname as 'Current<BR>Status',
    timestamp as 'When', 
    concat(name,' (', email_address,') ') as 'Who', 
    concat(SEC.description, ' ',SS2.statusname) as 'What' 
  FROM
      Sessions S, 
      Tracks T, 
      SessionStatuses SS, 
      SessionEditHistory SEH, 
      SessionEditCodes SEC, 
      SessionStatuses SS2 
  WHERE
    S.trackid=T.trackid and
    S.statusid = SS.statusid and
    S.sessionid = SEH.sessionid and
    SEH.sessioneditcode=SEC.sessioneditcode and
    SS2.statusid=SEH.statusid and
    S.statusid >= 1 and
    S.statusid <= 7 and
  ORDER BY
    S.sessionid, 
    SEH.timestamp
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
