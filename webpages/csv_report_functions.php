<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function render_query_result_as_csv($result) {
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $betweenValues = false;
        foreach ($row as $value) {
            if ($betweenValues) {
                echo ",";
            }
            if (strpos($value, "\"") !== false) {
                $value = str_replace("\"", "\"\"", $value);
                echo "\"$value\"";
            } elseif (strpos($value, ",") !== false or strpos($value, "\n") !== false) {
                echo "\"$value\"";
            } else {
                echo $value;
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

function render_html_table_as_csv($htmlString) {
    require_once("ext/simple_html_dom.php");
    $rows = array();
    $rowNumber = 0;
    $columnNumber = 0;

    $html = str_get_html($htmlString);
    foreach  ($html->find('table') as $table) {
        foreach  ($table->find('tr') as $tr) {
            $first = true;
            foreach ($tr->childNodes() as $child) {
                $columns = array_key_exists($rowNumber, $rows) ? $rows[$rowNumber] : array();
                $value = $child->plaintext;
                if (!$first) {
                    echo ",";
                }

                // skip columns that are "spans" of other columns
                while (array_key_exists($columnNumber, $columns)) {
                    echo $columns[$columnNumber];
                    echo ",";
                    $columnNumber += 1;
                }

                // if the current cell spans other rows or columns, 
                // make sure we track that
                if (isset($child->colspan) && isset($child->rowspan)) {
                    $rowspan = (int) $child->rowspan;
                    $colspan = (int) $child->colspan;
                    // handle the current row
                    for ($i = $columnNumber + 1; $i < ($columnNumber + $colspan); $i++) {
                        $columns[$i] = "";
                    }
                    $rows[$rowNumber] = $columns;

                    // handle subsequent rows
                    for ($i = $rowNumber + 1; $i < ($rowNumber + $rowspan); $i++) {
                        $nextRow = array();
                        if (array_key_exists($i, $rows)) {
                            $nextRow = $rows[$i];
                        }
                        for ($j = $columnNumber; $j < ($columnNumber + $colspan); $j++) {
                            $nextRow[$j] = "";
                        }
                        $rows[$i] = $nextRow;
                    }
                } else if (isset($child->colspan)) {
                    $span = (int) $child->colspan;
                    for ($i = $columnNumber + 1; $i < ($columnNumber + $span); $i++) {
                        $columns[$i] = "";
                    }
                    $rows[$rowNumber] = $columns;
                } else if (isset($child->rowspan)) {
                    $span = (int) $child->rowspan;
                    for ($i = $rowNumber + 1; $i < ($rowNumber + $span); $i++) {
                        $nextRow = array();
                        if (array_key_exists($i, $rows)) {
                            $nextRow = $rows[$i];
                        }
                        $nextRow[$columnNumber] = "";
                        $rows[$i] = $nextRow;
                    }
                }

                // escape any special characters that would mess up 
                // the CSV format
                if (strpos($value, "\"") !== false) {
                    $value = str_replace("\"", "\"\"", $value);
                }
                $value = preg_replace("(\\s+)", " ", $value);
                if (strpos($value, "&lt;") !== false) {
                    $value = str_replace("&lt;", "<", $value);
                }
                if (strpos($value, "&gt;") !== false) {
                    $value = str_replace("&gt;", ">", $value);
                }

                // write out the cell value
                echo "\"$value\"";
                $first = false;
                $columnNumber += 1;
            }

            echo "\n";
            if (array_key_exists($rowNumber, $rows)) {
                unset($rows[$rowNumber]);
            }
            $rowNumber += 1;
            $columnNumber = 0;
        }
    }
}
