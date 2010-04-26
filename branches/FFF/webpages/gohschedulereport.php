<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="gohschedulereport.php";
    $title="GoH Schedule";
    $description="<P>The GoH schedules.</P>\n";
    $additionalinfo="";
    $indicies="GOHWANTS=1, PROGWANTS=1";
    $GohBadgeList=GOH_BADGE_LIST; // make it a variable so it can be substituted

    $query = <<<EOD
SELECT 
    G.pubsname, 
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS duration,
      trackname, 
      concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid, 
      concat('<a href=EditSession.php?id=',S.sessionid,'>',title,'</a>') title,
      if ((S.pubstatusid=1), 'S-O', if((S.pubstatusid=3), 'DNP', ' ')) as 'Pubs Status',
      if ((moderator=1), 'Yes', ' ') as 'moderator'
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    LEFT JOIN Tracks T ON T.trackid=S.trackid
    JOIN Participants G ON G.badgeid = POS.badgeid
  WHERE
    G.badgeid in $GohBadgeList
  ORDER BY
    G.pubsname,
    SCH.starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
