<?php
    $title="Event Schedule by time then room";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="eventschedtimereport.php";

    function topofpage() {
        staff_header($title);
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Event Schedule by time then room as determined by session division</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT
        DATE_FORMAT(ADDTIME('2011-01-14 00:00:00',starttime),'%a %l:%i %p') as 'starttime',
        CASE
            WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
            WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
            ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
            END AS 'duration',
        R.roomid,
        R.roomname,
        R.function,
        T.trackname,
        S.sessionid,
        S.title, 
        PS.pubstatusname,
        IFNULL(GROUP_CONCAT( CONCAT(P.pubsname,"(",P.badgeid,")") SEPARATOR ", "),"&nbsp;") as participants
    FROM
            Schedule SCH
       JOIN Sessions S USING (sessionid)
       JOIN Tracks T USING (trackid)
       JOIN Rooms R USING (roomid)
       JOIN PubStatuses PS USING (pubstatusid)
  LEFT JOIN ParticipantOnSession POS USING (sessionid)
  LEFT JOIN Participants P USING (badgeid)
    WHERE
        S.divisionid=3  #  Events
    GROUP BY
        SCH.scheduleid
    ORDER BY
        SCH.starttime,
        R.roomname
EOD;
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
        $grid_array[$i]=mysql_fetch_array($result,MYSQL_ASSOC);
        } 
    topofpage();
    //echo "<BR>debug1:<BR>\n";
    //print_r($grid_array[1]);
    //echo "<BR>debug2:<BR>\n";
	//print_r($grid_array[1]['sessionid']);
    //echo "<BR>\n";
    echo "<P>Click on the room name to edit the room's schedule; the session id to edit the session's participants; or the title to edit the session.</P>\n";
    echo "<TABLE BORDER=1>";
    echo "<COL><COL><COL><COL><COL><COL><COL width=\"20%\"><COL><COL width=\"30%\">";
    echo "<TR><TH>Start Time</TH><TH>Duration</TH><TH>Roomname</TH><TH>Function</TH>";
    echo "<TH>Trackname</TH><TH>Sessionid</TH><TH>Title</TH><TH>PubStatus</TH><TH>Participants</TH></TR>\n";
    for ($i=1; $i<=$rows; $i++) {
        echo "<TR>";
        echo "<TD>{$grid_array[$i]['starttime']}</TD>";
        echo "<TD>{$grid_array[$i]['duration']}</TD>";
        echo sprintf("<TD><A HREF=\"MaintainRoomSched.php?selroom=%s\">%s</A></TD>",
            $grid_array[$i]['roomid'],$grid_array[$i]['roomname']);
        echo "<TD>{$grid_array[$i]['function']}</TD>";
        echo "<TD>{$grid_array[$i]['trackname']}</TD>";
        echo sprintf("<TD><A HREF=\"StaffAssignParticipants.php?selsess=%s\">%s</A></TD>",
            $grid_array[$i]['sessionid'],$grid_array[$i]['sessionid']);
        echo sprintf("<TD><A HREF=\"EditSession.php?id=%s\">%s</A></TD>",
            $grid_array[$i]['sessionid'],$grid_array[$i]['title']);
        echo "<TD>{$grid_array[$i]['pubstatusname']}</TD>";
        echo "<TD>{$grid_array[$i]['participants']}</TD>"; 
        echo "</TR>\n";
        }
    echo "</TABLE>";
    staff_footer();

