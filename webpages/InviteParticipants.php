<?php
//	Copyright (c) 2005-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Invite Participants";
require_once('StaffCommonCode.php');
staff_header($title);

$selpart = getString("selpart");
if ($selpart !== NULL) {
    $partbadgeid = mysqli_real_escape_string($linki, $selpart);
    $sessionid = getInt("selsess", 0);
    if (($partbadgeid == '') || ($sessionid == 0)) {
        echo "<p class=\"alert alert-error\">Database not updated. Select a participant and a session.</p>";
    } else {
        $query = "INSERT INTO ParticipantSessionInterest SET badgeid='$partbadgeid', ";
        $query .= "sessionid=$sessionid;";
        $result = mysqli_query($linki, $query);
        if ($result) {
            echo "<p class=\"alert alert-success\">Database successfully updated.</p>\n";
        } elseif (mysqli_errno($linki) == 1062) {
            echo "<p class=\"alert\">Database not updated. That participant was already invited to that session.</p>";
        } else {
            echo $query . "<p class=\"alert alert-error\">Database not updated.</p>";
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
?>
<p id="invite-participants-intro">Use this tool to put sessions marked "invited guests only" on a participant's interest list.</p>
<form class="form-inline zambia-form" name="invform" method="POST" action="InviteParticipants.php">
    <div class="row-fluid">
        <label class="control-label" for="participant-select">Select Participant:&nbsp;</label>
        <select id="participant-select" name="selpart">
            <option value="" selected="selected">Select Participant</option>
<?php
while (list($lastname, $firstname, $badgename, $badgeid, $pubsname) = mysqli_fetch_array($Presult, MYSQLI_NUM)) {
    echo "            <option value=\"" . $badgeid . "\">";
    if ($pubsname != "") {
        echo htmlspecialchars($pubsname);
    } else {
        echo htmlspecialchars($lastname) . ", ";
        echo htmlspecialchars($firstname);
    }
    echo " (" . htmlspecialchars($badgename) . ") - ";
    echo htmlspecialchars($badgeid) . "</option>\n";
}
?>
        </select>
        <label class="control-label" for="session-select">Select Session:&nbsp;</label>
        <select id="session-select" name="selsess">
            <option value="0" selected="selected">Select Session</option>
<?php
while (list($trackname, $sessionid, $title) = mysqli_fetch_array($Sresult, MYSQLI_NUM)) {
    echo "            <option value=\"" . $sessionid . "\">" . htmlspecialchars($trackname) . " - ";
    echo htmlspecialchars($sessionid) . " - " . htmlspecialchars($title) . "</option>\n";
}
?>
        </select>
    </div>
    <p>&nbsp;</p>
    <div class="SubmitButton">
        <button class="btn btn-primary" type="submit" name="Invite" >Invite</button>
    </div>
</form>
<?php
staff_footer();
?>
