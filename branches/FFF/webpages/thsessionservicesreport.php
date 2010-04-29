<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="thsessionservicesreport.php";
    $title="Session Services";
    $description="<P>Which Session needs which Services? (Sorted by room then time.)</P>\n";
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
    X.Sessionid,
    concat('<a href=EditSession.php?id=',X.sessionid,'>',X.title,'</a>') Title,
    X.Servicename 
  FROM
      (SELECT
           trackname, 
           S.sessionid, 
           title,
           duration,
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
    roomname, 
    starttime;
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
