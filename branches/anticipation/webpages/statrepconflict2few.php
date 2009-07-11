<?php
    $title="Conflict Report - Too Few Participants";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    $_SESSION['return_to_page']="statrepconflict2few.php";
    function topofpage() {
        staff_header("Conflict Report - Too Few Participants");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>List of all scheduled sessions with fewer than 4 partipants except those of type live performance, movie/video, reading, or KaffeeKlatsch.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $con_start_datetime=CON_START_DATIM;
    $query = <<<EOD
SELECT
        TR.trackname, S.sessionid,
        S.title, count(POS.badgeid) numassigned
    FROM
        Tracks TR JOIN
        Sessions S USING (trackid) LEFT JOIN
        ParticipantOnSession POS USING(sessionid)
    WHERE
        S.sessionid IN
            (SELECT sessionid FROM Schedule) AND
        S.typeid NOT IN (5,6,7,8,10,15,19)
# live perf, movie, ind rdng, grp rdng, KK, Autog, Doc tour
    GROUP BY 
        S.sessionid
    HAVING
        numassigned < 4
    ORDER BY TR.trackname, S.sessionid;
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
    echo "<P>Click on the session id to view or edit the session's participants.<BR>\n";
    echo "<TABLE BORDER=1>";
    echo "<TR>\n";
    echo "    <TH class=\"small\">Track Name</TH>\n";
    echo "    <TH class=\"small\">Session ID</TH>\n";
    echo "    <TH class=\"small\">Title</TH>\n";
    echo "    <TH class=\"small\">No. of<BR>Participants<BR>Assigned</TH>\n";
    echo "    </TR>\n";
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    while ($resultrow) {
        $sessionid=htmlentities($resultrow['sessionid']);
        echo "<TR>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['trackname'])."</TD>\n";
        echo "    <TD class=\"small\"><A HREF=\"StaffAssignParticipants.php?selsess=";
            echo "$sessionid\">$sessionid</A></TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['title'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['numassigned'])."</TD>\n";
        echo "    </TR>\n";
        $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        }
    echo "</TABLE>\n";
    staff_footer();
?>
        concat('<a href=StaffAssignParticipants.php?selsess=',S.sessionid,'>', S.sessionid,'</a>') as Sessionid,
