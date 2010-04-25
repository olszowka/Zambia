<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="gohgridreport.php";
    $title="GOH Grid";
    $description="<P>Display unabridged schedule of all events with any GOHs participanting with rooms on horizontal axis and time on vertical. This includes all items marked \"Do Not Print\" or \"Staff Only\".</P>\n";
    $additionalinfo="<P>Click on the room name to edit the room's schedule;\n";
    $additionalinfo.="the session id to edit the session's participants; or\n";
    $additionalinfo.="the title to edit the session.</P>\n";
    $indicies="GOHWANTS=1, GRIDSWANTS=1";

    $query = <<<EOD
SELECT
        R.roomname,
        R.roomid
    FROM
        Rooms R
    WHERE
        R.roomid in
        (SELECT DISTINCT roomid FROM Schedule SCH JOIN ParticipantOnSession USING (sessionid)
	 WHERE badgeid in ('7768', '96666', '96667', '96668', '10909'));
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
        $message="<P>This report retrieved no results matching the criteria.</P>\n";
        RenderError($title,$message);
        exit();
        }
    for ($i=1; $i<=$rooms; $i++) {
        $header_array[$i]=mysql_fetch_assoc($result);
        }
    $header_cells="<TR><TH>Time</TH>";

    $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'starttime'";
    for ($i=1; $i<=$rooms; $i++) {
        $header_cells.="<TH>";
        $header_cells.=sprintf("<A HREF=\"MaintainRoomSched.php?selroom=%s\">%s</A>",$header_array[$i]["roomid"],$header_array[$i]["roomname"]);
        $header_cells.="</TH>";
        $x=$header_array[$i]["roomid"];
        $y=$header_array[$i]["roomname"]." Title";
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.title,\"\") SEPARATOR '') as \"%s\"",$x,$y);
        $y=$header_array[$i]["roomname"]." SessionID";
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.sessionid,\"\") SEPARATOR '') as \"%s\"",$x,$y);
        $y=$header_array[$i]["roomname"]." Duration";
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.duration,\"\") SEPARATOR '') as \"%s\"",$x,$y);
        }
    $header_cells.="</TR>";

    $query.=" FROM Sessions S";
    $query.=" JOIN Schedule SCH USING (sessionid)";
    $query.=" JOIN Rooms R USING (roomid)";
    $query.=" WHERE S.sessionid in ";
    $query.=" (SELECT DISTINCT sessionid FROM ParticipantOnSession ";
    $query.=" WHERE badgeid IN ('7768', '96666', '96667', '96668', '10909')) ";
    $query.=" GROUP BY SCH.starttime ORDER BY SCH.starttime;";
    // echo "<BR>Query:<BR>$query<BR>\n"; // for debug only
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($rows=mysql_num_rows($result))) {
        $message="<P>This report retrieved no results matching the criteria.</P>\n";
        RenderError($title,$message);
        exit();
        }
    for ($i=1; $i<=$rows; $i++) {
        $grid_array[$i]=mysql_fetch_array($result,MYSQL_BOTH);
        } 
    topofpagereport($title,$description,$additionalinfo);
    echo "<TABLE BORDER=1>";
    echo $header_cells;
    for ($i=1; $i<=$rows; $i++) {
        echo "<TR><TD>";
        echo $grid_array[$i]['starttime'];
        echo "</TD>";
        for ($j=1; $j<=$rooms; $j++) {
            echo "<TD>";
            $x=$grid_array[$i][$j*3-1]; //sessionid
            if ($x!="") {
                    echo sprintf("(<A HREF=\"StaffAssignParticipants.php?selsess=%s\">%s</A>) ",$x,$x);
                    $y = $grid_array[$i][$j*3-2]; //title
                    echo sprintf("<A HREF=\"EditSession.php?id=%s\">%s</A>",$x,$y);
		    $y = substr($grid_array[$i][$j*3],0,-3); // duration; drop ":00" representing seconds off the end
		    if (substr($y,0,1)=="0") {$y = substr($y,1,999);} // drop leading "0"
		    echo sprintf(" (%s)",$y);
                    }
                else
                    { echo "&nbsp;"; } 
            echo "</TD>";
            }
        echo "</TR>\n";
        }
    echo "</TABLE>";
    staff_footer();
