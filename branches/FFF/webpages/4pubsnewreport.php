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
    $title="Pubs - Session Characteristics plus long description";
    $description="<P>For Scheduled items ONLY. Show sessionid, track, type, divisionid, pubstatusid, pubno, pubchardest, kids, title, long description.</P>\n";
    $additionalinfo="<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>\n";
    $indicies="PUBSWANTS=1, GENCSV=1";

    # First query sets the max length, second the actual program description query.
    $query="SET group_concat_max_len=25000";
    if (!$result=mysql_query($query,$link)) {
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        $message.="<P class\"errmsg\">".$query."\n";
	RenderError($title,$message);
        exit();
        }

    $query = <<<EOD
SELECT
    S.sessionid, 
    trackname, 
    typename, 
    divisionname, 
    pubcharname, 
    kidscatname, 
    title, 
    roomname, 
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime), '%a %l:%i %p') as 'Start Time', 
    CASE 
      WHEN HOUR(duration) < 1 THEN 
        concat(date_format(duration,'%i'),'min') 
      WHEN MINUTE(duration)=0 THEN 
        concat(date_format(duration,'%k'),'hr') 
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END as duration,
    if(group_concat(' ',pubsname) is NULL,'',group_concat(' ',pubsname)) as 'Participants' ,
    progguiddesc as 'Long Description'
  FROM
      Tracks T, 
      Types Ty, 
      Divisions D, 
      PubStatuses PS, 
      KidsCategories K, 
      Rooms R, 
      Sessions S 
    LEFT JOIN SessionHasPubChar SHPC ON S.sessionid=SHPC.sessionid 
    LEFT JOIN PubCharacteristics PC ON SHPC.pubcharid=PC.pubcharid, 
      Schedule SCH 
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
    LEFT JOIN CongoDump C ON POS.badgeid=C.badgeid 
    LEFT JOIN Participants P ON C.badgeid=P.badgeid
  WHERE 
    S.trackid=T.trackid and
    S.typeid=Ty.typeid and
    S.divisionid=D.divisionid and
    S.pubstatusid=PS.pubstatusid and
    PS.pubstatusname = 'Public' and
    S.kidscatid=K.kidscatid and
    S.sessionid=SCH.sessionid and
    R.roomid=SCH.roomid
  GROUP BY
    S.sessionid
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
