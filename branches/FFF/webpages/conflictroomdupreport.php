<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictroomdupreport.php";
    $title="Conflict Report - Room Schedule Overlaps.";
    $description="<P>Find any pairs of sessions whose times overlap in the same room.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname,
    SA.title as 'Title A',
    Asess as 'Sessionid A',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Astart),'%a %l:%i %p') as 'Start Time A',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Aend),'%a %l:%i %p') as 'End Time A',
    SB.title as 'Title B',
    Bsess as 'Sessionid B',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Bstart),'%a %l:%i %p') as 'Start Time B',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Bend),'%a %l:%i %p') as 'End Time B'
  FROM
      Sessions SA,
      Sessions SB,
      Rooms R,
      (SELECT
           A.roomid,
           A.sessionid as Asess,
           A.starttime as Astart,
           ADDTIME(A.starttime, SA.duration) as Aend,
           B.sessionid as Bsess,
           B.starttime as Bstart,
           ADDTIME(B.starttime, SB.duration) as Bend
         FROM
             Schedule A,
             Schedule B,
             Sessions SA,
             Sessions SB
         WHERE
           A.roomid = B.roomid and
           A.starttime<=B.starttime and
           ADDTIME(A.starttime, SA.duration)>B.starttime and
           A.sessionid<>B.sessionid and
           A.sessionid=SA.sessionid and
           B.sessionid=SB.sessionid) as Foo
  WHERE
    Foo.roomid = R.roomid and
    Foo.Asess=SA.sessionid and
    Foo.Bsess=SB.sessionid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
