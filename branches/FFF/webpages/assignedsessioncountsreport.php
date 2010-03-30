<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="assignedsessioncountsreport.php";

    function topofpage() {
        staff_header("Assigned Session by Session (counts)");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>How many people are assigned to each session? (Sorted by track then sessionid.)</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT 
    Trackname, 
    concat('<a href=StaffAssignParticipants.php?selsess=',Sessions.sessionid,'>', Sessions.sessionid,'</a>') as Sessionid,
    concat('<a href=EditSession.php?id=',Sessions.sessionid,'>',Sessions.title,'</a>') Title,
    Statusname, 
    count(badgeid) as NumAssigned 
  FROM
      ParticipantOnSession, 
      Sessions, 
      Tracks, 
      SessionStatuses  
  WHERE
    ParticipantOnSession.sessionid=Sessions.sessionid and
    Tracks.trackid=Sessions.trackid and
    Sessions.statusid=SessionStatuses.statusid
  GROUP BY
    sessionid 
  ORDER BY
    trackname,
    sessionid
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
