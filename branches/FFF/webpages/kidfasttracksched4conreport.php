<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="kidfasttracksched4conreport.php";
    $title="FastTrack Schedule (for con)";
    $description="<P>What is happening in FastTrack - The At-Con Version.</P>\n";
    $additionalinfo="";
    $indicies="FASTTRACKWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'Start Time',
    R.roomname,
    S.title,
    group_concat(concat(P.pubsname,' (',P.badgeid,')') SEPARATOR ', ') as 'Participants'
  FROM
      Schedule SCH
    JOIN Rooms R USING(roomid)
    JOIN Sessions S USING(sessionid)
    JOIN Tracks TR USING(trackid)
    LEFT JOIN ParticipantOnSession POS USING(sessionid)
    LEFT JOIN Participants P USING(badgeid)
  WHERE
    TR.trackname='FAST TRACK'
  GROUP BY
    SCH.scheduleid
  ORDER BY
    SCH.starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
