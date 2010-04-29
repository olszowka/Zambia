<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictnotregreport.php";
    $title="Conflict Report - Not Registered";
    $description="<P>This is a report of participants sorted by number of panels they are on that are actually running, with some registration information.  It is useful for cons that comp program participants based on a minimum number of panels.  In this case, this report helps make sure people get their comps.  Also, participants who have not earned a comp may need some kind of consideration.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1, REGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    if ((regtype is NULL), ' ', regtype) as 'regtype', 
    if ((assigned is NULL), '0', assigned) as 'assigned'
  FROM
      CongoDump C,
      Participants P 
    left join (SELECT
                   POS.badgeid, 
                   count(POS.sessionid) as assigned 
                 FROM
                     ParticipantOnSession POS,
                     Schedule S
                 WHERE
                   S.sessionid=POS.sessionid 
                 GROUP BY
                   badgeid) X on P.badgeid=X.badgeid 
  WHERE
    C.badgeid=P.badgeid and
    interested!=2 
  ORDER BY 
    regtype, 
    cast(assigned as unsigned) desc,
    substring_index(pubsname,' ',-1)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
