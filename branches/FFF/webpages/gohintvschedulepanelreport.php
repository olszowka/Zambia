<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="gohintvschedulepanelreport.php";
    $title="Interest v Schedule - sorted by GoHs";
    $description="<P>For each GoH, show which panels (but not Events) they are interested in,  and if they are assigned to it.  Also show the scheduling information.</P>\n";
    $additionalinfo="";
    $indicies="GOHWANTS=1, PROGWANTS=1";
    $GohBadgeList=GOH_BADGE_LIST; // make it a variable so it can be substituted

    $query = <<<EOD
SELECT 
    concat(X.pubsname, ' (', X.badgeid, ')') as Pubsname,
    X.trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',X.sessionid,'>', X.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',X.sessionid,'>',X.title,'</a>') as Title,
    (if (X.assigned is NULL,' ','yes')) as 'Assigned?', (if (moderator is NULL or moderator=0,' ','yes')) as 'Moderator?', concat('<a href=MaintainRoomSched.php?selroom=',Y.roomid,'>', Y.roomname,'</a>') as Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',Y.starttime),'%a %l:%i %p') as 'Start Time' 
  FROM 
      (SELECT
           PI.badgeid,
           PI.pubsname,
           PI.sessionid,
           POS.sessionid as assigned,
           moderator,
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
		 P.badgeid in $GohBadgeList and
                 P.badgeid=PSI.badgeid and 
                 S.sessionid=PSI.sessionid) PI 
           LEFT JOIN ParticipantOnSession POS on POS.badgeid=PI.badgeid and POS.sessionid=PI.sessionid) X 
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
    badgeid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);

