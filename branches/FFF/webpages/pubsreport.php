<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="pubsreport.php";
    $title="Report for Pubs";
    $description="<P>Report for Paul's Pocket Program.</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1";

    $query = <<<EOD
SELECT
    S.sessionid, 
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') as Day, 
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Time', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    roomname, 
    trackname as TRACK, 
    typename as TYPE,
    K.kidscatname,
    title, 
    progguiddesc as 'Long Text', 
    group_concat(' ',pubsname, if (moderator=1,' (m)','')) as 'PARTIC' 
  FROM 
      Rooms R, 
      KidsCategories K,
      Sessions S, 
      Tracks T,
      Types Ty, 
      Schedule SCH 
    LEFT JOIN ParticipantOnSession POS on SCH.sessionid=POS.sessionid 
    LEFT JOIN Participants P on POS.badgeid=P.badgeid
  WHERE
    R.roomid = SCH.roomid and
    K.kidscatid=S.kidscatid and
    SCH.sessionid = S.sessionid and
    T.trackid=S.trackid and
    S.typeid=Ty.typeid and
    S.pubstatusid = 2
  GROUP BY
    SCH.sessionid 
  ORDER BY
    SCH.starttime, 
    R.roomname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
