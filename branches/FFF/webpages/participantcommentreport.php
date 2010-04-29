<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="participantcommentreport.php";
    $title="Participant Commentary";
    $description="<P>Comments recorded for Participants. <A HREF=\"CommentOnParticipants.php\">(Add a comment)</A></P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
    P.pubsname,
    COP.commenter,
    COP.comment
  FROM
      Participants P 
    JOIN
      CommentsOnParticipants COP USING (badgeid)
  ORDER BY
    P.pubsname
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
