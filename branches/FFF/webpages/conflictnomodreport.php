<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictnomodreport.php";
    $title="Conflict Report - Sessions with no moderator";
    $description="<P>Panels need a moderator.  Other activities may not.  Think before you jump.  (This is limited to items in the schedule which have at least one participant.)</P>\n";
    $additionalinfo="<P>Click on the session id to edit the session's moderator.</P>\n";
    $indicies="CONFLICTWANTS=1";

    $query = <<<EOD
SELECT
    typename as 'Type',
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>(', S.sessionid,')</a> ',title) as 'Title'
  FROM
      Sessions S 
    JOIN 
      Types T USING (typeid) 
    LEFT JOIN
      (SELECT
           sessionid,                
           count(*) as parts,
           sum(if(moderator=1,1,0)) as mods
         FROM ParticipantOnSession
         GROUP BY sessionid) X USING (sessionid)
  WHERE 
    X.parts>0 AND
    X.mods=0
  ORDER BY
    S.sessionid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
