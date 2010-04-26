<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="attendingreport.php";
    $title="Attending Query (all info)";
    $description="<P>Shows who has responded and if they are attending.  (Interested, 1=yes, 2=no, 0=did not pick, blank=did not hit save.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, PUBSWANTS=1";

    $query = <<<EOD
SELECT
    P.pubsname,
    P.badgeid,
    P.interested,
    P.bestway 
  FROM
      Participants P 
  ORDER BY
    P.pubsname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
