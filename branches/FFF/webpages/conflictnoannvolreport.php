<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictnoannvolreport.php";
    $title="Conflict Report - Sessions with no volunteer or announcer";
    $description="<P>Classes and Panels need a volunteer and announcer.  Others may not.  Think before you jump.</P>\n";
    $additionalinfo="<P>Click on the session id to edit the session's volunteer or announcer.</P>\n";
    $indicies="CONFLICTWANTS=1, PROGWANTS=1";

    $query = <<<EOD
SELECT
    typename as 'Type',
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>(', S.sessionid,')</a> ',title) as 'Title', 
    concat(DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p')) as 'StartTime',
    GROUP_CONCAT(DISTINCT if((POS.volunteer=1),P.pubsname,'') SEPARATOR ' ') as 'Volunteer', 
    GROUP_CONCAT(DISTINCT if((POS.announcer=1),P.pubsname,'') SEPARATOR '') as 'Announcer' 
  FROM
      Sessions S 
    LEFT JOIN
      ParticipantOnSession POS on S.sessionid=POS.sessionid, 
      Types T, 
      Participants P,
      Schedule SCH
  WHERE 
    S.sessionid=SCH.sessionid and
    T.typeid=S.typeid AND 
    P.badgeid=POS.badgeid AND
    S.statusid=3
  GROUP BY
    S.sessionid
  ORDER BY
    typename,
    SCH.starttime,
    S.sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
