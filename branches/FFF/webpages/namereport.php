<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="namereport.php";
    $title="Name Report";
    $description="<P>Maps badgeid, pubsname, badgename and first and last name together (includes every record in the database regardless of status).</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, REGWANTS=1";

    $query = <<<EOD
SELECT
    C.badgeid,
    P.pubsname,
    C.badgename,
    C.lastname,
    C.firstname 
  FROM
      CongoDump C,
      Participants P 
  WHERE
    C.badgeid=P.badgeid and 
    P.badgeid is not NULL 
  ORDER BY
    IF(instr(P.pubsname,C.lastname)>0,C.lastname,substring_index(P.pubsname,' ',-1)),
    C.firstname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
