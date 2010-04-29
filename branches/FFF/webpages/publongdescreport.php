<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="publongdescreport.php";
    $title="Pubs - Session Characteristics plus long description";
    $description="<P>For Scheduled items ONLY. Show sessionid, track, type, divisionid, pubstatusid, pubno, pubchardest, kids, title, long description.</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1";

    $query = <<<EOD
SELECT
    S.sessionid, 
    trackname, 
    typename, 
    divisionname, 
    pubstatusname, 
    pubsno, 
    pubcharname, 
    kidscatname, 
    title, 
    progguiddesc as 'Long Description'
  FROM
      Tracks T, 
      Types Ty, 
      Divisions D, 
      PubStatuses PS, 
      KidsCategories K, 
      Sessions S 
    LEFT JOIN SessionHasPubChar SHPC on S.sessionid=SHPC.sessionid 
    LEFT JOIN PubCharacteristics PC on SHPC.pubcharid=PC.pubcharid, 
      Schedule SCH 
  WHERE
    S.trackid=T.trackid and
    S.typeid=Ty.typeid and
    S.divisionid=D.divisionid and
    S.pubstatusid=PS.pubstatusid and
    PS.pubstatusname = 'Public' and
    S.kidscatid=K.kidscatid and
    S.sessionid=SCH.sessionid;
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
