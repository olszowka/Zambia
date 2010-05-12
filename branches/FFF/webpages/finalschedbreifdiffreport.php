<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="2finalschedbreifdiffreport.php";
    $title="Schedule - brief diff";
    $description="<P>Recent changes to the PUBLIC Panel, Events, Film, Anime, Video and Arisia TV schedule.</P>\n";
    $change_since_date="\"2009-01-07 13:50:00\"";
    $additionalinfo="<P>This has a hard-coded \"date\" $change_since_date from which it determines \"recent\".  This should probably be a vector value from today.";
    $indicies="PUBSWANTS=1, PROGWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time', 
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    roomname, 
    trackname, 
    title,
    S.ts as changed
  FROM
      Sessions S, 
      Schedule SCH, 
      Tracks T, 
      Rooms R
  WHERE
    R.roomid=SCH.roomid and
    T.trackid=S.trackid and
    SCH.sessionid = S.sessionid and
    S.pubstatusid = 2 and
    S.ts > $change_since_date
  ORDER BY S.ts, 
    SCH.starttime,
    T.trackname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
