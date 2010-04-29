<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="partbioreport.php";
    $title="Conflict Report - Participant Bio in conflict";
    $description="<P>Show the badgeid, pubsname and bio for each participant who indicated he is attending.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1, CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    P.editedbio 
  FROM
      Participants P
  WHERE
    P.interested=1
  ORDER BY
    substring_index(pubsname," ",-1)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
