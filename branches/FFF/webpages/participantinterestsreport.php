<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="participantinterestsreport.php";
    $title="Participant Interests";
    $description="<P>What is that participant interested in?</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid,
    P.pubsname,
    yespanels as "New Panel Ideas",
    nopanels as "Panel Not Interested",
    yespeople,
    nopeople,
    otherroles 
  FROM
      ParticipantInterests PI, 
      Participants P
  WHERE
    P.badgeid=PI.badgeid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
