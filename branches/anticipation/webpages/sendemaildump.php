<?php
    $title="Send Email Dump";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    function topofpage() {
        staff_header("Send Email Dump");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Regenerate table containing dump to send email.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    $con_start_datetime=CON_START_DATIM;
    $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, CD.email,
        if(P.editedbio is not null and P.editedbio!='',P.editedbio,
            if(P.bio is not null and P.bio!='', P.bio, "<Not Available>")) as bio1,
        if(P.scndlangbio is not null and P.scndlangbio!='',P.scndlangbio, "<Not Available>") as bio2,
        B.sessionid, B.title, B.description,
        B.starttime, B.dur, B.languagestatusname, B.trackname, B.parts,
        if(C.pubsname=P.pubsname,"Yourself",if(C.pubsname is not null, C.pubsname, "<Not Available>")) as moderator
    FROM
        Participants P join
        CongoDump CD using (badgeid) join
        ParticipantOnSession POS using (badgeid) join
           (SELECT
                    S.sessionid, S.title, if(S.progguiddesc!="",S.progguiddesc,S.pocketprogtext) as description,
                    DATE_FORMAT(ADDTIME('$con_start_datetime',SCH.starttime),'%a %l:%i %p') as starttime,
                    DATE_FORMAT(S.duration,'%l:%i hrs:min') as dur,
                    L.languagestatusname, T.trackname, A.parts
                FROM
                    Sessions S join
                    Schedule SCH using (sessionid) join
                    Tracks T using (trackid) join
                    LanguageStatuses L using (languagestatusid) join
                       (SELECT
                                SCH.sessionid, GROUP_CONCAT(P.pubsname SEPARATOR ', ') AS parts
                            FROM
                                Schedule SCH join
                                ParticipantOnSession using (sessionid) join
                                Participants P using (badgeid)
                            GROUP BY
                                SCH.scheduleid) as A using (sessionid)
                 ) as B using (sessionid) left join
           (SELECT
                    P2.pubsname, POS2.sessionid
                FROM
                    Participants P2 join
                    ParticipantOnSession POS2 using (badgeid)
                WHERE
                    POS2.moderator=1) as C using (sessionid)
    ORDER BY P.badgeid, B.sessionid;
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
// *** add some code here ***
// trucate existing email dump table
//
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    $oldbadgeid=$resultrow['badgeid'];
    $atendofquery=false;
//  outer loop for each participant
    while (!$atendofquery) {
        $email_text="";
    //  add some code here ***
    //  Put introductory text into $email_text
    //  Put blurb into $email_text regarding participants name for publication, bio (English) and bio2 (French).
    //  Now put blurb introducing scheduled sessions
        while (true) {
        //  put blurb for an individual session including formatting of query results
            $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
            if (!$resultrow) {
                $atendofquery=true;
                break;
                }
            if ($oldbadgeid!=$resultrow['badgeid']) {
                break;
                }
            }
    //  put blurb to wrap up email
    //  insert record for this participant into email dump table
        }
// write confirmation of success to output stream
    staff_footer();
?>
