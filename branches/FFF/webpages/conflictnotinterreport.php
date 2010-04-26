<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictnotinterreport.php";
    $title="Conflict Report - people on panels they are not interested in";
    $description="<P>This can happen two ways: Either someone used the feature at the bottom of the assign page to do this deliberately or the participant removed his or her interest after being assigned.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    POS.Badgeid, 
    P.Pubsname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',POS.sessionid,'>', POS.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title
  FROM
      Sessions S,
      Participants P, 
      ParticipantOnSession POS
    left join ParticipantSessionInterest PSI ON POS.badgeid=PSI.badgeid and POS.sessionid=PSI.sessionid 
  WHERE
    P.badgeid=POS.badgeid and
    POS.sessionid = S.sessionid and
    PSI.sessionid is NULL
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
