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
    $title="Full Participant Schedule for the Program Packet Merge";
    $description="<P>pubsname, (day, time, duration, room, mod)</P>\n";
    $additionalinfo="<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>\n";
    $indicies="PUBSWANTS=1, CSVONLY=1, GENCSV=1";

    # First query sets the max length, second the actual program description query.
    $query = <<<EOD
SELECT
    POS.badgeid,
    pubsname,
    group_concat(roomname,'\",\"',
		 DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\",\"',
		 concat('(', 
                        CASE 
                          WHEN HOUR(duration) < 1 THEN 
                            concat(date_format(duration,'%i'),'min') 
                          WHEN MINUTE(duration)=0 THEN 
                            concat(date_format(duration,'%k'),'hr') 
                          ELSE
                            concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
                        END
			,')'),'\",\"',
		 trackname,'\",\"',
		 title,'\",\"',
		 if(moderator=1,'M',''),'\",\"'
		 ORDER BY starttime) AS panelinfo 
  FROM
      Participants P,
      Rooms R,
      Sessions S,
      Schedule SCH,
      ParticipantOnSession POS,
      CongoDump C,
      Tracks T
  WHERE
    P.badgeid=C.badgeid and
    S.sessionid=SCH.sessionid and
    POS.sessionid=S.sessionid and
    POS.badgeid=C.badgeid and
    T.trackid=S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid
  GROUP BY
    badgeid
  ORDER BY
    pubsname
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
