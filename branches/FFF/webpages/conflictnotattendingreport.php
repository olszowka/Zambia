<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictnotattendingreport.php";
    $title="Conflict Report - not attending people that are on panels.";
    $description="<P>If the interested field is set to 2, pull them off the panel.  If the interested field is set otherwise, escalate to a div-head.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    S.sessionid, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as sessionid,
    P.interested 
  FROM
      Sessions S, 
      Schedule SCH, 
      Participants P, 
      ParticipantOnSession POS 
  WHERE
    P.badgeid=POS.badgeid and
    SCH.sessionid=S.sessionid and
    SCH.sessionid=POS.sessionid and
    P.interested!=1
  ORDER BY
    P.badgeid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
