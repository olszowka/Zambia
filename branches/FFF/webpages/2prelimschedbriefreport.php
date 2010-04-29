<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $ProgramEmail=PROGRAM_EMAIL;
    $removed_tracks="('Video','Film','Anime')";
    $_SESSION['return_to_page']="2prelimschedbriefreport.php";
    $title="Preliminary Schedule";
    $description="<P>Preliminary panel schedule.</P>\n";
    $additionalinfo="<P>Please keep in mind that is it still changing as\n";
    $additionalinfo.="we recieve feedback from our panelists.  This table\n";
    $additionalinfo.="ignores the tracks: $removed_tracks  If you have\n";
    $additionalinfo.="any comments please contact us at:\n";
    $additionalinfo.="<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.</P>\n";
    $indicies="PUBSWANTS=1, PROGWANTS=1";

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',  
    trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title
  FROM
      Sessions S, 
      Schedule SCH, 
      Tracks T 
  WHERE
    T.trackid=S.trackid and
    SCH.sessionid = S.sessionid and
    trackname not in $removed_tracks
  ORDER BY
    T.trackname, 
    SCH.starttime
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
