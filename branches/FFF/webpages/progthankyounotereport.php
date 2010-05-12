<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $strScript=$_SERVER['SCRIPT_NAME'];
    $intLastSlash = strrpos($strScript, "/");
    $scriptname = substr($strScript, $intLastSlash+1, strlen($strScript));
    $_SESSION['return_to_page']="$scriptname";
    $title="Program Participant Thank you note query";
    $description="<P>prefered name, firstname, lastname, mailing address, count of scheduled sessions (for only some tracks!)</P>\n";
    $additionalinfo="<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>\n";
    $indicies="PROGWANTS=1, CSVONLY=1, GENCSV=1";

    # First query sets the max length, second the actual program description query.
    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    C.firstname, 
    C.lastname, 
    C.email, 
    SCH.sessioncount as 'Total involvement', 
    SCH.volcount as 'Volunteer Sessions',
    SCH.anncount as 'Announcer Sessions',
    (SCH.sessioncount-SCH.volcount-SCH.anncount) as 'Program Sessions' 
  FROM
      CongoDump as C, 
      Participants as P 
    LEFT JOIN (SELECT
                   POS1.badgeid as badgeid , 
                   count(SCH1.sessionid) as sessioncount,
                   sum(if(volunteer=1,1,0)) as volcount,
                   sum(if(announcer=1,1,0)) as anncount
                 FROM
                     ParticipantOnSession POS1, 
                     Schedule SCH1, 
                     Sessions S, 
                     Tracks T 
               WHERE POS1.sessionid=SCH1.sessionid and
                     SCH1.sessionid=S.sessionid and
                     S.trackid=T.trackid
               GROUP BY
                     POS1.badgeid) as SCH on P.badgeid=SCH.badgeid 
  WHERE
    SCH.sessioncount is not NULL and
    C.badgeid=P.badgeid 
  GROUP BY
    (P.badgeid) 
  ORDER BY
    cast(P.badgeid as unsigned)
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    if ($_GET["csv"]=="y") {
      topofpagecsv(str_replace('report.php','.csv',$scriptname));
      rendercsvreport($rows,$header_array,$class_array);
      } else {
      topofpagereport($title,$description,$additionalinfo);
      renderhtmlreport($rows,$header_array,$class_array);
      }
