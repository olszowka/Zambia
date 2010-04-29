<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="assignedsessionbypartdiffreport.php";
    $title="Differential Assigned Session by Participant";
    $description="<P>Recent changes to whom has been assigned to each session ordered by time, then badgeid.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, GOHWANTS=1, PUBSWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    Sessions.sessionid as SessionId, 
    title, 
    if ((moderator=1), 'Yes', ' ') as 'moderator',
    POS.ts as changed
  FROM
      ParticipantOnSession POS, 
      Sessions, 
      Participants P 
  WHERE
    POS.badgeid=P.badgeid and
    POS.sessionid=Sessions.sessionid and
    POS.ts>'2009-1-7 13:50:00'
  ORDER BY
    POS.ts,
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
