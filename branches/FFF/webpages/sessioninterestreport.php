<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessioninterestreport.php";
    $title="Session Interest Report (all info)";
    $description="<P>Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report.  (All data included including for invited sessions.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    T.trackname as 'Track',
    CONCAT('<A HREF=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</A>') as Sessionid,
    CONCAT('<A HREF=EditSession.php?id=',S.sessionid,'>',title,'</A>') as title,
    P.pubsname,
    P.badgeid as 'BadgeID',
    PSI.rank as 'Rank',
    PSI.willmoderate as 'Mod?',
    PSI.comments as 'Comments'
  FROM
      Participants P,
      ParticipantSessionInterest PSI,
      Sessions S,
      Tracks T
  WHERE
    P.badgeid=PSI.badgeid AND
    S.sessionid=PSI.sessionid AND
    T.trackid=S.trackid
  ORDER BY
    T.trackname,
    title
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
