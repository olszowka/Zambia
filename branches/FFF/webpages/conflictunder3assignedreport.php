<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictunder3assignedreport.php";
    $title="Conflict Report - Scheduled Programming sessions without enough people";
    $description="<P>This report runs against scheduled sessions in division program only.   If these are panels, you need at least 3 people.  All other types require at least 1.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    T.trackname, 
    Y.typename,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid, 
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    if(X.assigned is NULL, 0, X.assigned) assigned, concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname
  FROM
      Sessions  S
    LEFT JOIN (SELECT
                   S1.sessionid, count(badgeid) assigned 
                 FROM
                     Sessions S1 ,
                     ParticipantOnSession POS 
                 WHERE
                     S1.sessionid=POS.sessionid
                 GROUP BY
                     S1.sessionid ) X on S.sessionid=X.sessionid,
      Schedule SCH,
      Tracks T,
      Types Y,
      Divisions D,
      Rooms R
  WHERE
    S.sessionid=SCH.sessionid and
    S.trackid=T.trackid and
    S.typeid=Y.typeid and
    S.divisionid=D.divisionid and
    SCH.roomid=R.roomid and
    S.statusid=3 and
    D.divisionname='Programming' and
    ((Y.typename = 'Panel' and assigned<3) or
     (Y.typename != 'Panel'  and assigned<1) or
     assigned is NULL)
  ORDER BY
    T.trackname,
    S.sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
