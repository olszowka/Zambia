<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictschedschedreport.php";
    $title="Conflict Report - Schedule but not";
    $description="<P>These are sessions that are either in the grid and not set as scheduled or they are set as scheduled and not in the grid.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    if ((SCH.sessionid is NULL),'no','yes') in_grid, 
    if(statusid=3,'yes','no') status_sched 
  FROM
      Sessions S 
    LEFT JOIN Schedule SCH on S.sessionid=SCH.sessionid 
  HAVING
    (in_grid='no' and status_sched='yes') or
    (in_grid='yes' and status_sched='no')
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
