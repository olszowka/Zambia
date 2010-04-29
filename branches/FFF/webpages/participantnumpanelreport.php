<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="participantnumpanelreport.php";
    $title="Participant Number of Pannels and Constraints";
    $description="<P>How many panels does each person want to be on and the other constraints they indicated.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid,
    P.pubsname,
    interested,
    Friday,
    Saturday,
    Sunday,
    Monday,
    maxprog,
    preventconflict,
    otherconstraints
  FROM
      ParticipantAvailability PA, Participants P
    LEFT JOIN (SELECT
	           badgeid,
	           sum(if(day=1,maxprog,0)) as Friday,
                   sum(if(day=2,maxprog,0)) as Saturday,
                   sum(if(day=3,maxprog,0)) as Sunday,
	           sum(if(day=4,maxprog,0)) as Monday
                 FROM
	             ParticipantAvailabilityDays
                 GROUP BY badgeid) PADQ ON P.badgeid = PADQ.badgeid
  WHERE
    P.badgeid=PA.badgeid
  ORDER BY
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
