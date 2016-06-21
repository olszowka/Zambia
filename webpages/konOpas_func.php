<?php
//	$Header$
//	Copyright (c) 2011-2016 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('db_functions.php');
	function retrieveKonOpasData() {
		$results = array();
		if (prepare_db()===false) {
			$results["message_error"]="Unable to connect to database.<BR>No further execution possible.";
			return $results;
			};
		$ConStartDatim = CON_START_DATIM;
		// first query: which people are on which sessions
		$query = <<<EOD
SELECT
		SCH.sessionid, P.badgeid, P.pubsname, POS.moderator
	FROM
			 Schedule SCH
		JOIN Sessions S USING (sessionid)
		JOIN ParticipantOnSession POS USING (sessionid)
		JOIN Participants P USING (badgeid)
	WHERE
		S.pubstatusid = 2 /* Public */
	ORDER BY
		SCH.sessionid,
		POS.moderator DESC,
		P.badgeid;
EOD;
		$result = mysql_query_with_error_handling($query);
		$sessionHasParticipant = array();
		$participantOnSession = array();
		while($row = mysql_fetch_assoc($result)) {
			$sessionHasParticipant[$row["sessionid"]][] = array("id" => $row["badgeid"], "name" => $row["pubsname"].($row["moderator"] == "1" ? " (moderator)" : ""));
			$participantOnSession[$row["badgeid"]][] = $row["sessionid"];
			}
		$query = <<<EOD
SELECT
		S.sessionid AS id, S.title, TR.trackname, TY.typename, R.roomname AS loc, R.floor, SHPC.sessionid AS featured,
		DATE_FORMAT(duration, '%k') * 60 + DATE_FORMAT(duration, '%i') AS mins, S.progguiddesc AS `desc`, 
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%Y-%m-%d') as date,
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%H:%i') as time
	FROM
				  Schedule SCH
			 JOIN Sessions S USING (sessionid)
			 JOIN Tracks TR USING (trackid)
			 JOIN Types TY USING (typeid)
			 JOIN Rooms R USING (roomid)
		LEFT JOIN SessionHasPubChar SHPC ON S.sessionid = SHPC.sessionid AND SHPC.pubcharid = 5 /* Featured */
	WHERE
		S.pubstatusid = 2 /* Public */
	ORDER BY
		S.sessionid;
EOD;
		$result = mysql_query_with_error_handling($query);
		$program = array();
		while($row = mysql_fetch_assoc($result)) {
			$tagsArray = array("track:".$row["trackname"],"type:".$row["typename"]);
			if ($row["featured"])
				$tagsArray[] = 'Featured';
			$locArray = array($row["loc"]);
			if ($row["floor"] && $row["floor"] != "")
				$locArray[] = $row["floor"];
			$programRow = array(
				"id" => $row["id"],
				"title" => $row["title"],
				"tags" => $tagsArray,
				"date" => $row["date"],
				"time" => $row["time"],
				"loc" => $locArray,
				"people" => $sessionHasParticipant[$row["id"]],
				"desc" => $row["desc"],
                                "mins" => $row["mins"]
				);
			$program[] = $programRow;
			}
		$query = <<<EOD
SELECT
		P.badgeid, P.pubsname, P.bio
	FROM
		Participants P
	WHERE
		P.badgeid IN (
			SELECT POS.badgeid FROM
					 ParticipantOnSession POS
				JOIN Sessions S USING (sessionid)
				JOIN Schedule SCH USING (sessionid)
				WHERE S.pubstatusid = 2 /* Public */
			)
EOD;
		$result = mysql_query_with_error_handling($query);
		$people = array();
		while($row = mysql_fetch_assoc($result)) {
			$peopleRow = array(
				"id" => $row["badgeid"],
				"name" => array($row["pubsname"]),
				"prog" => $participantOnSession[$row["badgeid"]],
				"bio" => $row["bio"]
				);
			$people[] = $peopleRow;
			}
		//header('Content-type: application/json');
		$results["json"] = "var program = ".json_encode($program).";\n";
		$results["json"] .= "var people = ".json_encode($people).";\n";
		return $results;
	}
?>
