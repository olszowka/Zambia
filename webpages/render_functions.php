<?php
//  Copyright (c) 2007-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
//  RenderPrecis display requires:  a query result containing rows with these fields IN THIS ORDER:
//  $sessionid, $trackname, $typename, $title, $duration, $estatten, $progguiddesc, $persppartinfo, $starttime, $roomname, $statusname
//  it displays the precis view of the data.
function RenderPrecis($result, $showlinks) {
    echo "<h4 class=\"alert alert-success center\">Generated by Zambia: " . date('d-M-Y h:i A') . "</h4>\n";
    echo "<p>If a room name and time are listed, then the session is on the schedule; otherwise, not.</p>";
    echo "<hr>\n";
    if (mysqli_num_rows($result) < 1) {
        echo "<p class=\"alert alert-warning\">No matching results found.</p>";
        return;
    }
    echo "<table class=\"table table-condensed\">\n";
    while (list($sessionid, $trackname, $typename, $title, $duration, $estatten, $progguiddesc, $persppartinfo, $starttime, $roomname, $statusname, $taglist)
        = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<tr>\n";
        echo "  <td rowspan=\"3\" class=\"border0000\" id=\"sessidtcell\" style=\"font-weight:bold\">";
        if ($showlinks) {
            echo "<a href=\"StaffAssignParticipants.php?selsess=" . $sessionid . "\">" . $sessionid . "</a>";
        }
        echo "&nbsp;&nbsp;</td>\n";
        if (TRACK_TAG_USAGE !== "TAG_ONLY") {
            echo "  <td class=\"border0000\" style=\"font-weight:bold\">" . $trackname . "</td>\n";
            echo "  <td class=\"border0000\" style=\"font-weight:bold\">" . $typename . "</td>\n";
        } else {
            echo "  <td class=\"border0000\" colspan=\"2\" style=\"font-weight:bold\">" . $typename . "</td>\n";
        }
        echo "  <td class=\"border0000\" style=\"font-weight:bold\">";
        if ($showlinks) {
            echo "<a href=\"EditSession.php?id=" . $sessionid . "\">" . htmlspecialchars($title, ENT_NOQUOTES) . "</a>";
        } else {
            echo htmlspecialchars($title, ENT_NOQUOTES);
        }
        echo "&nbsp;&nbsp;</td>\n";
        echo "  <td class=\"border0000\" style=\"font-weight:bold\">" . $duration . "</td>\n";
        echo "  <td class=\"border0000\" style=\"font-weight:bold\">";
        if ($roomname) {
            echo $roomname;
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n";
        echo "  <td class=\"border0000\" style=\"font-weight:bold\">";
        if ($starttime) {
            echo $starttime;
        } else {
            echo "&nbsp;";
        }
        echo "</td>\n";
        echo "    <td class=\"border0000\" style=\"font-weight:bold\">$statusname</td>\n";
        if ($showlinks) {
            echo "    <td class=\"border0000\" style=\"font-weight:bold\"><a href=\"SessionHistory.php?selsess=$sessionid\">History</a></td>\n";
        } else {
            echo "<td class=\"border0000\"></td>";
        }
        echo "</tr>\n";
        echo "<tr>";
        echo "    <td colspan=\"2\" class=\"border0010\">" . (is_null($taglist) ? "" : htmlspecialchars($taglist, ENT_NOQUOTES)) . "</td>";
        echo "    <td colspan=\"6\" class=\"border0010\">" . (is_null($progguiddesc) ? "" : htmlspecialchars($progguiddesc, ENT_NOQUOTES)) . "</td>";
        echo "</tr>\n";
        if ($persppartinfo) {
            echo "<tr><td></td>";
            echo "<td colspan=\"2\" class=\"border0000\">Prospective Participant Info: </td>";
            echo "<td colspan=\"6\" class=\"border0000\"><span class=\"alert\" style=\"padding: 0\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</span></td>";
            echo "</tr>\n";
        }
        echo "<tr><td colspan=\"8\" class=\"border0020\">&nbsp;</td></tr>\n";
    }
    echo "</table>\n";
}
?>
