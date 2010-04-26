<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictpartnumsreport.php";
    $title="Conflict Report - Participant Number of Sessions";
    $description="<P>Compare number of sessions participants requested with the number of which they were assigned.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1, REGWANTS=1";

    $query = <<<EOD
SELECT
    PA.badgeid, 
    P.pubsname, 
    if ((fridaymaxprog is NULL), '<div style=background:lightgray\;>-</div>',concat('<div style=background:lightgray\;>',fridaymaxprog,'</div>')) as 'Fri Reqd.', 
    if ((frisched is NULL), '-', frisched) as 'Fri Asgnd.', 
    if ((saturdaymaxprog is NULL), '<div style=background:lightgray\;>-</div>',concat('<div style=background:lightgray\;>',saturdaymaxprog,'</div>')) as 'Sat Reqd.', 
    if ((satsched is NULL), '-', satsched) as 'Sat Asgnd.', 
    if ((sundaymaxprog is NULL), '<div style=background:lightgray\;>-</div>',concat('<div style=background:lightgray\;>',sundaymaxprog,'</div>')) as 'Sun Reqd.', 
    if ((sunsched is NULL), '-', sunsched) as 'Sun Asgnd.', 
    if ((mondaymaxprog is NULL), '<div style=background:lightgray\;>-</div>',concat('<div style=background:lightgray\;>',mondaymaxprog,'</div>')) as 'Mon Reqd.', 
    if ((monsched is NULL), '-', monsched) as 'Mon Asgnd.', 
    if ((maxprog is NULL), '<div style=background:lightgray\;>-</div>',concat('<div style=background:lightgray\;>',maxprog,'</div>')) as 'Total Reqd.', 
    if ((totsched is NULL), '-', totsched) as 'Tot Asgnd.'
  FROM
      ParticipantAvailability PA,  
      Participants P 
    LEFT JOIN (SELECT
                   badgeid, 
                   sum(if(starttime<'24:00:00',1,0)) as frisched, 
                   sum(if((starttime>='24:00:00' && starttime<'48:00:00'),1,0)) as satsched, 
                   sum(if((starttime>='48:00:00' && starttime<'72:00:00'),1,0)) as sunsched, 
                   sum(if(starttime>='72:00:00',1,0)) as monsched, 
                   count(*) as totsched 
                 FROM
                     (SELECT
                          POS.badgeid, 
                          POS.sessionid, 
                          SCH.starttime 
                        FROM
                            ParticipantOnSession POS, 
                            Schedule SCH 
                        WHERE
                          POS.sessionid=SCH.sessionid) as FOO
                 GROUP BY
                   badgeid) as BAR on P.badgeid=BAR.badgeid 
    LEFT JOIN (SELECT 
                   badgeid,
                   sum(if(day=1,maxprog,0)) as fridaymaxprog,
                   sum(if(day=2,maxprog,0)) as saturdaymaxprog,
                   sum(if(day=3,maxprog,0)) as sundaymaxprog,
                   sum(if(day=4,maxprog,0)) as mondaymaxprog
                 FROM
                     ParticipantAvailabilityDays
                 GROUP BY
                     badgeid) as PAD on P.badgeid = PAD.badgeid
  WHERE 
    PA.badgeid = P.badgeid and
    P.interested=1 
  ORDER BY
    cast(PA.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
