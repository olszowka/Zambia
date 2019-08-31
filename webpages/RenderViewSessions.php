<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderViewSessions($result) {
    global $title;
    $title = "Session Query Results";
    staff_header($title);
    ?>
    <table class="table table-condensed table-hover">
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <col>
        <tr>
            <th class="border1121">Sess.<br>ID</th>
            <th class="border1121">Track</th>
            <th class="border1121">Title</th>
            <th class="border1121">Duration</th>
            <th class="border1121">Est. Atten.</th>
            <th class="border1121">Status</th>
        </tr>
        <?php
        while (list($sessionid, $trackname, $title, $duration, $estatten, $statusname) = mysqli_fetch_array($result, MYSQLI_NUM)) {
            echo "        <tr>\n";
            echo "            <td class=\"border1111\"><a href=\"EditSession.php?id=$sessionid\">$sessionid</a></td>\n";
            echo "            <td class=\"border1111\">$trackname</td>\n";
            echo "            <td class=\"border1111\">" . htmlspecialchars($title, ENT_NOQUOTES) . "</td>\n";
            echo "            <td class=\"border1111\">$duration</td>\n";
            echo "            <td class=\"border1111\">$estatten</td>\n";
            echo "            <td class=\"border1111\">$statusname</td>\n";
            echo "        </tr>\n";
        }
        ?>
    </table>
    <?php
    staff_footer();
}
?>
