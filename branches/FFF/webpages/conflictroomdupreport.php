<?php
    $title="";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="conflictroomdupreport.php";

    function topofpage() {
        staff_header("Conflict Report - Room Schedule Overlaps.");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Find any pairs of sessions whose times overlap in the same room.</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT
    concat('<a href=MaintainRoomSched.php?selroom=',R.roomid,'>', R.roomname,'</a>') as Roomname,
    SA.title as 'Title A',
    Asess as 'Sessionid A',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Astart),'%a %l:%i %p') as 'Start Time A',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Aend),'%a %l:%i %p') as 'End Time A',
    SB.title as 'Title B',
    Bsess as 'Sessionid B',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Bstart),'%a %l:%i %p') as 'Start Time B',
    DATE_FORMAT(ADDTIME('$ConStartDatim',Bend),'%a %l:%i %p') as 'End Time B'
  FROM
      Sessions SA,
      Sessions SB,
      Rooms R,
      (SELECT
           A.roomid,
           A.sessionid as Asess,
           A.starttime as Astart,
           ADDTIME(A.starttime, SA.duration) as Aend,
           B.sessionid as Bsess,
           B.starttime as Bstart,
           ADDTIME(B.starttime, SB.duration) as Bend
         FROM
             Schedule A,
             Schedule B,
             Sessions SA,
             Sessions SB
         WHERE
           A.roomid = B.roomid and
           A.starttime<=B.starttime and
           ADDTIME(A.starttime, SA.duration)>B.starttime and
           A.sessionid<>B.sessionid and
           A.sessionid=SA.sessionid and
           B.sessionid=SB.sessionid) as Foo
  WHERE
    Foo.roomid = R.roomid and
    Foo.Asess=SA.sessionid and
    Foo.Bsess=SB.sessionid
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
