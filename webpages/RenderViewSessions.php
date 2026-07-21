<?php
// Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderViewSessions($result) {
    global $title;
    $title = "View All Sessions";
    staff_header($title, 'bs5', true);
    ?>
    <table id="reportTable" class="table table-bordered border-dark table-clear">
        <thead>
            <tr>
                <th>Sess.<br>ID</th>
                <th>Track</th>
                <th>Tags</th>
                <th>Title</th>
                <th>Duration</th>
                <th>Est. Atten.</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php
            while (list($sessionid, $trackname, $title, $duration, $estatten, $statusname, $taglist) = mysqli_fetch_array($result, MYSQLI_NUM)) {
                echo "        <tr>\n";
                echo "            <td ><a href=\"EditSession.php?id=$sessionid\">$sessionid</a></td>\n";//class=\"border1111\"
                echo "            <td >$trackname</td>\n";
                echo "            <td >$taglist</td>\n";
                echo "            <td >" . htmlspecialchars($title, ENT_NOQUOTES) . "</td>\n";
                echo "            <td >$duration</td>\n";
                echo "            <td >$estatten</td>\n";
                echo "            <td >$statusname</td>\n";
                echo "        </tr>\n";
            }
            ?>
        </tbody>
    </table>
    <?php
    staff_footer();
}
?>
