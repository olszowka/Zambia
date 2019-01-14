<?php
//	Copyright (c) 2018 Peter Olszowka and Drew Van Zandt. All rights reserved. See copyright document for more details.
require_once('db_functions.php');
function retrieveGuidebookData() {
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
	WHERE EXISTS (
	    SELECT *
	        FROM
	                 ParticipantOnSession POS
	            JOIN Schedule SCH USING (sessionid)
	            JOIN Sessions S USING (sessionid)
	        WHERE
	                S.pubstatusid = 2 /* Public */
	            AND POS.badgeid = P.badgeid
	  
	    );
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
    // Grab tracks with ids
    $query = <<<EOD
SELECT
        TR.trackid, TR.trackname
    FROM
        Tracks TR
    WHERE EXISTS (
        SELECT *
            FROM
	                 Schedule SCH
	            JOIN Sessions S USING (sessionid)
	        WHERE
	                S.pubstatusid = 2 /* Public */
                AND S.trackid = TR.trackid
    );
EOD;
    $result = mysql_query_with_error_handling($query);
    $tracks = array();
    while($row = mysql_fetch_assoc($result)) {
        $tracksRow = array(
            "id" => $row["trackid"],
            "name" => $row["trackname"]
        );
        $tracks[] = $tracksRow;
    }
    // Grab Locations with ids
    $query = <<<EOD
SELECT
        R.roomid, R.roomname
    FROM
        Rooms R
    WHERE EXISTS (
        SELECT *
            FROM
	                 Schedule SCH
	            JOIN Sessions S USING (sessionid)
	        WHERE
	                S.pubstatusid = 2 /* Public */
                AND SCH.roomid = R.roomid 
    );
EOD;
    $result = mysql_query_with_error_handling($query);
    $locations = array();
    while($row = mysql_fetch_assoc($result)) {
        $locationsRow = array(
            "id" => $row["roomid"],
            "name" => $row["roomname"]
        );
        $locations[] = $locationsRow;
    }
    //header('Content-type: application/json');
    $results["json"] = "var program = ".json_encode($program).";\n";
    $results["json"] .= "var people = ".json_encode($people).";\n";
    $results["json"] .= "var tracks = ".json_encode($tracks).";\n";
    $results["json"] .= "var locations = ".json_encode($locations).";\n";
    return $results;
}

?>
