<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="allroomschedtrackreport.php";
    $title="Full Room Schedule by track then time.";
    $description="<P>Lists all Sessions Scheduled in all Rooms.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, GOHWANTS=1";

    $query = <<<EOD
SELECT
    Trackname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname,
    Function,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    group_concat(' ',pubsname,' (',P.badgeid,')') as 'Participants'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
  GROUP BY
    SCH.scheduleid
  ORDER BY 
    T.trackname,
    SCH.starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
