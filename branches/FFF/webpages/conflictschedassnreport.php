<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictschedassnreport.php";
    $title="Conflict Report - Assigned v. Scheduled issue";
    $description="<P>These are sessions that are either in the grid and have no one assigned or the have people assigned and are not in the grid.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    trackname,
    typename,
    divisionname,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid, 
    concat('<a href=EditSession.php?id=',S.sessionid,'>',title,'</a>') Title,
    if ((SCH.sessionid is NULL), 'no room', concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>')) in_grid, 
    if ((num_assigned is NULL), 0, num_assigned) as num_assigned,
    if ((num_int is NULL), 0, num_int) as num_int
  FROM
      Tracks T, 
      Types Y,
      Divisions D,
      Sessions S 
    LEFT JOIN Schedule SCH on S.sessionid=SCH.sessionid 
    LEFT JOIN (SELECT
                   sessionid, 
                   count(badgeid) as num_assigned 
                 FROM
                     ParticipantOnSession 
                 GROUP BY
                     sessionid) A on A.sessionid=S.sessionid 
    LEFT JOIN (SELECT
                   sessionid,
                   count(badgeid) as num_int 
                 FROM
                     ParticipantSessionInterest
                 GROUP BY
                     sessionid) B on B.sessionid=S.sessionid
    LEFT JOIN Rooms R on R.roomid=SCH.roomid 
  WHERE
    T.trackid=S.trackid and
    Y.typeid=S.typeid and
    D.divisionid=S.divisionid
  HAVING 
    (in_grid='no room' and num_assigned>0) or
    (in_grid!='no room' and (num_assigned<1 or num_assigned is NULL))
  ORDER BY
    num_assigned DESC,
    in_grid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
