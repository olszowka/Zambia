<?php
    $title="Participant Assignment Report";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link, $lbadgeid, $pubsname, $attending, $sessionid, $title;
    $_SESSION['return_to_page']="participantassignmentreport.php";
    function topofpage() {
        staff_header("Participant Assignment Report");
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>List of all participants and their assigned sessions.</P>\n";
        }
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }
    function parseresultrow($resultrow) {
        global $lbadgeid, $pubsname, $attending, $sessionid, $title;
        $lbadgeid=htmlentities($resultrow['badgeid']);
        $pubsname=htmlentities($resultrow['pubsname']);
        $i=$resultrow['interested'];
        if ($i=="") {
                $attending="Unknown";
                }
            else {
                switch ($i) {
                    case 0:
                        $attending="Unknown";
                        break;
                    case 1:
                        $attending="Yes";
                        break;
                    case 2:
                        $attending="No";
                        break;
                    case 3:
                        $attending="Duplicate";
                        break;
                    default:
                        $attending="No";
                    }
                }
        $sessionid=$resultrow['sessionid'];
        $title=htmlentities($resultrow['title']);
        }
    $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested, POS.sessionid, S.title
    FROM
        ParticipantOnSession POS join
        Sessions S using (sessionid) right join
        Participants P using (badgeid)
    WHERE
        badgeid in (
             SELECT badgeid from Participants where interested is null or interested in (0,1)
             UNION
             SELECT distinct badgeid from ParticipantOnSession
             )
    ORDER BY
        P.pubsname;
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
    echo "<P>Click on the session id to edit the session's participants.</P>\n";
    echo "<P>Hover the mouse over the session id to view the session's title.</P>\n";
    echo "<TABLE BORDER=1>";
    echo "<TR>\n";
    echo "    <TH>Badgeid</TH>\n";
    echo "    <TH>Participant Name</TH>\n";
    echo "    <TH>Attending</TH>\n";
    echo "    <TH>Session ID(s)</TH>\n";
    echo "    </TR>\n";
    $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
    parseresultrow($resultrow);
    $oldbadgeid=$lbadgeid;
    echo "<TR><TD>$lbadgeid</TD><TD>$pubsname</TD><TD>$attending</TD><TD>";
    while (true) {
        if ($oldbadgeid==$lbadgeid) {
                if ($sessionid=="") {
                        echo "&nbsp;";
                        }
                    else {
                        echo "<A HREF=\"StaffAssignParticipants.php?selsess=$sessionid\" TITLE=\"$title\">$sessionid</A>, ";
                        }
                $resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
                if (!$resultrow) break;
                parseresultrow($resultrow);
                }
            else {
                echo "</TD></TR>\n";
                $oldbadgeid=$lbadgeid;
                echo "<TR><TD>$lbadgeid</TD><TD>$pubsname</TD><TD>$attending</TD><TD>";
                }
        }
    echo "</TD></TR></TABLE>\n";
    staff_footer();
?>