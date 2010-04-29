<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="2finalschedbreifreport.php";
    $title="Schedule";
    $description="<P>Below is the Panel, Events, Film, Anime, Video and Arisia TV schedule.</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1, PROGWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    roomname, 
    trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title
  FROM
      Sessions S, 
      Schedule SCH, 
      Tracks T, 
      Rooms R 
  WHERE
    R.roomid=SCH.roomid and
    T.trackid=S.trackid and
    SCH.sessionid = S.sessionid and
    S.pubstatusid = 2
  ORDER BY
    SCH.starttime,
    T.trackname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
