<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictpartdupreport.php";
    $title="Conflict Report - Participant Double Booked";
    $description="<P>Find all instances where a participant is scheduled to be in two or more places at once.</P>\n";
    $additionalinfo="<P>Click on the session id to edit the session's volunteer or announcer.</P>";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    concat(P.pubsname, '(', P.badgeid, ')') as Participant,
    ' ',
    TA.trackname as 'Track A', 
    concat('<a href=MaintainRoomSched.php?selroom=',RA.roomid,'>', RA.roomname,'</a>') as 'Room A', 
    concat('<a href=StaffAssignParticipants.php?selsess=',Asess,'>', Asess,'</a>') as 'Session ID A', 
    DATE_FORMAT(ADDTIME('$ConStartDatim',Astart),'%a %l:%i %p') as 'Start Time A', 
    left(Adur,5) as 'Dur A',
    ' ',
    TB.trackname as 'Track B', 
    concat('<a href=MaintainRoomSched.php?selroom=',RB.roomid,'>', RB.roomname,'</a>') as 'Room B', 
    concat('<a href=StaffAssignParticipants.php?selsess=',Bsess,'>', Bsess,'</a>') as 'Session ID B', 
    DATE_FORMAT(ADDTIME('$ConStartDatim',Bstart),'%a %l:%i %p') as 'Start Time B',
    left(Bdur,5) as 'Dur B'
  FROM
      Rooms RA, 
      Rooms RB, 
      Tracks TA, 
      Tracks TB, 
      Participants P,
      (SELECT
          POSA.badgeid, 
          SCHA.roomid AS Aroom, 
          SCHA.sessionid AS Asess, 
          SCHA.starttime AS Astart, 
          ADDTIME(SCHA.starttime, SA.duration) AS Aend, 
          SA.trackid AS Atrack, 
	  SA.duration AS Adur,
          SCHB.sessionid AS Bsess, 
          SCHB.roomid AS Broom, 
          SCHB.starttime AS Bstart, 
          ADDTIME(SCHB.starttime, SB.duration) AS Bend, 
          SB.trackid AS Btrack,
	  SB.duration AS Bdur
        FROM ParticipantOnSession POSA, 
            ParticipantOnSession POSB, 
            Schedule SCHA, 
            Schedule SCHB, 
            Sessions SA, 
            Sessions SB 
        WHERE
          POSA.sessionid = SA.sessionid and
          SCHA.sessionid=POSA.sessionid and
          POSB.sessionid = SB.sessionid and
          SCHB.sessionid=POSB.sessionid and
          POSA.badgeid=POSB.badgeid and
          (SCHA.starttime<SCHB.starttime or 
           (SCHA.starttime=SCHB.starttime and 
            SCHA.sessionid<SCHB.sessionid)) and
          ADDTIME(SCHA.starttime, SA.duration)>SCHB.starttime and
          POSA.sessionid<>POSB.sessionid) as Foo 
  WHERE
    Aroom=RA.roomid and
    P.badgeid=Foo.badgeid and
    Broom=RB.roomid and
    TA.trackid=Atrack and
    TB.trackid=Btrack
  ORDER BY
    cast(P.badgeid as unsigned), 
    Astart
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
