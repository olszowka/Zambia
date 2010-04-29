<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="benpalmreport.php";
    $title="Report for Ben and the palm.";
    $description="<P>StartTime Duration Room Track Title Participants.</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') as 'Day',
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Start Time',
    concat('(',left(duration,5),')') Length,
    Roomname,
    trackname as Track,
    Title,
    if(group_concat(pubsname) is NULL,'',group_concat(pubsname SEPARATOR ', ')) as 'Participants'
  FROM
      Rooms R
    JOIN Schedule SCH USING (roomid)
    JOIN Sessions S USING (sessionid)
    LEFT JOIN Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    S.pubstatusid = 2
  GROUP BY
    SCH.sessionid
  ORDER BY
    SCH.starttime,
    R.roomname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
