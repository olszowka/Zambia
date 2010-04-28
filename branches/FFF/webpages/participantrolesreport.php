<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="participantrolesreport.php";
    $title="Participant Roles";
    $description="<P>What Roles is a participant willing to take?</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid,
    P.pubsname,
    rolename 
  FROM
      Participants P,
      ParticipantHasRole PR,
      Roles 
  WHERE
    P.badgeid=PR.badgeid and
    PR.roleid=Roles.roleid 
  ORDER BY
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
