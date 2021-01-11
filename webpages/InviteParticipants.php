<?php
//	Copyright (c) 2005-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$bootstrap4 = true;
$title = "Invite Participants";
require_once('StaffCommonCode.php');
staff_header($title, $bootstrap4);
$message = "";
$alerttype = "success";
if(may_I("Staff")) {

    if (isset($_POST["selpart"])) {
        $partbadgeid = mysqli_real_escape_string($linki, getString("selpart"));
        $sessionid = getInt("selsess", 0);
        if (($partbadgeid == '') || ($sessionid == 0)) {
            echo "<p class=\"alert alert-error\">Database not updated. Select a participant and a session.</p>";
        } else {
            $query = "INSERT INTO ParticipantSessionInterest SET badgeid='$partbadgeid', ";
            $query .= "sessionid=$sessionid;";
            $result = mysqli_query($linki, $query);
            if ($result) {
                $message =  "<p>Database successfully updated.</p>";
            } elseif (mysqli_errno($linki) == 1062) {
                $message =  "<p>Database not updated. That participant was already invited to that session.</p>";
                $alerttype = "warning";
            } else {
                $message = $query . "<p>Database not updated.</p>";
                $alerttype = "danger";
            }

        }
    }
    $query = <<<EOD
SELECT
        CD.lastname,
        CD.firstname,
        CD.badgename,
        P.badgeid,
        P.pubsname
    FROM
             Participants P
        JOIN CongoDump CD USING(badgeid)
    WHERE
        P.interested=1
    ORDER BY
        IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
    if (!$Presult = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $query = <<<EOD
SELECT
        T.trackname, S.sessionid, S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
    WHERE
        SS.may_be_scheduled=1
    ORDER BY
        T.trackname, S.sessionid, S.title;
EOD;
    if (!$Sresult = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }

    $participants = array();
    while (list($lastname, $firstname, $badgename, $badgeid, $pubsname) = mysqli_fetch_array($Presult, MYSQLI_NUM)) {
        $name = "";
        if ($pubsname != "") {
            $name = htmlspecialchars($pubsname);
        } else {
            if (mb_strlen($lastname) > 0)
                $name =  htmlspecialchars($lastname) . ", ";
            $name .= htmlspecialchars($firstname);
        }
        $name .= " (" . htmlspecialchars($badgename) . ") - ";
        $name .= htmlspecialchars($badgeid);
        $participants[] = (object) array('badgeid' => $badgeid, 'name' => $name );
    }
    $resultXML = ObjecttoXML('participants', $participants);

    $sessions = array();
    while (list($trackname, $sessionid, $title) = mysqli_fetch_array($Sresult, MYSQLI_NUM)) {
        $name =  htmlspecialchars($trackname) . " - " .  htmlspecialchars($sessionid) . " - " . htmlspecialchars($title);
        $sessions[] = (object) array('sessionid' => $sessionid, 'title' => $name );
    }
    $resultXML = ObjecttoXML('sessions', $sessions, $resultXML);

    $PriorArray["getSessionID"] = session_id();

    $ControlStrArray = generateControlString($PriorArray);
    $paramArray["control"] = $ControlStrArray["control"];
    $paramArray["controliv"] = $ControlStrArray["controliv"];

    if ($message != "") {
        $paramArray["UpdateMessage"] = $message;
        $paramArray["MessageAlertType"] = $alerttype;
    }
    // following line for debugging only
    //echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
    RenderXSLT('InviteParticipants.xsl', $paramArray, $resultXML);
}
staff_footer();
?>