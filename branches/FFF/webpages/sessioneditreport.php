<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessioneditreport.php";
    $title="Session Edit History Report";
    $description="<P>Show the most recent edit activity for each session (sorted by time).</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1";

    $query = <<<EOD
SELECT
    SEH2.timestamp as "When",
    concat("<a href=StaffAssignParticipants.php?selsess=",S.sessionid,">", S.sessionid,"</a>") as Sessionid,
    T.trackname as "Track", 
    concat("<a href=EditSession.php?id=",S.sessionid,">",S.title,"</a>") Title,
    SS.statusname as "Current<BR>Status", 
    concat(SEH1.name," (",SEH1.email_address,")") as "Who", 
    SEC.description as "What"
  FROM
      Sessions S, 
      Tracks T, 
      SessionStatuses SS, 
      SessionEditHistory SEH1, 
      SessionEditCodes SEC, 
      (SELECT
           SEH3.sessionid, 
           Max(SEH3.timestamp) as timestamp 
         FROM
             SessionEditHistory SEH3 
         GROUP BY
           SEH3.sessionid) SEH2 
  WHERE
    S.trackid=T.trackid and
    S.sessionid = SEH1.sessionid and
    S.sessionid = SEH2.sessionid and
    SEH1.timestamp = SEH2.timestamp and
    S.statusid = SS.statusid and
    SEH1.sessioneditcode = SEC.sessioneditcode and
    S.statusid >= 1 and
    S.statusid <= 7 
  ORDER BY
    SEH2.timestamp Desc
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
