<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="allroomschedtimereport.php";
    $title="Full Room Schedule by time then room";
    $description="<P>Lists all Sessions Scheduled in all Rooms (includes \"Public\", \"Do Not Print\" and \"Staff Only\").</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, GOHWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname,
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    Function,
    Trackname,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',title,'</a>') as Title,
    PS.pubstatusname as PubStatus,
    group_concat(' ',P.pubsname,' (',P.badgeid,')') as 'Participants'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN PubStatuses PS USING (pubstatusid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime,
    R.roomname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
