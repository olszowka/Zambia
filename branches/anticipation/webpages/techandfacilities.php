<?php
    $title="Tech and Facilities Report";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    $_SESSION['return_to_page']="techandfacilities.php";
    function topofpage() {
        staff_header("Tech and Facilities Report");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>List of all sessions with info relevant to tech and facilities.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $con_start_datetime=CON_START_DATIM;
    $query = <<<EOD
SELECT
        if (isnull(R.roomname),'Unscheduled',R.roomname) as roomname,
        SCH.roomid,
        DATE_FORMAT(ADDTIME('$con_start_datetime',SCH.starttime),'%a %l:%i %p') as 'starttime',
        S.Duration,
        T.Trackname,
        S.Sessionid,
        S.title,
        S.Servicenotes,
        A.svcs,
        B.feats
    FROM 
        Sessions S JOIN
        Tracks T USING (trackid) LEFT JOIN
        Schedule SCH USING (sessionid) LEFT JOIN
        Rooms R USING (roomid)
        LEFT JOIN
            (SELECT
                 S.sessionid, group_concat(SV.servicename) as svcs
             FROM
                 Sessions S JOIN
                 SessionHasService USING (sessionid) JOIN
                 Services SV USING (serviceid)
             GROUP BY
                 S.sessionid) A USING (sessionid)
        LEFT JOIN
            (SELECT
                 S.sessionid, group_concat(F.featurename) as feats
             FROM
                 Sessions S JOIN
                 SessionHasFeature USING (sessionid) JOIN
                 Features F USING (featureid)
             GROUP BY
                 S.sessionid) B USING (sessionid)
    WHERE
        S.statusid in (3,4,5,6)
    HAVING
        S.servicenotes!=' ' or svcs is not null or feats is not null
    ORDER BY
        roomname, starttime;
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        RenderError($title,$message);
        exit ();
        }
    if (0==mysql_num_rows($result)) {
        topofpage();
        noresults();
        exit();
        }
    topofpage();
    echo "<P>Click on the session tile to edit the session.<BR>\n";
    echo "Click on the room name to reschedule the session.<BR>\n";
    echo "<TABLE BORDER=1>";
    echo "<TR>\n";
    echo "    <TH class=\"small\">Room Name</TH>\n";
    echo "    <TH class=\"small\">&nbsp;&nbsp;Start&nbsp;Time&nbsp;&nbsp;</TH>\n";
    echo "    <TH class=\"small\">Duration</TH>\n";
    echo "    <TH class=\"small\">Track</TH>\n";
    echo "    <TH class=\"small\">Session ID</TH>\n";
    echo "    <TH class=\"small\">Title</TH>\n";
    echo "    <TH class=\"small\">Notes for Tech and Fac.</TH>\n";
    echo "    <TH class=\"small\">Services</TH>\n";
    echo "    <TH class=\"small\">Features</TH>\n";
    echo "    </TR>\n";
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    while ($resultrow) {
        echo "<TR>\n";
        echo "    <TD class=\"small\">";
        if ($resultrow['roomid']=="") {
                echo $resultrow['roomname'];
                }
            else {
                echo "<A HREF=\"MaintainRoomSched.php?selroom={$resultrow['roomid']}\">{$resultrow['roomname']}</A>";
                }
        echo "</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['starttime'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['Duration'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['Trackname'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['Sessionid'])."</TD>\n";
        echo "    <TD class=\"small\"><A HREF=\"EditSession.php?id={$resultrow['sessionid']}\">".htmlentities($resultrow['title'])."</A></TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['Servicenotes'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['svcs'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['feats'])."</TD>\n";
        echo "    </TR>\n";
        $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        }
    echo "</TABLE>\n";
    staff_footer();
?>
