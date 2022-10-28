<?php
//	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.

function survey_filter_prepare_filter($filterlist, $andor) {
    $qfilter = array();
    foreach ($filterlist as $filter) {
        if (array_key_exists($filter->questionid, $qfilter))
            $qfilter[$filter->questionid]  .= ',' . $filter->value;
        else
            $qfilter[$filter->questionid] = $filter->value;
    }

    //error_log("qfilter");
    //var_error_log($qfilter);

    $qcte = array();
    // having built question lookup, now deal with the matches
    foreach($filterlist as $filter) {
        switch ($filter->type) {
            case 'text':
                $qcte[$filter->questionid] = "SELECT participantid, count(*) AS answers\nFROM ParticipantSurveyAnswers S" .
                    $filter->questionid . "\nWHERE questionid = " . $filter->questionid . " AND value LIKE '%" .
                    $filter->value . "%'\nGROUP BY participantid";
                break;
            case 'min':
            case 'max':
                if ($qfilter[$filter->questionid] != "") {
                    $qcte[$filter->questionid] = "SELECT participantid, count(*) AS answers\nFROM ParticipantSurveyAnswers S" .
                    $filter->questionid . "\nWHERE questionid = " . $filter->questionid . " AND CAST(value AS UNSIGNED) ";

                    $range = explode(",", $qfilter[$filter->questionid]);
                    if (count($range) == 2) {
                        $min = $range[0] > $range[1] ? $range[1] : $range[0];
                        $max = $range[0] < $range[1] ? $range[1] : $range[0];
                        $qcte[$filter->questionid] .= "BETWEEN $min AND $max\n";
                    } else {
                        $qcte[$filter->questionid] .=  ($filter->type == 'min' ? ">= " : "<= ") . $range[0] . "\n";
                    }
                    $qfilter[$filter->questionid] = "";
                    $qcte[$filter->questionid] .= "\nGROUP BY participantid";
                }
                break;
            case 'check':
                if ($qfilter[$filter->questionid] != "") {
                    $range = explode(",", $qfilter[$filter->questionid]);
                    $values = "";
                    foreach ($range as $value) {
                        $values .= "CONCAT(',', value, ',') LIKE '%,$value,%' OR ";
                    }
                    $qcte[$filter->questionid] = "SELECT participantid, count(*) AS answers\nFROM ParticipantSurveyAnswers S" .
                    $filter->questionid . "\nWHERE questionid = " . $filter->questionid . " AND (" . mb_substr($values, 0, -3) . ")\nGROUP BY participantid";
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'month':
                if ($qfilter[$filter->questionid] != "") {
                    $range = explode(",", $qfilter[$filter->questionid]);
                    $months = array();
                    foreach ($range as $value) {
                         $months[] = $value;
                    }
                    $values = "(";
                    foreach ($months as $value) {
                        $values .= "'$value',";
                    }
                    $values = mb_substr($values, 0, -1) . ")";
                    $qcte[$filter->questionid] = "SELECT participantid, count(*) AS answers\nFROM ParticipantSurveyAnswers S" .
                    $filter->questionid . "\nWHERE questionid = " . $filter->questionid . " AND questionid=" . $filter->questionid . " AND value IN " . $values . "\nGROUP BY participantid";
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'from-monthyear':
            case 'to-monthyear':
                if ($qfilter[$filter->questionid] != "") {
                    $qcte[$filter->questionid] = "SELECT participantid, count(*) AS answers\nFROM ParticipantSurveyAnswers S" .
                    $filter->questionid . "\nWHERE questionid = " . $filter->questionid;

                    $range = explode(",", $qfilter[$filter->questionid]);
                    switch (count($range)) {
                        case 4:
                            $date1 = $range[1] . '/' . $range[0] . '/01';
                            $date2 = $range[3] . '/' . $range[2] . '/01';

                            if (mb_strlen($range[1]) == 4) {
                                $min = "STR_TO_DATE('$date1', '%Y/%b/%d')";
                                $max = "STR_TO_DATE('$date2', '%Y/%b/%d')";
                                $qcte[$filter->questionid] .= " AND STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-', substr(value, 5)), '%b-%d-%Y') BETWEEN $min AND $max\n";
                            }
                            else {
                                $min = "STR_TO_DATE('$date1', '%y/%b/%d')";
                                $max = "STR_TO_DATE('$date2', '%y/%b/%d')";
                                $qcte[$filter->questionid] .= " AND STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-', substr(value, 5)), '%b-%d-%y') BETWEEN $min AND $max\n";
                            }
                            break;
                        case 3:
                            $i = 0;
                            if (is_numeric($range[$i])) {  // start with year, followed by month year
                                $date1 = $range[$i] . '/01/01';
                                $ytype = mb_strlen($range[$i]) == 4 ? '%Y' : '%y';
                                $i = $i + 1;
                            } else if (is_numeric($range[$i + 1])) { // start with month year, followed by month or year
                                $date1 = $range[$i + 1] . '/' . $range[$i] . '/01';
                                $ytype = mb_strlen($range[$i + 1]) == 4 ? '%Y' : '%y';
                                $i = $i + 2;
                            } else { // start with month, then month year
                                $date1 = $range[$i + 2] . '/' . $range[$i] . '/01';
                                $ytype = mb_strlen($range[$i + 2]) == 4 ? '%Y' : '%y';
                                $i = $i + 1;
                            }
                            if (is_numeric($range[$i])) // next cell is year, so it was month year year
                                $date2 = $range[$i] . '/' . $range[0] . '/01';
                            else if ($i == 1) // next cells are month year
                                $date2 = $range[$i + 1] . '/' . $range[$i] . '/01';
                            else  // it was ending in month, use first year
                                $date2 = $range[$i - 1] . '/' . $range[$i] . '/01';

                            $min = "STR_TO_DATE('$date1', '$ytype/%b/%d')";
                            $max = "STR_TO_DATE('$date2', '$ytype/%b/%d')";
                            $qcte[$filter->questionid] .= " AND STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-', substr(value, 5)), '%b-%d-$ytype') BETWEEN $min AND $max\n";
                            break;
                        case 2:
                            if (is_numeric($range[0])) {  // start with year, followed by year or month
                                $date1 = $range[0];
                                $ytype = mb_strlen($date1) == 4 ? '%Y' : '%y';
                                if (is_numeric($range[1])) {
                                    $date2 = $range[1];

                                    if ($date1 > $date2) {   // flip out of order dates so the between works
                                        $d = $date1;
                                        $date1 = $date2;
                                        $date2 = $d;
                                    }
                                    $qcte[$filter->questionid] .= " AND DATE_FORMAT(STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-', substr(value, 5)), '%b-%d-$ytype'), '$ytype') BETWEEN $date1 AND $date2\n";
                                } //else {  ear month (sort of invalid range ignore and take all answers for this question
                            } else if (is_numeric($range[1]))  { // month year (need to check for from or to
                                $ytype = mb_strlen($range[1]) == 4 ? '%Y' : '%y';
                                $date1 = $range[1] . '/' . $range[0] . '/01';
                                $qcte[$filter->questionid] .= " AND STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-', substr(value, 5)), '%b-%d-$ytype') " . ($filter->type == 'from-monthyear' ? ">= " : "<= ") .
                                    "STR_TO_DATE('$date1', '$ytype/%b/%d')\n";
                            } else { // month month use monthnum and check for range
                                $date1 = "CAST(DATE_FORMAT(STR_TO_DATE('2020/" . $range[0] . "/01', '%Y/%b/%d'), '%c') AS UNSIGNED)";
                                $date2 = "CAST(DATE_FORMAT(STR_TO_DATE('2020/" . $range[1] . "/01', '%Y/%b/%d'), '%c') AS UNSIGNED)";
                                $qcte[$filter->questionid] .= " AND CAST(DATE_FORMAT(STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-2020'), '%b-%d-%Y'), '%c') AS UNSIGNED) BETWEEN $date1 AND $date2\n";
                            }
                            break;
                        case 1:
                            if (is_numeric($range[0])) {
                                $date1 = $range[0];
                                $ytype = mb_strlen($date1) == 4 ? '%Y' : '%y';
                                $qcte[$filter->questionid] .= " AND CAST(DATE_FORMAT(STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-', substr(value, 5)), '%b-%d-$ytype'), '$ytype') AS UNSIGNED) " . ($filter->type == 'from-monthyear' ? ">= " : "<= ") . "CAST($date1 AS UNSIGNED)\n";
                            } else {
                                $date1 = "CAST(DATE_FORMAT(STR_TO_DATE('2020/" . $range[0] . "/01', '%Y/%b/%d'), '%c') AS UNSIGNED)";
                                $qcte[$filter->questionid] .= " AND CAST(DATE_FORMAT(STR_TO_DATE(CONCAT(substr(value, 1, 3), '-01-2020'), '%b-%d-%Y'), '%c') AS UNSIGNED) " . ($filter->type == 'from-monthyear' ? ">= " : "<= ") . " $date1\n";
                            }
                    }
                    $qfilter[$filter->questionid] = "";
                }
                break;
        }
    }
    //var_error_log($qcte);
    return $qcte;
}

function survey_filter_build_cte($qcte) {
    if (count($qcte) > 0) {
        $query = "WITH ";
        foreach ($qcte as $qid => $cte) {
           $query .= "S$qid(participantid, answers) AS (\n$cte\n),\n";
        }
        $query = mb_substr($query, 0, -2) . "\n";
        return $query;
    }
    return "";
}

function survey_filter_build_join($qcte) {
    $join = "";
    foreach ($qcte as $qid => $cte) {
        $join .= "JOIN (\n$cte\n) S$qid ON (S$qid.participantid = P.badgeid)\n";

    }
    return $join;
}

function survey_filter_build_join_subquery($qcte) {
    $join = "";
    foreach ($qcte as $qid => $cte) {
        $join .= "JOIN (\n$cte\n) S$qid ON (S$qid.participantid = P.badgeid)\n";
    }
    return $join;
}

function survey_filter_build_where($qcte, $andor) {
    $where = "";
    if (count($qcte) > 0) {
        $where .= " AND (";
        foreach ($qcte as $qid => $cte) {
            $where .= "S$qid.answers = 1 $andor";
        }
        $where = mb_substr($where, 0, -mb_strlen($andor)) . ")\n";
    }
    return $where;
}


?>
