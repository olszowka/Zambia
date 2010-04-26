<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="namechangesreport.php";
    $title="Name Report - Changes";
    $description="<P>(in progress... try back in a bit). Do these folks want to update thier badgenames?   The pubsname and badgename don't match.   Report shows badgeid, pubsname, badgename, firstname and lastname.</P>\n";
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
    P.pubsname and
    strcmp(C.badgename, P.pubsname) and
    C.badgename=P.pubsname,
    P.pubsname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
