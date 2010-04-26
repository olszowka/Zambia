<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictpartatimereport.php";
    $title="Conflict Report - Participants Scheduled Outside Available Times";
    $description="<P>Show all participant-sessions scheduled outside set of times participant has listed as being available.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT 
    FOO.badgeid, 
    P.pubsname, 
    TR.trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',FOO.sessionid,'>', FOO.sessionid,'</a>') as Sessionid, 
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname, 
    DATE_FORMAT(ADDTIME('2009-01-16 00:00:00',FOO.starttime),'%a %l:%i %p') as 'Start Time', 
    DATE_FORMAT(ADDTIME('2009-01-16 00:00:00',FOO.endtime),'%a %l:%i %p') as 'End Time', 
    FOO.hours as 'Ttl. Hours Avail.' 
  FROM 
      (SELECT
           SCHD.badgeid, 
           SCHD.trackid, 
           SCHD.sessionid, 
           SCHD.starttime, 
           SCHD.endtime, 
           SCHD.roomid, 
           PAT.availabilitynum, 
           HRS.hours 
         FROM
             (SELECT
                  POS.badgeid, 
                  SCH.sessionid, 
                  SCH.starttime, 
	          SCH.roomid, 
	          ADDTIME(SCH.starttime,S.duration) as endtime, 
	          S.trackid 
                FROM
                    Schedule SCH, 
                    ParticipantOnSession POS, 
	            Sessions S 
                WHERE
                  SCH.sessionid = POS.sessionid and
                  SCH.sessionid = S.sessionid) as SCHD 
           LEFT JOIN ParticipantAvailabilityTimes PAT on SCHD.badgeid = PAT.badgeid and
             SCHD.starttime>=PAT.starttime and
             SCHD.endtime<=PAT.endtime 
           LEFT JOIN (SELECT
                          badgeid,
                          sum(hour(subtime(endtime,starttime))) as hours 
                        FROM
                            ParticipantAvailabilityTimes 
                        GROUP BY
		          badgeid) as HRS on SCHD.badgeid=HRS.badgeid 
                        HAVING
                          PAT.availabilitynum is null) as FOO, 
    Tracks TR, 
    Participants P, 
    Rooms R 
  WHERE
    FOO.badgeid = P.badgeid and
    FOO.trackid = TR.trackid and
    FOO.roomid = R.roomid 
  HAVING
    FOO.hours is not NULL 
  ORDER BY
    cast(FOO.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
