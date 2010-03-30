<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="arisiatvroomschedreport.php";

    function topofpage() {
        staff_header("Arisia TV by time.");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Just things in TV room.</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    trackname, 
    S.sessionid,
    title,
    group_concat(' ',pubsname,' (',P.badgeid,')') as 'Participants'
  FROM
      Tracks T,
      Rooms R,
      Sessions S,
      Schedule SCH
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid = POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid = P.badgeid
  WHERE
    T.trackid = S.trackid and
    SCH.roomid = R.roomid and
    SCH.sessionid = S.sessionid and
    roomname in ('ArisiaTV')
  GROUP BY
    SCH.sessionid
  ORDER BY
    SCH.starttime
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
