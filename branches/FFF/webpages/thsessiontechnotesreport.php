<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="thsessiontechnotesreport.php";
    $title="Session Tech and Hotel notes";
    $description="<P>What notes are in on this panel for tech and hotel? (Sorted by room then time.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, TECHWANTS=1, HOTELWANTS=1";

    $query = <<<EOD
SELECT
    Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start&nbsp;Time', 
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
    Servicenotes 
  FROM
      Tracks T, 
      Sessions S, 
      Rooms R, 
      Schedule SCH 
  WHERE
    T.trackid=S.trackid and
    S.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid and
    S.servicenotes!=' ' 
  ORDER BY
    Roomname, 
    Starttime
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
