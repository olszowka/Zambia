<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictfewassignedreport.php";
    $title="Conflict Report - Sessions with under 4 people assigned";
    $description="<P>Not all of these are actually conflict, you want to think about them.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    trackname,
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    title,
    count(badgeid) assigned
  FROM
          Sessions S
      LEFT JOIN ParticipantOnSession POS on S.sessionid=POS.sessionid,
      Tracks T
  WHERE
    T.trackid=S.trackid and
    S.statusid=3 AND
    POS.volunteer=0 AND
    POS.announcer=0
  GROUP BY
    sessionid having assigned<4
  ORDER BY
    trackname,
    S.sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
