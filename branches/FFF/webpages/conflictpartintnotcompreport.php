<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictpartintnotcompreport.php";
    $title="Conflict Report - Interested Participants that wont comp";
    $description="<P>Comps are limited to participants on 3 or more panels.  These folks are on less than 3 scheduled panels.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1, REGWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    if (X.Schd is NULL, 0, X.Schd) Schd, 
    if (Y.Intr is NULL, 0, Y.Intr) Intr
  FROM
      CongoDump C, 
      Participants P 
    LEFT JOIN (SELECT
                   POS.badgeid, 
                   count(POS.sessionid) Schd
                 FROM
                     ParticipantOnSession POS,
                     Schedule
                 WHERE
                   S.sessionid=POS.sessionid
                 GROUP BY
                   POS.badgeid) X on P.badgeid=X.badgeid 
    LEFT JOIN (SELECT
                   PSI.badgeid, 
                   count(PSI.sessionid) as Intr
                 FROM
                     ParticipantSessionInterest PSI
                 GROUP BY
                     PSI.badgeid) Y on Y.badgeid=P.badgeid
  WHERE
    C.badgeid=P.badgeid and
    interested=1 and
    C.regtype is NULL 
  HAVING
    Schd < 3
  ORDER BY
    Intr DESC,
    cast(C.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
