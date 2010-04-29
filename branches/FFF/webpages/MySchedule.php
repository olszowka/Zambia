<?php
    $title="My Schedule";
    require ('PartCommonCode.php'); // initialize db; check login;
    require_once('ParticipantHeader.php');
    $ConStartDatim=CON_START_DATIM; //make it a variable so it will be substituted
    $ProgramEmail=PROGRAM_EMAIL; //Use it a variable locally
    // require_once('renderMySessions2.php');
    if (!may_I('my_schedule')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    // set $badgeid from session

    ## General presenter information
    // Gather the comments offered on this presenter into pcommentarray, if any
    $query = <<<EOD
SELECT
    comment
  FROM
      CommentsOnParticipants
  WHERE
    badgeid="$badgeid"
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $pcommentrows=mysql_num_rows($result);
    for ($i=0; $i<$pcommentrows; $i++) {
        $pcommentarray[$i]=mysql_fetch_assoc($result);
        }

    // Get the state of registration into $regmessage
    $query = <<<EOD
SELECT
    message
  FROM
      CongoDump C
    LEFT JOIN RegTypes R on C.regtype=R.regtype
  WHERE
    C.badgeid="$badgeid"
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $row=mysql_fetch_array($result, MYSQL_NUM);
    $regmessage=$row[0];

    // Get the number of pannels the participant is on
    $query = <<<EOD
SELECT
    count(*) 
  FROM
      ParticipantOnSession POS,
      Schedule SCH
  WHERE
    POS.sessionid=SCH.sessionid and
    badgeid=$badgeid
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $row=mysql_fetch_array($result, MYSQL_NUM);
    $poscount=$row[0];

    // Message about state of registration, (on more than 3 pannels programming will ask for a comp).
    if (!$regmessage) {
        if ($poscount>=3) {
                $regmessage="not registered.</span><span>  Programming has requested a comp membership for you";
                }
            else {
                $regmessage="not registered.</span><span>  Panelists on 3 or more panels receive complementary memberships from Programming.  If you are interested in increasing your number of panels to take advantage of this, please contact us and we will work with you to see if it is possible.  If you are expecting a comp from helping another division, that will show up here shortly after registration processes it.  Please contact that division or registration with questions";
                }
        }

    ## Schedule information
    // Build the schedule of classes into schdarray
    $query = <<<EOD
SELECT
    POS.sessionid,
    trackname,
    title,
    roomname,
    progguiddesc,
    DATE_FORMAT(ADDTIME('$ConStartDatim', starttime),'%a %l:%i %p') as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END
      AS Duration,
    persppartinfo,
    notesforpart
  FROM
      ParticipantOnSession POS,
      Sessions S,
      Rooms R,
      Schedule SCH,
      Tracks T
  WHERE
    badgeid="$badgeid" and
    POS.sessionid = S.sessionid and
    R.roomid = SCH.roomid and
    S.sessionid = SCH.sessionid and
    S.trackid = T.trackid
  ORDER BY
    starttime
EOD;
    //error_log("Zambia: $query");
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $schdrows=mysql_num_rows($result);
    for ($i=0; $i<$schdrows; $i++) {
        $schdarray[$i]=mysql_fetch_assoc($result);
	$schdarray[$i]["feedbackgraph"]=sprintf("Feedback/%s.jpg",$schdarray[$i]["sessionid"]);
        }

    // Build the list of individuals associated with each class into partarray
    $query = <<<EOD
SELECT
    POS.sessionid,
    CD.badgename,
    P.pubsname,
    POS.moderator,
    POS.volunteer,
    POS.announcer,
    PSI.comments AS PresenterComments
  FROM
      ParticipantOnSession POS
    JOIN CongoDump CD USING(badgeid)
    JOIN Participants P USING(badgeid)
    LEFT JOIN ParticipantSessionInterest PSI USING(sessionid,badgeid)
  WHERE
    POS.sessionid in (SELECT
                          sessionid 
                        FROM
                            ParticipantOnSession
                        WHERE badgeid='$badgeid')
  ORDER BY
    sessionid,
    moderator DESC
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $partrows=mysql_num_rows($result);
    for ($i=0; $i<$partrows; $i++) {
        $partarray[$i]=mysql_fetch_assoc($result);
        }

    // Build the list of comments associated with each class and this participant into ccommentarray
    $query = <<<EOD
SELECT
    sessionid,
    comment
  FROM
      CommentsOnSessions
  WHERE
    sessionid in (SELECT
                      sessionid 
                    FROM
                        ParticipantOnSession
                    WHERE badgeid='$badgeid')
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $ccommentrows=mysql_num_rows($result);
    for ($i=0; $i<$ccommentrows; $i++) {
        $ccommentarray[$i]=mysql_fetch_assoc($result);
        }

    ## Begin the presentation of the information
    participant_header($title);
    echo "<P>Below is the list of all the panels for which you are scheduled.  If you need any changes";
    echo " to this schedule please contact <A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n";
    echo "<P>In order to put together the entire schedule, we had to schedule some panels outside of the times that certain panelists requested.  If this happened to you, we would love to have you on the panel, but understand if you cannot make it.  Please let us know if you cannot.\n";
    echo "<P>Several of the panels we are running this year were extremely popular with over 20 potential panelists signing up.  Choosing whom to place on those panels was difficult.  There is always a possibility that one of the panelists currently scheduled will be unavailable so feel free to check with us to see if a space has opened up on a panel on hwhich you'd still like to participate.\n";
    echo "<P>To facilitate communication yet also preserve privacy, we provide you the option of putting your contact information in the comments field for each panel (under the <A HREF=\"./my_sessions2.php\">\"My Panel Interests\"</A> tab).  That will expose it to other panelists who can then email or call you as appropriate to discuss the panel in advance.  If you check back in a day or two you may find other panelists' information.\n";
    echo "<P><A HREF=\"MyScheduleIcal.php\">Here</A> is an iCal (Calendar standard) calendar of your schedule.\n";
    echo "<P>Your registration status is <SPAN class=\"hilit\">$regmessage.</SPAN>\n";
    if ($pcommentrows > 0) {
      echo "<P>General <A HREF=#genfeedback>Feedback</A> received about or for you.";
      }
    echo "<P>Thank you -- <A HREF=\"mailto:$ProgramEmail\">Programming</a>\n";
    echo "    <TABLE>\n";
    echo "        <COL><COL width=\"30%\"><COL width=\"20%\"><COL><COL width=\"6%\"><COL><COL width=\"18%\">\n";
    for ($i=0; $i<$schdrows; $i++) {
        echo "        <TR>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["sessionid"]."</TD>\n";
        echo "            <TD class=\"hilit\">".htmlspecialchars($schdarray[$i]["title"])."</TD>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["roomname"]."</TD>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["trackname"]."</TD>\n";
        echo "            <TD class=\"hilit\">&nbsp;</TD>\n";
        echo "            <TD class=\"hilit\">".$schdarray[$i]["starttime"]."</TD>\n";
        echo "            <TD class=\"hilit\">Duration: ".$schdarray[$i]["duration"]."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($schdarray[$i]["progguiddesc"])."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($schdarray[$i]["persppartinfo"])."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD colspan=6 class=\"border0010\">".htmlspecialchars($schdarray[$i]["notesforpart"])."</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD colspan=7 class=\"smallspacer\">&nbsp;</TD></TR>\n";
        echo "        <TR><TD>&nbsp;</TD>\n";
        echo "            <TD class=\"usrinp\">Panelists' Publication Names (Badge Names)</TD>\n";
        echo "            <TD colspan=5 class=\"usrinp\">Their Comments</TD>\n";
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
            echo "            <TD class=\"$class\">".htmlspecialchars($partarray[$j]["pubsname"]);
	    if ($partarray[$j]["pubsname"]!=$partarray[$j]["badgename"]) echo " (".htmlspecialchars($partarray[$j]["badgename"]).")";
            if ($partarray[$j]["moderator"]) {
                echo " <I>mod</I> ";
                }
            if ($partarray[$j]["volunteer"]) {
                echo " <I>volunteer</I> ";
                }
            if ($partarray[$j]["announcer"]) {
                echo " <I>announcer</I> ";
                }
            echo "</TD>\n";
            echo "            <TD colspan=5 class=\"$class\">".htmlspecialchars(fix_slashes($partarray[$j]["PresenterComments"]));
            echo "</TD>\n";
            echo "            </TR>\n";
	    }
	if (file_exists($schdarray[$i]["feedbackgraph"])) {
            $class="border0010";
	    echo "        <TR><TD>&nbsp;</TD>\n            <TD colspan=6><hr>Feedback graph from surveys:<br>";
	    echo sprintf("<img src=\"%s\"></TD>            </TR>\n",$schdarray[$i]["feedbackgraph"]);
	    }
	for ($k=0; $k<$ccommentrows; $k++) {
	  if ($ccommentarray[$k]["sessionid"]!=$schdarray[$i]["sessionid"]) {
	    continue;
	    }
	    echo "        <TR><TD>&nbsp;</TD>\n            <TD colspan=6><hr>Written feedback from surveys:<br>";
            echo "        <TR><TD>&nbsp;</TD>\n";
	    echo "            <TD colspan=5 class=\"$class\">".htmlspecialchars(fix_slashes($ccommentarray[$k]["comment"]));
            echo "</TD>\n";
            echo "            </TR>\n";
	  }
        echo "        <TR><TD colspan=7 class=\"border0020\">&nbsp;</TD></TR>\n";
        echo "        <TR><TD colspan=7 class=\"border0000\">&nbsp;</TD></TR>\n";
        }
    echo "        </TABLE>\n"; 
    if ($pcommentrows > 0) {
      echo "<hr>\n<P><A NAME=genfeedback></A>Personal Feedback:</A></P>\n";
      echo "<UL>\n";
      for ($i=0; $i<$pcommentrows; $i++) {
	echo "  <LI>".$pcommentarray[$i]["comment"]."\n";
        }
      echo "</UL>\n<br>\n";
      }
    participant_footer();
?>
