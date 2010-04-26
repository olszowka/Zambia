<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="prognamesannvolprogreport.php";
    $title="Program Participant Number, Names, Contact, and Involvement";
    $description="<P>Full listing of the names and contact, and how many classes, as a Program Participant, Announcer or Volunteer they are involved in.  Replaces 4progthankyounotereport's non-csv version.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    C.firstname, 
    C.lastname, 
    C.email, 
    SCH.sessioncount as 'Total involvement', 
    SCH.volcount as 'Volunteer Sessions',
    SCH.anncount as 'Announcer Sessions',
    (SCH.sessioncount-SCH.volcount-SCH.anncount) as 'Program Sessions' 
  FROM
      CongoDump as C, 
      Participants as P 
    LEFT JOIN (SELECT
                   POS1.badgeid as badgeid , 
                   count(SCH1.sessionid) as sessioncount,
                   sum(if(volunteer=1,1,0)) as volcount,
                   sum(if(announcer=1,1,0)) as anncount
                 FROM
                     ParticipantOnSession POS1, 
                     Schedule SCH1, 
                     Sessions S, 
                     Tracks T 
               WHERE
                 POS1.sessionid=SCH1.sessionid and
                 SCH1.sessionid=S.sessionid and
                 S.trackid=T.trackid 
               GROUP BY
                 POS1.badgeid) as SCH on P.badgeid=SCH.badgeid 
  WHERE 
    SCH.sessioncount is not NULL and
    C.badgeid=P.badgeid 
  GROUP BY
    (P.badgeid) 
  ORDER BY
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
