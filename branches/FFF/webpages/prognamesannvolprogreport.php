<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="prognamesannvolprog.php";

    function topofpage() {
        staff_header("Program Participant Number, Names, Contact, and Involvement");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Full listing of the names and contact, and how many classes, as a Program Participant, Announcer or Volunteer they are involved in.</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

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
               WHERE
                 POS1.sessionid=SCH1.sessionid and
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
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        RenderError($title,$message);
        exit ();
        }
    if (0==($rows=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    for ($i=1; $i<=$rows; $i++) {
        $class_array[$i]=mysql_fetch_assoc($result);
        }
    $header_array=array_keys($class_array[1]);
    $columns=count($header_array);
    $headers="";
    foreach ($header_array as $header_name) {
      $headers.="<TH>";
      $headers.=$header_name;
      $headers.="</TH>\n";
      }
    topofpage();
    echo "<TABLE BORDER=1>";
    echo "<TR>" . $headers . "</TR>";
    for ($i=1; $i<=$rows; $i++) {
        echo "<TR>";
        foreach ($header_array as $header_name) {
            echo "<TD>";
            echo $class_array[$i][$header_name];
            echo "</TD>\n";
	    }
        echo "</TR>\n";
        }
    echo "</TABLE>";
    staff_footer();
