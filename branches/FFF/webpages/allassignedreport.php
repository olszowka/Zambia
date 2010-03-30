<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="allassignedreport.php";

    function topofpage() {
        staff_header("All Sessions that are assigned");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Who is assigned to what.</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT
    trackname as Trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',S.sessionid,'>',S.title,'</a>') Title,
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    group_concat(' ',P.pubsname,' (',P.badgeid,')') as 'Participants',
    GROUP_CONCAT(DISTINCT if((POS.moderator=1),P.pubsname,'') SEPARATOR ' ') as 'Moderator', 
    GROUP_CONCAT(DISTINCT if((POS.volunteer=1),P.pubsname,'') SEPARATOR ' ') as 'Volunteer', 
    GROUP_CONCAT(DISTINCT if((POS.announcer=1),P.pubsname,'') SEPARATOR '') as 'Announcer' 
  FROM
      Sessions S, 
      Participants P, 
      ParticipantOnSession POS, 
      Tracks 
  WHERE
    P.badgeid=POS.badgeid AND
    POS.sessionid=S.sessionid AND
    Tracks.trackid=S.trackid 
  GROUP BY
    S.sessionid 
  ORDER BY
    Trackname,
    S.sessionid
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
