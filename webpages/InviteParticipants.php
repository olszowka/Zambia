<?php
//	Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $title;
$title = "Invite Participants";
require_once('StaffCommonCode.php');
staff_header($title);

if (isset($_POST["selpart"])) {
    $partbadgeid = $_POST["selpart"];
    $sessionid = $_POST["selsess"];
    if (($partbadgeid == 0) || ($sessionid == 0)) {
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
echo "<p>Use this tool to put sessions marked \"invited guests only\" on a participant's interest list.\n";
echo "<form class=\"form-inline\" name=\"invform\" method=\"POST\" action=\"InviteParticipants.php\">";
echo "<div class=\"row-fluid\"><label class=\"control-label\" for=\"selpart\">Select Participant:&nbsp;</label>\n";
echo "<select name=\"selpart\">\n";
echo "     <option value=0 selected>Select Participant</option>\n";
while (list($lastname, $firstname, $badgename, $badgeid, $pubsname) = mysqli_fetch_array($Presult, MYSQLI_NUM)) {
    echo "     <option value=\"" . $badgeid . "\">";
    if ($pubsname != "") {
        echo htmlspecialchars($pubsname);
    } else {
        echo htmlspecialchars($lastname) . ", ";
        echo htmlspecialchars($firstname);
    }
    echo " (" . htmlspecialchars($badgename) . ") - ";
    echo htmlspecialchars($badgeid) . "</option>\n";
}
echo "</select>\n";
echo "<label class=\"control-label\" for=\"selsess\">Select Session:&nbsp;</label>\n";
echo "<select name=\"selsess\">\n";
echo "     <option value=\"0\" selected=\"selected\">Select Session</option>\n";
while (list($trackname, $sessionid, $title) = mysqli_fetch_array($Sresult, MYSQLI_NUM)) {
    echo "     <option value=\"" . $sessionid . "\">" . htmlspecialchars($trackname) . " - ";
    echo htmlspecialchars($sessionid) . " - " . htmlspecialchars($title) . "</option>\n";
}
echo "</select></div>\n";
echo "<p>&nbsp;";
echo "<div class=\"SubmitButton\"><button class=\"btn btn-primary\" type=\"submit\" name=\"Invite\" >Invite</button></div>";
echo "</form>";
staff_footer(); ?>
