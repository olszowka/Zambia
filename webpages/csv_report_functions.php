<?php
// Copyright (c) 2023 Peter Olszowka. All rights reserved. See copyright document for more details.
function render_query_result_as_csv($result) {
    global $report;
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $betweenValues = false;
        foreach ($row as $index => $value) {
            if ($betweenValues) {
                echo ",";
            }
            if (isset($report['map_functions'][$index]) && is_callable($report['map_functions'][$index])) {
                $value = $report['map_functions'][$index]($value);
            }
            if (!is_null($value)) {
                if (strpos($value, "\"") !== false) {
                    $value = str_replace("\"", "\"\"", $value);
                    echo "\"$value\"";
                } elseif (strpos($value, ",") !== false or strpos($value, "\n") !== false) {
                    echo "\"$value\"";
                } else {
                    echo $value;
                }
            }
            $betweenValues = true;
        }
        echo "\n";
    }
    mysqli_free_result($result);
}
function echo_if_zero_rows_and_exit($result) {
    global $title;
    if (mysqli_num_rows($result) == 0) {
        staff_header($title);
        $message = "Report returned no records.";
        echo "<p>" . $message . "\n";
        staff_footer();
        mysqli_free_result($result);
        exit();
    }
}
