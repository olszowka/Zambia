<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="thsessionroomsetsreport.php";
    $title="Session roomsets";
    $description="<P>What roomsets are we using (Sorted by Room then Time.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, TECHWANTS=1, HOTELWANTS=1";

    $query = <<<EOD
SELECT
    Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    Trackname, 
    S.Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    Roomsetname 
  FROM
      RoomSets RS, 
      Tracks T, 
      Sessions S, 
      Rooms R, 
      Schedule SCH 
  WHERE
    T.trackid=S.trackid and
    S.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid and
    RS.roomsetid=S.roomsetid 
  ORDER BY
    roomname, 
    starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
