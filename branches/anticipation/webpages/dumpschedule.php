<?php
    $title="Dump Schedule to TDF";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    function topofpage() {
        staff_header("Dump Schedule to TDF");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Regenerate file containing whole schedule to schedule.tdf</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $con_start_datetime=CON_START_DATIM;
    $schedule_columns = array('session_id', 'publication_number', 'title', 'track', 'start_date&time', 'duration', 'location',
        'for_publication', 'language', 'participants', 'moderator');

    $filename = "scheduledump.TDF";
    $fp = fopen("exports/$filename", "w+");
    if (!$fp) {
        $message="Can't open exports/$filename<BR>";
        RenderError($title,$message);
        exit ();
        }

    fwrite($fp, implode("\t", $schedule_columns));
    fwrite($fp, "\n");
// Populate $rooms array from db
    $query = "SELECT roomid, roomname from Rooms";
    if (($result=mysql_query($query,$link))===false OR 0==mysql_num_rows($result)) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        RenderError($title,$message);
        exit ();
        }
    while ($resultrow=mysql_fetch_array($result,MYSQL_ASSOC)) {
        $rooms[$resultrow['roomid']]=$resultrow['roomname'];
        }
// Populate $tracks array from db
    $query = "SELECT trackid, trackname from Tracks";
    if (($result=mysql_query($query,$link))===false OR 0==mysql_num_rows($result)) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        RenderError($title,$message);
        exit ();
        }
    while ($resultrow=mysql_fetch_array($result,MYSQL_ASSOC)) {
        $tracks[$resultrow['trackid']]=$resultrow['trackname'];
        }
// Populate $languages array from db
    $query = "SELECT languagestatusid, languagestatusname from LanguageStatuses";
    if (($result=mysql_query($query,$link))===false OR 0==mysql_num_rows($result)) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        RenderError($title,$message);
        exit ();
        }
    while ($resultrow=mysql_fetch_array($result,MYSQL_ASSOC)) {
        $languages[$resultrow['languagestatusid']]=$resultrow['languagestatusname'];
        }
// Populate $pubstatus array from db
    $query = "SELECT pubstatusid, pubstatusname from PubStatuses";
    if (($result=mysql_query($query,$link))===false OR 0==mysql_num_rows($result)) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        RenderError($title,$message);
        exit ();
        }
    while ($resultrow=mysql_fetch_array($result,MYSQL_ASSOC)) {
        $pubstatus[$resultrow['pubstatusid']]=$resultrow['pubstatusname'];
        }
        $query= <<<EOD
SELECT
        S.sessionid, S.pubsno, S.title, S.trackid,
        DATE_FORMAT(ADDTIME('$con_start_datetime',starttime),'%m/%d/%Y %H:%i:%s') as starttime,
        DATE_FORMAT(S.duration,'%k:%i hrs:min') as duration, SCH.roomid, S.pubstatusid,
        S.languagestatusid,
        if(group_concat(P.pubsname) is NULL,'',group_concat(P.pubsname separator', ')) as participants,
        if(A.pubsname is NULL,'NONE',A.pubsname) as moderator
    FROM
        Sessions S join
        Schedule SCH using(sessionid) left join
        ParticipantOnSession POS using (sessionid) left join
        Participants P using (badgeid) left join
#          Moderator for each session - A
           (SELECT
                    P2.pubsname, POS2.sessionid
                FROM
                    Participants P2 join
                    ParticipantOnSession POS2 using (badgeid)
                WHERE
                    POS2.moderator=1) as A using (sessionid)
    GROUP BY
        sessionid
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
//
    $count=0;
    while ($resultrow=mysql_fetch_array($result,MYSQL_ASSOC)) {
        $csv_line = '';
        $csv_line .= $resultrow['sessionid']."\t";
        $csv_line .= $resultrow['pubsno']."\t";
        $csv_line .= $resultrow['title']."\t";
        $csv_line .= $tracks[$resultrow['trackid']]."\t";
        $csv_line .= $resultrow['starttime']."\t";
        $csv_line .= $resultrow['duration']."\t";
        $csv_line .= $rooms[$resultrow['roomid']]."\t";
        $csv_line .= $pubstatus[$resultrow['pubstatusid']]."\t";
        $csv_line .= $languages[$resultrow['languagestatusid']]."\t";
        $csv_line .= $resultrow['participants']."\t";
        $csv_line .= $resultrow['moderator']."\n";
        fwrite($fp, $csv_line);
        $count++;
        }
    fclose($fp);
    echo "$count record(s) written to file.<BR>\n";
    staff_footer();
?>
