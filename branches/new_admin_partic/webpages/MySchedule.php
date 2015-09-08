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
	$queryArr = array();
	$queryArr["sessions"] = <<<EOD
SELECT
            POS.sessionid,
            T.trackname,
            S.title,
            R.roomname,
            S.progguiddesc,
            DATE_FORMAT(ADDTIME('$CON_START_DATIM', SCH.starttime),'%a %l:%i %p') AS starttime,
            left(S.duration, 5) AS duration,
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
            SCH.starttime;
EOD;
	$queryArr["participants"] = <<<EOD
SELECT
        POS.sessionid, CD.badgename, P.pubsname, 
        IF (P.share_email=1, CD.email, NULL) AS email,
        POS.moderator, PSI.comments
    FROM
				ParticipantOnSession POS
		   JOIN CongoDump CD USING(badgeid)
		   JOIN Participants P USING(badgeid)
	  LEFT JOIN ParticipantSessionInterest PSI USING(sessionid, badgeid)
    WHERE
        POS.sessionid IN (
            SELECT sessionid FROM ParticipantOnSession WHERE badgeid='$badgeid'
			)
    ORDER BY sessionid, moderator DESC;
EOD;
	$resultXML=mysql_query_XML($queryArr);
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
        }
	//echo($resultXML->saveXML());
	//exit();
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
	$xsl = new DomDocument;
	$xsl->load('xsl/my_schedule.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	if ($html = $xslt->transformToXML($resultXML)) {
		    echo $html;
			}
		else {
		    trigger_error('XSL transformation failed.', E_USER_ERROR);
			}
    participant_footer();
?>
