<?php
	global $message_error;
	$title = "Participant Double Booked Report";
    require_once('db_functions.php');
    require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
	$_SESSION['return_to_page']="conflictpartdupreport.php";
	$queryArray["conflict"]=<<<EOD
SELECT
		P.pubsname,
		CNFLC.badgeid,
		CD.firstname,
		CD.lastname,
		CNFLC.titlea,
		TA.trackname as tracknamea,
		RA.roomname as roomnamea,
		CNFLC.roomida,
		CNFLC.sessionida,
		DATE_FORMAT(ADDTIME('$ConStartDatim',CNFLC.starttimea),'%a %l:%i %p') as starttimea,
		DATE_FORMAT(CNFLC.durationa,'%l:%i') as durationa,
		CNFLC.titleb,
		TB.trackname as tracknameb,
		RB.roomname as roomnameb,
		CNFLC.roomidb,
		CNFLC.sessionidb,
		DATE_FORMAT(ADDTIME('$ConStartDatim',CNFLC.starttimeb),'%a %l:%i %p') as starttimeb,
		DATE_FORMAT(CNFLC.durationb,'%l:%i') as durationb
	FROM
		(SELECT
				POSA.badgeid, 
				SCHA.roomid AS roomida, 
				SCHA.sessionid AS sessionida, 
				SCHA.starttime AS starttimea, 
				ADDTIME(SCHA.starttime, SA.duration) AS endtimea, 
				SA.trackid AS trackida, 
				SA.duration AS durationa,
				SA.title AS titlea,
				SCHB.sessionid AS sessionidb, 
				SCHB.roomid AS roomidb, 
				SCHB.starttime AS starttimeb, 
				ADDTIME(SCHB.starttime, SB.duration) AS endtimeb, 
				SB.trackid AS trackidb,
				SB.duration AS durationb,
				SB.title AS titleb
			FROM
					Schedule SCHA
			   JOIN Sessions SA ON SCHA.sessionid = SA.sessionid
			   JOIN ParticipantOnSession POSA ON SA.sessionid = POSA.sessionid
		 	   JOIN ParticipantOnSession POSB ON POSA.badgeid = POSB.badgeid
		 	   JOIN Schedule SCHB ON POSB.sessionid = SCHB.sessionid
			   JOIN Sessions SB ON SCHB.sessionid = SB.sessionid
			WHERE
					SCHA.sessionid < SCHB.sessionid
				AND SCHA.starttime < ADDTIME(SCHB.starttime, SB.duration)
				AND ADDTIME(SCHA.starttime, SA.duration) > SCHB.starttime
			) AS CNFLC
		JOIN Rooms RA ON CNFLC.roomida = RA.roomid 
		JOIN Rooms RB ON CNFLC.roomidb = RB.roomid 
		JOIN Tracks TA ON CNFLC.trackida = TA.trackid
		JOIN Tracks TB ON CNFLC.trackidb = TB.trackid
		JOIN Participants P ON CNFLC.badgeid = P.badgeid
		JOIN CongoDump CD ON CNFLC.badgeid = CD.badgeid
	ORDER BY
		CD.lastname
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	date_default_timezone_set('US/Eastern');
	echo "<p align=center> Generated: ".date("D M j G:i:s T Y")."</p>\n";
	echo "<p>List all instances where a participant is scheduled to be in two or more places at the same time. [On&nbsp;Demand]</p>\n";
	echo "<p class=\"small\">Click on a title to edit the session details.</p>\n";
	echo "<p class=\"small\">Click on a room name to edit the schedule for that room.</p>\n";
	echo "<p class=\"small\">Click on a session id to edit the participant assignments for that session.</p>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->load('reportxsl/conflictpartdup.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	// some browsers does not support empty div, iframe, script and textarea tags
	staff_footer();
?>
