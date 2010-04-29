<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="invitationreport.php";
    $title="Invited Guest Report";
    $description="<P>For each invited guest session, list the participants who have been invited (and have not deleted the invitation.)</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    T.trackname as Track,
    concat("<a href=EditSession.php?id=",S.sessionid,">",S.sessionid,"</a>") as "Session<BR>ID",
    S.title as Title,
    PSI.badgeid as BadgeId,
    P.pubsname as Pubsname
  FROM
      Sessions S
    JOIN Tracks T ON S.trackid=T.trackid
    LEFT JOIN ParticipantSessionInterest PSI ON S.sessionid=PSI.sessionid
    LEFT JOIN Participants P on PSI.badgeid=P.badgeid
  WHERE
    T.selfselect=1 and
    S.invitedguest=1 and
    statusid=2
  ORDER BY
    T.display_order,
    S.sessionid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
