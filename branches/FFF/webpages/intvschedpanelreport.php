<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="intvschedpanelreport.php";
    $title="Interest v Schedule - sorted by track, then title";
    $description="<P>Show who is interested in each panel and if they are assigned to it.  Also show the scheduling information.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    X.trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',X.sessionid,'>', X.sessionid,'</a>') as Sessionid,
    X.title, 
    X.badgeid, 
    X.pubsname,  
    (if (X.assigned is NULL,'no','yes')) as 'Assigned?', 
    concat('<a href=MaintainRoomSched.php?selroom=',Y.roomid,'>', Y.roomname,'</a>') as Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',Y.starttime),'%a %l:%i %p') as 'Start Time' 
  FROM
      (SELECT
           PI.badgeid,
           PI.pubsname,
           PI.sessionid,
           POS.sessionid as assigned,
           title,
           trackname 
         FROM
             (SELECT
                  T.trackname,
                  S.title,
                  S.sessionid,
                  P.badgeid,
                  P.pubsname 
               FROM
                   Tracks T,
                   ParticipantSessionInterest PSI, 
                   Participants P,
                   Sessions S
               WHERE
                 S.trackid=T.trackid and
                 P.interested=1 and
                 P.badgeid=PSI.badgeid and
                 S.sessionid=PSI.sessionid ) PI 
                   left join ParticipantOnSession POS 
                          on POS.badgeid=PI.badgeid and POS.sessionid=PI.sessionid) X 
           LEFT JOIN (SELECT
                          SCH.starttime,
                          R.roomname,
                          R.roomid,
                          SCH.sessionid 
                       FROM 
                           Schedule SCH,
                           Rooms R 
                       WHERE
                           R.roomid=SCH.roomid) as Y on X.sessionid=Y.sessionid 
  ORDER BY
    X.trackname,
    X.title
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);

