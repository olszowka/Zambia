<?php
//  Copyright (c) 2015-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
    require_once('db_functions.php');
    function retrieveKonOpasData() {
        $results = array();
        if (prepare_db_and_more() === false) {
            $results["message_error"] = "Unable to connect to database.<br />No further execution possible.";
            return $results;
            };
        $ConStartDatim = CON_START_DATIM;
        $photoPublicDirectory = mb_substr(PHOTO_PUBLIC_DIRECTORY, 2);
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
        $result = mysqli_query_with_error_handling($query);
        $sessionHasParticipant = array();
        $participantOnSession = array();
        while($row = mysqli_fetch_assoc($result)) {
            $sessionHasParticipant[$row["sessionid"]][] = array("id" => $row["badgeid"], "name" => $row["pubsname"].($row["moderator"] == "1" ? " (moderator)" : ""));
            $participantOnSession[$row["badgeid"]][] = $row["sessionid"];
            }
        $query = <<<EOD
SELECT
        S.sessionid AS id, TR.trackname, TY.typename, R.roomname AS loc, SQ.tags,
        CASE
            WHEN TRIM(IFNULL(S.secondtitle, '')) = '' THEN S.title
            ELSE S.secondtitle
            END AS `title`,
        CASE
            WHEN TRIM(IFNULL(S.pocketprogtext, '')) = '' THEN S.progguidhtml
            ELSE S.pocketprogtext
            END AS `desc`,
        DATE_FORMAT(duration, '%k') * 60 + DATE_FORMAT(duration, '%i') AS mins, 
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%Y-%m-%d') as date,
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%H:%i') as time
    FROM
                  Schedule SCH
             JOIN Sessions S USING (sessionid)
             JOIN Tracks TR USING (trackid)
             JOIN Types TY USING (typeid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN ( SELECT
                            S2.sessionid, GROUP_CONCAT(TA.tagname SEPARATOR ',') as tags
                        FROM
                                      Schedule SCH2
                                 JOIN Sessions S2 USING (sessionid)
                            LEFT JOIN SessionHasTag SHT USING (sessionid)
                            LEFT JOIN Tags TA USING (tagid)
                        WHERE
                            S2.pubstatusid = 2 /* Public */
                        GROUP BY 
                            S2.sessionid
                            
                    ) AS SQ USING (sessionid)
    WHERE
        S.pubstatusid = 2 /* Public */
    ORDER BY
        S.sessionid;
EOD;
        $result = mysqli_query_with_error_handling($query);
        $program = array();
        while($row = mysqli_fetch_assoc($result)) {
            $programRow = array(
                "id" => $row["id"],
                "title" => $row["title"],
                "tags" => array("track:".$row["trackname"],"type:".$row["typename"]),
                "date" => $row["date"],
                "time" => $row["time"],
                "loc" => array($row["loc"]),
                "people" => $sessionHasParticipant[$row["id"]],
                "desc" => $row["desc"],
                "mins" => $row["mins"]
                );
            if ($row["tags"]) {
                $tags = explode(',', $row["tags"]);
                $programRow["tags"] = array_merge($programRow["tags"], array_map(fn($s): string => "tag:$s", $tags));
            }
            $program[] = $programRow;
            }
            // P.badgeid, P.pubsname, COALESCE(P.htmlbio, P.bio, '') AS bio, IFNULL(P.approvedphotofilename, 'default.png') AS photo
            // P.badgeid, P.pubsname, IFNULL(P.bio, '') AS bio, P.approvedphotofilename AS photo,
        $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, COALESCE(P.htmlbio, P.bio, '') AS bio, P.approvedphotofilename AS photo,
        PSA1.value AS website, PSA2.value AS facebook, PSA3.value AS twitter, PSA4.value AS instagram
    FROM
                  Participants P
        LEFT JOIN ParticipantSurveyAnswers PSA1 on P.badgeid = PSA1.participantid AND PSA1.questionid = 11
        LEFT JOIN ParticipantSurveyAnswers PSA2 on P.badgeid = PSA2.participantid AND PSA2.questionid = 12
        LEFT JOIN ParticipantSurveyAnswers PSA3 on P.badgeid = PSA3.participantid AND PSA3.questionid = 13
        LEFT JOIN ParticipantSurveyAnswers PSA4 on P.badgeid = PSA4.participantid AND PSA4.questionid = 14
    WHERE
        P.badgeid IN (
            SELECT POS.badgeid FROM
                     ParticipantOnSession POS
                JOIN Sessions S USING (sessionid)
                JOIN Schedule SCH USING (sessionid)
                WHERE S.pubstatusid = 2 /* Public */
            )
EOD;
        $result = mysqli_query_with_error_handling($query);
        $people = array();
        while($row = mysqli_fetch_assoc($result)) {
            $peopleRow = array(
                "id" => $row["badgeid"],
                "name" => array($row["pubsname"]),
                "prog" => $participantOnSession[$row["badgeid"]],
                "bio" => mb_ereg_replace('/[\x00-\x1F\x7F]/','',$row["bio"])
                );
            $links = array();
//            if ($row["website"]) {
//                $links["website"] = trim($row["website"]); // trim is safe for UTF-8, but not other mb encodings
//            }
//            if ($row["facebook"]) {
//                $links["facebook"] = trim($row["facebook"]);
//            }
//            if ($row["twitter"]) {
//                $links["twitter"] = trim($row["twitter"]);
//            }
//            if ($row["instagram"]) {
//                $links["instagram"] = trim($row["instagram"]);
//            }
            if ($row["photo"]) {
                $links["img"] = ROOT_URL . $photoPublicDirectory . "/" . $row["photo"];
            }
            if (count($links) > 0) {
                $peopleRow["links"] = $links;
            }
            $people[] = $peopleRow;
            }
        //header('Content-type: application/json');
        $results["json"] = "var program = ".json_encode($program).";\n";
        $results["json"] .= "var people = ".json_encode($people).";\n";
        return $results;
    }
?>
