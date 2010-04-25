<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="allassignedreport.php";
    $title="All Sessions that are assigned";
    $description="<P>Who is assigned to what.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    trackname as Trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    group_concat(' ',P.pubsname,' (',P.badgeid,')') as 'Participants',
    GROUP_CONCAT(DISTINCT if((POS.moderator=1),P.pubsname,'') SEPARATOR ' ') as 'Moderator', 
    GROUP_CONCAT(DISTINCT if((POS.volunteer=1),P.pubsname,'') SEPARATOR ' ') as 'Volunteer', 
    GROUP_CONCAT(DISTINCT if((POS.announcer=1),P.pubsname,'') SEPARATOR '') as 'Announcer' 
  FROM
      Sessions S, 
      Participants P, 
      ParticipantOnSession POS, 
      Tracks 
  WHERE
    P.badgeid=POS.badgeid AND
    POS.sessionid=S.sessionid AND
    Tracks.trackid=S.trackid 
  GROUP BY
    S.sessionid 
  ORDER BY
    Trackname,
    S.sessionid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
