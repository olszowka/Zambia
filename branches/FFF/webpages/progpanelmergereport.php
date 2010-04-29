<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="progpanelmergereport.php";
    $title="Full Participant Schedule for the Program Packet Merge";
    $description="<P>sessionid, room, starttime, duration, (badgeid, pubsname, mod)</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1, GENCSV=0";

//$query="set group_concat_max_len=25000;"
    $query = <<<EOD
SELECT
    POS.sessionid, 
    roomname, 
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') starttime, 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    trackname, 
    title, 
    group_concat( pubsname, ' MAKEMEACOMMA ', if(moderator=1,'M',''), ' MAKEMEACOMMA ' order by moderator, pubsname) panelinfo,
    pubstatusname
  FROM 
      Rooms R, 
      Sessions S, 
      Schedule SCH 
    LEFT JOIN ParticipantOnSession POS on SCH.sessionid=POS.sessionid
    LEFT JOIN CongoDump C on C.badgeid=POS.badgeid
    LEFT JOIN Participants P on P.badgeid=POS.badgeid,
      Tracks T,
      PubStatuses PUB
  WHERE
    S.sessionid=SCH.sessionid and
    POS.sessionid=S.sessionid and
    T.trackid=S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid and
    PUB.pubstatusid = S.pubstatusid
  GROUP BY
    POS.sessionid 
  ORDER BY
    pubsname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
