<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderSessionCountReport($result) {
    global $title;
    $title = "Session Count Report";
    require_once('StaffCommonCode.php');
    staff_header($title);
    ?>
    <table class="table table-condensed table-hover">
        <tr>
            <th class="y1">Track</th>
            <th class="y2">Status</th>
            <th class="y3">Number of<br>Sessions</th>
        </tr>
        <?php
        while (list($track, $status, $count) = mysqli_fetch_array($result, MYSQLI_NUM)) {
            echo "        <tr>\n";
            echo "            <td class=\"x1\">" . $track . "</td>\n";
            echo "            <td class=\"x2\">" . $status . "&nbsp;&nbsp;&nbsp;</td>\n";
            echo "            <td class=\"x3\">" . $count . "&nbsp;&nbsp;&nbsp;</td>\n";
            echo "            </tr>\n";
        }
        ?>
    </table>
    <?php staff_footer();
} ?>
