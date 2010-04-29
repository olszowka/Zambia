<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="thsessionservicesservicereport.php";
    $title="Session Services by Service";
    $description="<P>Which Session needs which Services? (Sorted by service then time.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, TECHWANTS=1, HOTELWANTS=1";

    $query = <<<EOD
SELECT
    X.Servicename as Service,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    Roomname,
    Trackname, 
    X.Sessionid,
    concat('<a href=EditSession.php?id=',X.sessionid,'>',X.title,'</a>') Title
  FROM
      (SELECT
           duration, 
           trackname, 
           S.sessionid,
           title,
           servicename 
         FROM
             Tracks T, 
             Sessions S, 
             SessionHasService SF, 
             Services F 
         WHERE
           T.trackid=S.trackid and
           S.sessionid=SF.sessionid and
           F.serviceid=SF.serviceid) X,
      Rooms R, 
      Schedule SCH 
  WHERE
    X.sessionid=SCH.sessionid and
    SCH.roomid=R.roomid 
  ORDER BY
    X.servicename, 
    starttime,
    roomname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
