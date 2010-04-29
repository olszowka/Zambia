<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="sessioninterestpartreport.php";
    $title="Session Interest by participant (all info)";
    $description="<P>Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report.  (All data included including for invited sessions.) order by participant.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT 
    P.badgeid BadgeID, 
    P.pubsname, 
    T.trackname Track, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    I.rank Rank, 
    I.comments Comments 
  FROM
      Participants as P, 
      ParticipantSessionInterest as I, 
      Tracks as T,
      Sessions as S
      left join Schedule SCH 
      on S.sessionid=SCH.sessionid 
  WHERE
    P.badgeid=I.badgeid and
    S.sessionid=I.sessionid and 
    T.trackid=S.trackid 
  ORDER BY
    cast(P.badgeid as unsigned) 
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);

