<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessionintassncountsreport.php";
    $title="Assigned, Interested and Not-scheduled Report";
    $description="<P>These are sessions that are in need of a home in the schedule.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    if ((num_int is NULL), 0, num_int) as Intr,
    if ((num_assigned is NULL), 0, num_assigned) as Assn,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid, 
    concat('<a href=EditSession.php?id=',S.sessionid,'>',title,'</a>') Title,
    trackname, 
    typename
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
                   sessionid, count(badgeid) as num_int 
                 FROM
	             ParticipantSessionInterest
                 GROUP BY
                   sessionid) B on B.sessionid=S.sessionid
    LEFT JOIN Rooms R on R.roomid=SCH.roomid 
  WHERE
    T.trackid=S.trackid and
    Y.typeid=S.typeid and
    D.divisionid=S.divisionid and
    D.divisionname = 'Programming' and
    SCH.sessionid is NULL
  HAVING
    Intr>=4
  ORDER BY
    Intr DESC,
    Assn DESC
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
