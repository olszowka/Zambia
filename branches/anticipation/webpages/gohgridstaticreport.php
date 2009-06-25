<?php
    $title="GOH Grid";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $_SESSION['return_to_page']="gohgridstaticreport.php";
    function topofpage() {
        staff_header($title);
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Grid of all GOH sessions.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $query = <<<EOD
SELECT
        R.roomname,
        R.roomid
    FROM
            Rooms R
    WHERE
        R.roomid in
        (SELECT DISTINCT roomid FROM Schedule);
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($rooms=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    for ($i=1; $i<=$rooms; $i++) {
        $header_array[$i]=mysql_fetch_assoc($result);
        }
    $header_cells="<TR><TH>Time</TH>";
    $con_start_datetime=CON_START_DATIM;
    $query="SELECT DATE_FORMAT(ADDTIME('$con_start_datetime',SCH.starttime),'%a %l:%i %p') as 'starttime'";
    for ($i=1; $i<=$rooms; $i++) {
        $header_cells.="<TH>";
        $header_cells.=sprintf("<A HREF=\"MaintainRoomSched.php?selroom=%s\">%s</A>",$header_array[$i]["roomid"],$header_array[$i]["roomname"]);
        $header_cells.="</TH>";
        $x=$header_array[$i]["roomid"];
        $y=$header_array[$i]["roomname"]." Title";
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.title,\"\") SEPARATOR '') as \"%s\"",$x,$y);
        $y=$header_array[$i]["roomname"]." SessionID";
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.sessionid,\"\") SEPARATOR '') as \"%s\"",$x,$y);
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.duration,\"\") SEPARATOR '') as \"%s\"",$x,$y);
        }
    $header_cells.="</TR>";

    $query.=" FROM Participants G, Sessions S";
    $query.=" JOIN Schedule SCH USING (sessionid)";
    $query.=" JOIN Rooms R USING (roomid)";
    $query.=" LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid";
    $query.=" LEFT JOIN Participants P ON POS.badgeid=P.badgeid";
    $query.=" WHERE G.badgeid in ('4352', '6282', '6752', '93571') AND G.badgeid=POS.badgeid";
    $query.=" GROUP BY SCH.starttime ORDER BY SCH.starttime;";

    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($rows=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    for ($i=1; $i<=$rows; $i++) {
        $grid_array[$i]=mysql_fetch_array($result,MYSQL_BOTH);
        } 
    topofpage();
    echo "<P>Click on the room name to edit the room's schedule; the session id to edit the session's participants; or the title to edit the session.</P>\n";
    echo "<TABLE BORDER=1>";
    echo $header_cells;
    for ($i=1; $i<=$rows; $i++) {
        echo "<TR><TD>";
        echo $grid_array[$i]['starttime'];
        echo "</TD>";
        for ($j=1; $j<=$rooms; $j++) {
            echo "<TD>";
            $x=$grid_array[$i][$j*2]; //sessionid
            if ($x!="") {
                    echo sprintf("(<A HREF=\"StaffAssignParticipants.php?selsess=%s\">%s</A>) ",$x,$x);
                    $y = $grid_array[$i][$j*2-1]; //title
                    echo sprintf("<A HREF=\"EditSession.php?id=%s\">%s</A>",$x,$y);
                    }
                else
                    { echo "&nbsp;"; } 
            echo "</TD>";
            }
        echo "</TR>\n";
        }
    echo "</TABLE>";
    staff_footer();
