<?php
    $title="Scheduled Session Report";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    $_SESSION['return_to_page']="scheduledsessiondetailreport.php";
    function topofpage() {
        staff_header("Scheduled Session Report");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>List of all scheduled sessions and select characteristics.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $query = <<<EOD
SELECT
        S.sessionid, TR.trackname, TY.typename, S.title, R.roomname,
        DATE_FORMAT(ADDTIME('2009-08-06 00:00:00',starttime),'%a %l:%i %p') as 'starttime',
        if(S.progguiddesc="",S.pocketprogtext,S.progguiddesc) as description
    FROM
        Sessions S join
        Schedule using (sessionid) join
        Tracks TR using (trackid) join
        Types TY using (typeid) join
        Rooms R using (roomid)
    ORDER BY
        TR.trackname, S.sessionid;
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
    echo "<P>Click on the session id to edit the session's participants.<BR>\n";
    echo "Click on the title to edit the session itself.<BR>\n";
    echo "<TABLE BORDER=1>";
    echo "<TR>\n";
    echo "    <TH class=\"small\">Session<BR>ID</TH>\n";
    echo "    <TH class=\"small\">Track</TH>\n";
    echo "    <TH class=\"small\">Type</TH>\n";
    echo "    <TH class=\"small\">Room Name</TH>\n";
    echo "    <TH class=\"small\">Day&nbsp;and&nbsp;Time</TH>\n";
    echo "    <TH class=\"small\">Title</TH>\n";
    echo "    <TH class=\"small\">Description</TH>\n";
    echo "    </TR>\n";
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    while ($resultrow) {
        echo "<TR>\n";
        echo "    <TD class=\"small\"><A HREF=\"StaffAssignParticipants.php?selsess={$resultrow['sessionid']}\">{$resultrow['sessionid']}</A></TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['trackname'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['typename'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['roomname'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['starttime'])."</TD>\n";
        echo "    <TD class=\"small\"><A HREF=\"EditSession.php?id={$resultrow['sessionid']}\">".htmlentities($resultrow['title'])."</A></TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['description'])."</TD>\n";
        echo "    </TR>\n";
        $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        }
    echo "</TABLE>\n";
    staff_footer();
?>
