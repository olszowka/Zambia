<?php

require_once("db_functions.php");

class ScheduledSession {
    public $title;
    public $sessionid;
    public $progguiddesc;
    public $starttime;
    public $duration;
    public $roomname;
    public $roomid;
    public $trackname;
    public $participants;
}

class ScheduledParticipant {
    public $pubsname;
    public $badgeid;
    public $moderator;
}

function render_sessions_as_xml($sessions) {
    $xml = new DomDocument("1.0", "UTF-8");
    $doc = $xml -> createElement("doc");
    $doc = $xml -> appendChild($doc);

    foreach ($sessions as $s) {
        $panel = $xml -> createElement("session");
        $panel->setAttribute("title", $s->title);
        $panel->setAttribute("progguiddesc", $s->progguiddesc);
        $panel->setAttribute("roomname", $s->roomname);
        $panel->setAttribute("trackname", $s->trackname);
        $panel->setAttribute("starttime", $s->starttime);
        
        foreach ($s->participants as $p) {
            $particpant = $xml -> createElement("participant");
            $particpant->setAttribute("pubsname", $p->pubsname);
            $particpant->setAttribute("badgeid", $p->badgeid);
            $particpant->setAttribute("moderator", $p->moderator);

            $panel -> appendChild($particpant);
        }

        $doc -> appendChild($panel);
    }
    return $xml;
}

function get_scheduled_events_with_participants_as_xml() {
    return render_sessions_as_xml(get_scheduled_events_with_participants());
}

function get_scheduled_events_with_participants() {
    $query1 =<<<'EOD'
SELECT
        S.sessionid, S.title, S.progguiddesc, R.roomname, SCH.roomid, PS.pubstatusname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
		T.trackname, KC.kidscatname
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN PubStatuses PS USING (pubstatusid)
        JOIN Tracks T USING (trackid)
		JOIN KidsCategories KC USING (kidscatid)
    ORDER BY
        SCH.starttime, R.roomname;
EOD;

    $sessions = array();
    $sessionsById = array();
    $result = mysqli_query_with_error_handling($query1);

    while ($row = mysqli_fetch_assoc($result)) {
        $session = new ScheduledSession();
        $session->title = $row["title"];
        $session->sessionid = $row["sessionid"];
        $session->progguiddesc = $row["progguiddesc"];
        $session->roomname = $row["roomname"];
        $session->trackname = $row["trackname"];
        $session->starttime = $row["starttime"];
        $session->participants = array();


        $sessions[] = $session;
        $sessionsById[$session->sessionid] = $session;
    }

    $query2 =<<<'EOD'
SELECT
        SCH.sessionid, P.pubsname, P.badgeid, POS.moderator
    FROM
			 Schedule SCH
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump C USING (badgeid)
    ORDER BY
		SCH.sessionid, POS.moderator DESC, 
        IF(instr(P.pubsname,C.lastname)>0,C.lastname,substring_index(P.pubsname,' ',-1)),
        C.firstname;
EOD;

    $result = mysqli_query_with_error_handling($query2);
    while ($row = mysqli_fetch_assoc($result)) {
        $session = $sessionsById[$row["sessionid"]];
        $participant = new ScheduledParticipant();
        $participant->pubsname = $row["pubsname"];
        $participant->moderator = $row["moderator"];
        $participant->badgeid = $row["badgeid"];

        $session->participants[] = $participant;
    }
    return $sessions;
}

?>