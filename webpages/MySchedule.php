<?php
    $title="My Schedule";
    require ('db_functions.php'); //define database functions
    require ('data_functions.php');
    require_once('ParticipantFooter.php');
    require_once('renderMySessions2.php');
    require ('PartCommonCode.php'); // initialize db; check login;
    if (!may_I('my_schedule')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    // set $badgeid from session
    $query= <<<EOD
    SELECT POS.sessionid, trackname, title, roomname, pocketprogtext,
    DATE_FORMAT(ADDTIME('2006-01-13 00:00:00', starttime),'%a %l:%i %p') as 'Start Time',
    left(duration,5) as 'Duration', persppartinfo, notesforpart FROM
    ParticipantOnSession POS, Sessions S, Rooms R, Schedule SCH, Tracks T
    where badgeid="$badgeid" and POS.sessionid = S.sessionid and
    R.roomid = SCH.roomid and S.sessionid = SCH.sessionid and S.trackid = T.trackid
    ORDER BY starttime
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $schdrows=mysql_num_rows($result);
    for ($i=0; $i<$schdrows; $i++) {
        list($schdarray[$i]["sessionid"],$schdarray[$i]["trackname"],
            $schdarray[$i]["title"],$schdarray[$i]["roomname"],$schdarray[$i]["pocketprogtext"],
            $schdarray[$i]["starttime"],$schdarray[$i]["duration"],$schdarray[$i]["persppartinfo"],
            $schdarray[$i]["notesforpart"])=mysql_fetch_array($result, MYSQL_NUM);
        }
    $query= <<<EOD
    SELECT Sess.sessionid, CD.badgename, POS.moderator, PSI.comments FROM
    ParticipantOnSession POS, CongoDump CD, 
    (SELECT sessionid FROM ParticipantOnSession WHERE badgeid="$badgeid") AS Sess
    LEFT JOIN ParticipantSessionInterest PSI ON Sess.sessionid = PSI.sessionid
    and POS.badgeid = PSI.badgeid WHERE Sess.sessionid = POS.sessionid and 
    POS.badgeid = CD.badgeid order by sessionid, moderator desc
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $partrows=mysql_num_rows($result);
    for ($i=0; $i<$partrows; $i++) {
        list($partarray[$i]["sessionid"],$partarray[$i]["badgename"],$partarray[$i]["moderator"],
            $partarray[$i]["comments"])=mysql_fetch_array($result, MYSQL_NUM);
        }
    $query="SELECT message FROM CongoDump C LEFT JOIN RegTypes R on C.regtype=R.regtype ";
    $query.="WHERE C.badgeid=\"$badgeid\"";
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $row=mysql_fetch_array($result, MYSQL_NUM);
    $regmessage=$row[0];
    $query="SELECT count(*) from ParticipantOnSession POS, Schedule SCH WHERE ";
    $query.="POS.sessionid=SCH.sessionid and badgeid=\"$badgeid\"";
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $row=mysql_fetch_array($result, MYSQL_NUM);
    $poscount=$row[0];
    if (!$regmessage) {
        if ($poscount>=3) {
                $regmessage="not registered.</span><span>  Programming has requested a comp membership for you";
                }
            else {
                $regmessage="not registered.</span><span>  Panelists on 3 or more panels receive complementary memberships from Programming.  If you are interested in increasing your number of panels to take advantage of this, please contact us and we will work with you to see if it is possible.  If you are expecting a comp from helping another division, that will show up here shortly after registration processes it.  Please contact that division or registration with questions";
                }
        }
    participant_header($title);
    echo "<P>Below is the list of all the panels for which you are scheduled.  If you need any changes";
    echo " to this schedule please contact <A 
HREF=\"mailto:<?php echo PROGRAM_EMAIL; ?>\"><?php echo 
PROGRAM_EMAIL; ?></A>.\n";
    echo "<P>In order to put together the entire schedule, we had to schedule some panels outside of the times that certain panelists requested.  If this happened to you, we would love to have you on the panel, but understand if you cannot make it.  Please let us know if you cannot.\n";
    echo "<P>Several of the panels we are running this year were extremely popular with over 20 potential panelists signing up.  Choosing whom to place on those panels was difficult.  There is always a possibility that one of the panelists currently scheduled will be unavailable so feel free to check with us to see if a space has opened up on a panel on hwhich you'd still like to participate.\n";
    echo "<P>To facilitate communication yet also preserve privacy, we provide you the option of putting your contact information in the comments field for each panel (under the <A HREF=\"./my_sessions2.php\">\"My Panel Interests\"</A> tab).  That will expose it to other panelists who can then email or call you as appropriate to discuss the panel in advance.  If you check back in a day or two you may find other panelists' information.\n";
    echo "<P>Your registration status is <SPAN class=\"hilit\">$regmessage.</SPAN>\n";
    echo "<P>Thank you -- <A HREF=\"mailto: <?php echo PROGRAM_EMAIL; 
?>\"> Programming </a>\n";
    echo "    <TABLE>\n";
    echo "        <COL><COL><COL><COL><COL width=\"12%\"><COL width=\"15%\"><COL width=\"15%\">\n";
    for ($i=0; $i<$schdrows; $i++) {
        echo "        <TR>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["sessionid"]."</TD>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["trackname"]."</TD>\n";
        echo "            <TD colspan=2 class=\"hilit\">".htmlspecialchars($schdarray[$i]["title"])."</TD>\n";
        // echo "            <TD class=\"hilit\">".$schdarray[$i]["roomname"]."</TD>\n";
        echo "            <TD class=\"hilit\">&nbsp;</TD>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["starttime"]."</TD>\n";
        echo "            <TD class=\"hilit\">Duration: ".$schdarray[$i]["duration"]."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($schdarray[$i]["pocketprogtext"])."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($schdarray[$i]["persppartinfo"])."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($schdarray[$i]["notesforpart"])."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=2 class=\"usrinp\">Panelists' Badge Names</TD>\n";
        echo "            <TD colspan=4 class=\"usrinp\">Their Comments</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
        for ($j=0; $j<$partrows; $j++) {
            if ($partarray[$j]["sessionid"]!=$schdarray[$i]["sessionid"]) {
                continue;
                }
            if ($partarray[$j+1]["sessionid"]==$schdarray[$i]["sessionid"]) {
                    $class="border0010";
                    }
                else {
                    $class="";
                    }
            echo "        <TR><TD>&nbsp;</TD>\n";
            echo "            <TD colspan=2 class=\"$class\">".htmlspecialchars($partarray[$j]["badgename"]);
            if ($partarray[$j]["moderator"]) {
                echo " (mod) ";
                }
            echo "</TD>\n";
            echo "            <TD colspan=4 class=\"$class\">".htmlspecialchars(fix_slashes($partarray[$j]["comments"]));
            echo "</TD>\n";
            echo "            </TR>\n";
            }
        echo "        <TR><TD colspan=7 class=\"border0020\">&nbsp;</TD></TR>\n";
        echo "        <TR><TD colspan=7 class=\"border0000\">&nbsp;</TD></TR>\n";
        }
    echo "        </TABLE>\n"; 
    participant_footer();
?>
