<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="allpartschedbyparttimereport.php";
    $title="Full Participant Schedule by time";
    $description="<P>The schedule sorted by participant.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, GOHWANTS=1";

    $query = <<<EOD
SELECT 
    if ((P.pubsname is NULL), ' ', concat(' ',P.pubsname,' (',P.badgeid,')')) as 'Participants', 
    if ((moderator=1),'moderator', ' ') as Moderator,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname,
    Function, 
    Trackname,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title
  FROM 
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid 
    LEFT JOIN Tracks T ON T.trackid=S.trackid 
  WHERE
    S.typeid not in (10, 12)
  ORDER BY
    cast(P.badgeid as unsigned),
    SCH.starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
