<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictsessdupreport.php";
    $title="Conflict Report - Duplicate Session";
    $description="<P>Lists all sessions scheduled more than once.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    S.Sessionid, 
    S.Title, 
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', 
    R.roomname,'</a>') as Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'Start Time' 
  FROM
      Sessions S, 
      Rooms R, 
      Schedule SCH, 
      (SELECT
           sessionid, 
           count(*) as mycount 
         FROM
             Schedule 
         GROUP BY
             sessionid 
         HAVING
             mycount>1) X 
  WHERE
    S.sessionid=X.sessionid and
    S.sessionid=SCH.sessionid and
    R.roomid = SCH.roomid
  ORDER BY
    S.sessionid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
