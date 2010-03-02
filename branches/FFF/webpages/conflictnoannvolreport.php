<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="conflictnoannvolreport.php";

    function topofpage() {
        staff_header("Conflict Report - Sessions with no volunteer or announcer");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Classes and Panels need a volunteer and announcer.  Others may not.  Think before you jump.</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

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
    echo "<P>Click on the session id to edit the session's volunteer or announcer.</P>\n";
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
