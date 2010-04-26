<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="gameroomshedtimereport.php";
    $title="Gaming Schedule by time then room";
    $description="<P>Just things in track gaming (gaming and gaming panels).</P>\n";
    $additionalinfo="";
    $indicies="GAMINGWANTS=1, PROGWANTS=1";

    $query = <<<EOD
SELECT
    roomname,
    function,
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    trackname,
    S.sessionid,
    title,
    group_concat(' ', pubsname,' (',P.badgeid,')') as 'Participants'
  FROM
      Schedule SCH
    JOIN Sessions S USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    trackname in ('Gaming')
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime,
    R.roomname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
