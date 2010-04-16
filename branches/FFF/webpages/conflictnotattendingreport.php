<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="conflictnotattendingreport.php";

    function topofpage() {
        staff_header("Conflict Report - not attending people that are on panels.");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>If the interested field is set to 2, pull them off the panel.  If the interested field is set otherwise, escalate to a div-head.</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    S.sessionid, 
    concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as sessionid,
    P.interested 
  FROM
      Sessions S, 
      Schedule SCH, 
      Participants P, 
      ParticipantOnSession POS 
  WHERE
    P.badgeid=POS.badgeid and
    SCH.sessionid=S.sessionid and
    SCH.sessionid=POS.sessionid and
    P.interested!=1
  ORDER BY
    P.badgeid
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
