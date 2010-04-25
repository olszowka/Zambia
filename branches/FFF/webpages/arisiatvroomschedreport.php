<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="arisiatvroomschedreport.php";
    $title="Arisia TV by time.";
    $description="<P>Just things in TV room.</P>\n";
    $additionalinfo="";
    $indicies="ARISIATVWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    trackname, 
    S.sessionid,
    title,
    group_concat(' ',pubsname,' (',P.badgeid,')') as 'Participants'
  FROM
      Tracks T,
      Rooms R,
      Sessions S,
      Schedule SCH
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid = POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid = P.badgeid
  WHERE
    T.trackid = S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid and
    roomname in ('ArisiaTV')
  GROUP BY
    SCH.sessionid
  ORDER BY
    SCH.starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
