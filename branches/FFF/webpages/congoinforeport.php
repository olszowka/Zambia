<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="congoinforeport.php";
    $title="Congo Info (all info).";
    $description="<P>Shows the information retreived from Congo.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, EVENTSWANTS=1, REGWANTS=1";

    $query = <<<EOD
SELECT
    badgename,
    badgeid,
    regtype,
    lastname,
    firstname,
    phone,
    email,
    postaddress
  FROM
      CongoDump
  WHERE
    badgeid is not NULL
  ORDER BY
    badgename
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
