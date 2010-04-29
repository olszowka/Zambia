<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="partpickyreport.php";
    $title="Participants With People to Avoid";
    $description="<P>Show the badgeid, pubsname and list of people to avoid for each participant who indicated he is attending and listed people with whom he does not want to share a panel.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    PI.badgeid,
    P.pubsname,
    PI.nopeople
  FROM
      ParticipantInterests PI
    JOIN Participants P USING (badgeid)
  WHERE
    P.interested=1 and
    (nopeople is not null and nopeople!='')
  ORDER BY
    substring_index(pubsname,' ',-1)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
