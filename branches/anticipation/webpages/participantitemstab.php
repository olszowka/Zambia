<?php
    header("Content-type: text/html;");

    require_once ('db_functions.php');

    function getParticipantInfo($id) {
        $SQL = "select pubsname from Participants where badgeid = '".$id."'";
        $result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
        if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
        $row = mysql_fetch_array($result,MYSQL_ASSOC);

        echo "<table>";
        echo "<tr>";
        echo "<td colspan=5>" . htmlentities($row[pubsname]) . "</td>";
        echo "</tr>";
        echo "</table>";
    }

    function getParticipantFullSchedule($id) {
        $con_start_datim=CON_START_DATIM;
        $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, CD.email, B.starttime, B.title, B.parts,
        if(C.pubsname=P.pubsname,"Yourself",if(C.pubsname is not null, C.pubsname, "<Not Available>")) as moderator,
        B.description, B.dur, B.roomname, B.languagestatusname, B.trackname,
        if(isnull(D.svcs),"None",D.svcs) as services, B.sessionid
    FROM
        Participants P join
        CongoDump CD using (badgeid) join
        ParticipantOnSession POS using (badgeid) join
#          Most info for each session - B
           (SELECT
                    S.sessionid, S.title, if(S.progguiddesc!="",S.progguiddesc,S.pocketprogtext) as description,
                    DATE_FORMAT(ADDTIME('$con_start_datim',SCH.starttime),'%a %k:%i') as starttime,
                    SCH.starttime as starttime2, DATE_FORMAT(S.duration,'%k:%i hrs:min') as dur,
                    R.roomname, L.languagestatusname, T.trackname, A.parts
                FROM
                    Sessions S join
                    Schedule SCH using (sessionid) join
                    Tracks T using (trackid) join
                    LanguageStatuses L using (languagestatusid) join
                    Rooms R using (roomid) join
#                      Participants for each session - A
                       (SELECT
                                SCH.sessionid, GROUP_CONCAT(P.pubsname SEPARATOR ', ') AS parts
                            FROM
                                Schedule SCH join
                                ParticipantOnSession using (sessionid) join
                                Participants P using (badgeid) join
                                CongoDump CD using (badgeid)
                            GROUP BY
                                SCH.scheduleid) as A using (sessionid)
                 ) as B using (sessionid) left join
#          Moderator for each session - C
               (SELECT
                        P2.pubsname, POS2.sessionid
                    FROM
                        Participants P2 join
                        ParticipantOnSession POS2 using (badgeid)
                    WHERE
                        POS2.moderator=1) as C using (sessionid) left join
#          Services for each session - D
           (SELECT
                    S.sessionid, GROUP_CONCAT(SV.servicename SEPARATOR ', ') as svcs
                FROM
                    Sessions S join
                    SessionHasService SHS using (sessionid) join
                    Services SV using (serviceid)
                GROUP BY
                    sessionid) as D using (sessionid)
    WHERE
        P.badgeid='$id'
    ORDER BY B.starttime2;
EOD;
        $result = mysql_query( $query ) or die("Couldnt execute query.".mysql_error());
        if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
        $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        echo "<table>";
        while ($resultrow) {
        echo "<tr>";
        	echo "<td>".htmlentities($resultrow['starttime'])."</td>";
            echo "<td>".htmlentities($resultrow['roomname'])."</td>";
            echo "<td>".htmlentities($resultrow['title'])."</td>";
            echo "<td>(".htmlentities($resultrow['languagestatusname']).")</td>";
            //echo "Moderator:  ".htmlentities($resultrow['moderator']);
//            echo "<br/>\n";
            $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
        echo "</tr>";
        }
        echo "</table>";
            
    }

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

echo "<div>";
if ($id) {
echo "<button type='button' onclick=\"$('#printScheduleSummary').jqprint();\" style=\"float:right;\">Print</button>";
echo "<div id='printScheduleSummary'>";
getParticipantInfo($id);
getParticipantFullSchedule($id);
echo "</div>";
} else {
    echo "No data";
}
echo "</div>";

?>

