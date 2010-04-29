<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessioninterestpartcountreport.php";
    $title="Session Interest Counts by Participant";
    $description="<P>Just how many panels did each participant sign up for anyway?</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.Badgeid, 
    P.Pubsname, 
    count(sessionid) as Interested 
  FROM
      Participants P 
    LEFT JOIN ParticipantSessionInterest PSI on P.badgeid=PSI.badgeid 
  WHERE
    P.interested=1 
  GROUP BY
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);

