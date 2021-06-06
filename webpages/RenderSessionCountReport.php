<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderSessionCountReport($result) {
    global $title;
    $title = "Session Count Report";
    require_once('StaffCommonCode.php');
    staff_header($title, true);
    ?>
<div class="container">

    <div class="alert alert-primary mt-2" role="alert"> Sessions are sorted by the session status. </div>

    <div class="card">
        <div class="card-body">

            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th class="y1">Track</th>
                        <th class="y2">Status</th>
                        <th class="y3 text-right">Number of<br>Sessions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                while (list($track, $status, $count) = mysqli_fetch_array($result, MYSQLI_NUM)) {
                    echo "        <tr>\n";
                    echo "            <td class=\"x1\">" . $track . "</td>\n";
                    echo "            <td class=\"x2\">" . $status . "&nbsp;&nbsp;&nbsp;</td>\n";
                    echo "            <td class=\"x3 text-right\">" . $count . "</td>\n";
                    echo "            </tr>\n";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    <?php staff_footer();
} ?>
