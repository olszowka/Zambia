<?php
    $title="My Schedule";
    require ('PartCommonCode.php'); // initialize db; check login;
    $CON_START_DATIM=CON_START_DATIM; //make it a variable so it will be substituted
    $PROGRAM_EMAIL=PROGRAM_EMAIL; //make it a variable so it will be substituted
    require_once('ParticipantHeader.php');
    // require_once('renderMySessions2.php');
    if (!may_I('my_schedule')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    // set $badgeid from session
    $query= <<<EOD
SELECT
            POS.sessionid,
            T.trackname,
            S.title,
            R.roomname,
            S.progguiddesc,
            DATE_FORMAT(ADDTIME('$CON_START_DATIM', SCH.starttime),'%a %l:%i %p') as 'Start Time',
            left(duration,5) as 'S.Duration',
            S.persppartinfo,
            S.notesforpart
    FROM
            ParticipantOnSession POS
       JOIN Sessions S USING (sessionid)
       JOIN Schedule SCH USING (sessionid)
       JOIN Rooms R USING (roomid)
       JOIN Tracks T USING (trackid)
    WHERE
            POS.badgeid="$badgeid"
    ORDER BY
            SCH.starttime
EOD;
    //error_log("Zambia: $query");
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $schdrows=mysql_num_rows($result);
    for ($i=0; $i<$schdrows; $i++) {
        list(
            $schdarray[$i]["sessionid"],
            $schdarray[$i]["trackname"],
            $schdarray[$i]["title"],
            $schdarray[$i]["roomname"],
            $schdarray[$i]["progguiddesc"],
            $schdarray[$i]["starttime"],
            $schdarray[$i]["duration"],
            $schdarray[$i]["persppartinfo"],
            $schdarray[$i]["notesforpart"])
                =mysql_fetch_array($result, MYSQL_NUM);
        }
    $query= <<<EOD
SELECT
            POS.sessionid, CD.badgename, P.pubsname, 
            IF (P.share_email=1,CD.email,null) 'email',
            POS.moderator, PSI.comments
    FROM
            ParticipantOnSession POS
       JOIN CongoDump CD USING(badgeid)
       JOIN Participants P USING(badgeid)
  LEFT JOIN ParticipantSessionInterest PSI USING(sessionid,badgeid)
    WHERE
            POS.sessionid in
                    (select sessionid from ParticipantOnSession where badgeid='$badgeid')
            ORDER BY sessionid, moderator desc
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $partrows=mysql_num_rows($result);
    for ($i=0; $i<$partrows; $i++) {
        list(
            $partarray[$i]["sessionid"],
            $partarray[$i]["badgename"],
            $partarray[$i]["pubsname"],
            $partarray[$i]["email"],
            $partarray[$i]["moderator"],
	        $partarray[$i]["comments"])
	            =mysql_fetch_array($result, MYSQL_NUM);
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
    echo " to this schedule please contact <A HREF=\"mailto:$PROGRAM_EMAIL\"> Programming </A>.\n";
    echo "<P>In order to put together the entire schedule, we had to schedule some panels outside of the times that certain panelists requested.  If this happened to you, we would love to have you on the panel, but understand if you cannot make it.  Please let us know if you cannot.\n";
    echo "<P>Several of the panels we are running this year were extremely popular with over 20 potential panelists signing up.  Choosing whom to place on those panels was difficult.  There is always a possibility that one of the panelists currently scheduled will be unavailable so feel free to check with us to see if a space has opened up on a panel on which you'd still like to participate.\n";
    echo "<P>Your registration status is <SPAN class=\"hilit\">$regmessage.</SPAN>\n";
    echo "<P>Thank you -- <A HREF=\"mailto:$PROGRAM_EMAIL\"> Programming </a>\n";
    echo "    <TABLE class=\"table table-condensed\">\n";
    echo "        <COL><COL width=\"30%\"><COL width=\"20%\"><COL><COL width=\"6%\"><COL><COL width=\"18%\">\n";
    for ($i=0; $i<$schdrows; $i++) {
        echo "        <TR class=\"label\">\n";
        echo "            <TD class=\"badge\">".$schdarray[$i]["sessionid"]."</TD>\n";
        echo "            <TD class=\"sched_hd\">".htmlspecialchars($schdarray[$i]["title"])."</TD>\n";
        echo "            <TD class=\"sched_hd\">".$schdarray[$i]["roomname"]."</TD>\n";
        echo "            <TD class=\"sched_hd\">".$schdarray[$i]["trackname"]."</TD>\n";
        echo "            <TD class=\"sched_hd\">&nbsp;</TD>\n";
        echo "            <TD class=\"sched_hd\">".$schdarray[$i]["starttime"]."</TD>\n";
        echo "            <TD class=\"sched_hd\">Duration: ".$schdarray[$i]["duration"]."</TD>\n";
        echo "            </TR>\n";
        if (($x=$schdarray[$i]["progguiddesc"])!='') {
            echo "        <TR><TD>&nbsp;</TD>\n";
            echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($x)."</TD>\n";
            echo "            </TR>\n";
            }
        if (($x=$schdarray[$i]["persppartinfo"])!='') {
            echo "        <TR class=\"alert\"><TD>&nbsp;</TD>\n";
            echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($x)."</TD>\n";
            echo "            </TR>\n";
            }
        if (($x=$schdarray[$i]["notesforpart"])!='') {
            echo "        <TR><TD>&nbsp;</TD>\n";
            echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($x)."</TD>\n";
            echo "            </TR>\n";
            }
        echo "        <TR><TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD class=\"badge\">Panelists' Publication Names (Badge Names)</TD>\n";
        echo "            <TD class=\"badge\">Email addresses</TD>\n";
        echo "            <TD colspan=4 class=\"badge\">Comments</TD>\n";
        echo "            </TR>\n";
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
            echo "            <TD class=\"$class\">".htmlspecialchars($partarray[$j]["pubsname"]);
	    if ($partarray[$j]["pubsname"]!=$partarray[$j]["badgename"]) echo " (".htmlspecialchars($partarray[$j]["badgename"]).")";
            if ($partarray[$j]["moderator"]) {
                echo " <I>mod</I> ";
                }
            echo "</TD>\n";
            echo "            <TD class=\"$class\">".htmlspecialchars(fix_slashes($partarray[$j]["email"]));
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
