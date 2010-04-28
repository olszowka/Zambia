<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="participantinterestedcountreport.php";
    $title="Participant Interested Count";
    $description="<P>Show the number of people that are interested in attending.</P>\n";
    $additionalinfo="<P>Interested, 1=yes, 2=no, 0=did not pick, NULL=did not hit save.</P>";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.Interested as "Interest Flag",
    count(P.badgeid) as Count
  FROM
      Participants P 
  GROUP BY
    P.interested
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
