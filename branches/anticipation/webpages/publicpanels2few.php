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
        echo "<P>List of all scheduled panels with fewer than 4 partipants.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $con_start_datetime=CON_START_DATIM;
    $query = <<<EOD
SELECT
        S.sessionid, S.title, S.progguiddesc, LS.languagestatusname, RM.roomname,
        DATE_FORMAT(ADDTIME('$con_start_datetime',SCH.starttime),'%a %k:%i') as 'starttime',
        DATE_FORMAT(S.duration,'%k:%i hrs:min') as dur, TR.trackname, 
        A.parts, A.countparts
    FROM
        Sessions S JOIN
        Tracks TR USING (trackid) JOIN
        LanguageStatuses LS USING (languagestatusid) JOIN
        Schedule SCH USING (sessionid) JOIN
        Rooms RM USING (roomid) LEFT JOIN
            (SELECT
                    SCH.sessionid, GROUP_CONCAT(P.pubsname SEPARATOR ', ') AS parts,
                    COUNT(P.badgeid) as countparts
                FROM
                    Schedule SCH JOIN
                    ParticipantOnSession USING (sessionid) JOIN
                    Participants P USING (badgeid)
                GROUP BY
                    SCH.scheduleid) as A USING (sessionid)
    WHERE
        S.typeid=1
# panels only
    HAVING
        A.countparts < 4 or isnull(A.countparts)
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
    echo "<TABLE BORDER=1>";
    echo "<COL width=18%><COL width=25%><COL width=25%><COL><COL>\n";
    echo "<TR>\n";
    echo "    <TH class=\"small border2112\" rowspan=3>Session ID</TH>\n";
    echo "    <TH class=\"small border2211\" colspan=4>Title</TH>\n";
    echo "    </TR>\n";
    echo "<TR>\n";
    echo "    <TH class=\"small border1211\" colspan=4>Description</TH>\n";
    echo "    </TR>\n";
    echo "<TR>\n";
    echo "    <TH class=\"small border1111\">Language</TH>\n";
    echo "    <TH class=\"small border1111\">Where</TH>\n";
    echo "    <TH class=\"small border1111\">Time</TH>\n";
    echo "    <TH class=\"small border1211\">Duration</TH>\n";
    echo "    </TR>\n";
    echo "<TR>\n";
    echo "    <TH class=\"small border1122\">Track</TH>\n";
    echo "    <TH class=\"small border1221\" colspan=4>Other Participants</TH>\n";
    echo "    </TR>\n";
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    while ($resultrow) {
        echo "<TR>\n";
        echo "    <TD class=\"small border2112\" rowspan=3>".htmlentities($resultrow['sessionid'])."</TD>\n";
        echo "    <TD class=\"small border2211\" colspan=4>".htmlentities($resultrow['title'])."</TD>\n";
        echo "    </TR>\n";
        echo "<TR>\n";
        echo "    <TD class=\"small border1211\" colspan=4>".htmlentities($resultrow['progguiddesc'])."</TD>\n";
        echo "    </TR>\n";
        echo "<TR>\n";
        echo "    <TD class=\"small border1111\">".htmlentities($resultrow['languagename'])."</TD>\n";
        echo "    <TD class=\"small border1111\">".htmlentities($resultrow['roomname'])."</TD>\n";
        echo "    <TD class=\"small border1111\">".htmlentities($resultrow['starttime'])."</TD>\n";
        echo "    <TD class=\"small border1211\">".htmlentities($resultrow['dur'])."</TD>\n";
        echo "    </TR>\n";
        echo "<TR>\n";
        echo "    <TD class=\"small border1122\">".htmlentities($resultrow['trackname'])."</TD>\n";
        echo "    <TD class=\"small border1221\" colspan=4>".htmlentities($resultrow['parts'])."</TD>\n";
        echo "    </TR>\n";
        $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        }
    echo "</TABLE>\n";
    staff_footer();
?>
