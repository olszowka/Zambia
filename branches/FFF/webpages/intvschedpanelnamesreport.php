<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="intvschedpanelnamesreport.php";
    $title="Interest v Schedule - sorted by pubsname";
    $description="<P>Show who is interested in each panel and if they are assigned to it.  Also show the scheduling information (sorted by pubsname).</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT 
    concat(X.pubsname, '(', X.badgeid, ')') as Pubsname,
    X.trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',X.sessionid,'>', X.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',X.sessionid,'>',X.title,'</a>') Title,
    X.rank as Rank,
    (if (X.assigned is NULL,' ','yes')) as 'Asgn?', 
    (if (moderator is NULL or moderator=0,' ','yes')) as 'Mod?', 
    concat('<a href=MaintainRoomSched.php?selroom=',Y.roomid,'>', Y.roomname,'</a>') as Roomname,
    DATE_FORMAT(ADDTIME('$ConStartDatim',Y.starttime),'%a %l:%i %p') as 'Start Time' 
    FROM 
        (SELECT
             PI.badgeid,
             PI.pubsname,
             PI.sessionid,
             POS.sessionid as assigned,
             moderator,
             title,
             trackname,
             rank
           FROM
               (SELECT
                    T.trackname,
                    S.title,
                    S.sessionid,
                    P.badgeid,
                    P.pubsname,
                    PSI.rank
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
    substring_index(pubsname,' ',-1),
    pubsname,
    trackname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
