<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="kidcountreport.php";
    $title="Participant Kid Count";
    $description="<P>How many kids did the participants say they are bringing for Fast Track?</P>\n";
    $additionalinfo="";
    $indicies="FASTTRACKWANTS=1";

    $query = <<<EOD
SELECT
    P.Badgeid, 
    Pubsname, 
    Numkidsfasttrack  
  FROM
      Participants P, 
      ParticipantAvailability PA 
  WHERE
      P.badgeid=PA.badgeid
  ORDER BY
      Numkidsfasttrack DESC
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);

