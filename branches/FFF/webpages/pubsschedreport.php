<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $strScript=$_SERVER['SCRIPT_NAME'];
    $intLastSlash = strrpos($strScript, "/");
    $scriptname = substr($strScript, $intLastSlash+1, strlen($strScript));
    $_SESSION['return_to_page']="$scriptname";
    $title="Schedule report for Pubs";
    $description="<P>Lists all Sessions Scheduled in all Rooms.</P>\n";
    $additionalinfo="<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>\n";
    $indicies="PUBSWANTS=1, GENCSV=1";

    $query = <<<EOD
SELECT
    S.sessionid,
    R.roomname, 
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'Start Time', 
    CASE 
      WHEN HOUR(duration) < 1 THEN 
        concat(date_format(duration,'%i'),'min') 
      WHEN MINUTE(duration)=0 THEN 
        concat(date_format(duration,'%k'),'hr') 
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    T.trackname,
    S.title, 
    PS.pubstatusname
  FROM 
      Sessions S
    JOIN Schedule SCH using (sessionid)
    JOIN Rooms R using (roomid)
    JOIN Tracks T using (trackid)
    JOIN PubStatuses PS using (pubstatusid) 
  ORDER BY
    SCH.starttime, 
    R.roomname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    if ($_GET["csv"]=="y") {
      topofpagecsv(str_replace('report.php','.csv',$scriptname));
      rendercsvreport($rows,$header_array,$class_array);
      } else {
      topofpagereport($title,$description,$additionalinfo);
      renderhtmlreport($rows,$header_array,$class_array);
      }
