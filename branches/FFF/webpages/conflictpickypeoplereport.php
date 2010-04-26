<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictpickypeoplereport.php";
    $title="Conflict Report - Picky people";
    $description="<P>Show who the picky people do not want to be on a panel with and who they are on panels with.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT 
    X.b AS badgeid, 
    X.pn AS pubsname, 
    X.no AS nopeople, 
    X.tn AS track, 
    concat('<a href=StaffAssignParticipants.php?selsess=',X.s,'>', X.s,'</a>') AS Sessionid, 
    X.sn AS sessionname, 
    group_concat(DISTINCT P2.pubsname,concat(' (',P2.badgeid,')') SEPARATOR ', ') AS 'others on this panel' 
  FROM 
      (SELECT
            PI.badgeid as b,
            P.pubsname as pn,
            S.sessionid as s,
            nopeople as no,
            title as sn,
            trackname as tn 
          FROM
              ParticipantInterests PI,
              ParticipantOnSession PS, 
              Sessions S,
              Participants P,
              Tracks T 
          WHERE
            T.trackid=S.trackid and
            S.sessionid=PS.sessionid and
            PS.badgeid=PI.badgeid and
            P.badgeid=PI.badgeid and
            (nopeople is not null and nopeople!='')) X, 
      Participants P2,
      ParticipantOnSession PSO 
  WHERE
    X.s=PSO.sessionid and P2.badgeid=PSO.badgeid 
  GROUP BY
    X.s 
  ORDER BY
    cast(X.b as unsigned)
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
