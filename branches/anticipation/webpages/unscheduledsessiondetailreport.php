<?php
    $title="Scheduled Session Report";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    $_SESSION['return_to_page']="unscheduledsessiondetailreport.php";
    function topofpage() {
        staff_header("Unscheduled Session Report");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>List of unscheduled sessions and select characteristics.  Includes sessions of status\n";
        echo "\"vetted\", \"translate me\", \"scheduled\"(but not on schedule), and \"assigned\".</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $query = <<<EOD
SELECT
        S.sessionid, TR.trackname, TY.typename, PS.pubstatusname, S.title,
        if(S.progguiddesc="",S.pocketprogtext,S.progguiddesc) as description
    FROM
        Sessions S join
        Tracks TR using (trackid) join
        Types TY using (typeid) join
        PubStatuses PS using (pubstatusid)
    WHERE
        S.statusid in (3,4,5,6) and
        S.sessionid not in
            (Select distinct sessionid from Schedule)
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
    echo "    <TH class=\"small\">Sessionid</TH>\n";
    echo "    <TH class=\"small\">Track</TH>\n";
    echo "    <TH class=\"small\">Type</TH>\n";
    echo "    <TH class=\"small\">Pub Status</TH>\n";
    echo "    <TH class=\"small\">Title</TH>\n";
    echo "    <TH class=\"small\">Description</TH>\n";
    echo "    </TR>\n";
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    while ($resultrow) {
        echo "<TR>\n";
        echo "    <TD class=\"small\"><A HREF=\"StaffAssignParticipants.php?selsess={$resultrow['sessionid']}\">{$resultrow['sessionid']}</A></TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['trackname'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['typename'])."</TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['pubstatusname'])."</TD>\n";
        echo "    <TD class=\"small\"><A HREF=\"EditSession.php?id={$resultrow['sessionid']}\">".htmlentities($resultrow['title'])."</A></TD>\n";
        echo "    <TD class=\"small\">".htmlentities($resultrow['description'])."</TD>\n";
        echo "    </TR>\n";
        $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        }
    echo "</TABLE>\n";
    staff_footer();
?>
