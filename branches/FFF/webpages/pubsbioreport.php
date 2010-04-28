<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="pubsbioreport.php";
    $title="Pubs - Participant Bio and pubname";
    $description="<P>Show the badgeid, pubsname and bio for each participant who is on at least one scheduled session.</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1";

    $query = <<<EOD
SELECT
    P.badgeid,
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.pubsname,
    P.bio
  FROM
      Participants P
    JOIN CongoDump CD USING (badgeid)
    JOIN (SELECT
	      DISTINCT(badgeid)
            FROM
	        ParticipantOnSession POS,
                Schedule SCH
            WHERE
	      POS.sessionid=SCH.sessionid) as X using (badgeid)
  ORDER BY
    IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),
    CD.firstname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
