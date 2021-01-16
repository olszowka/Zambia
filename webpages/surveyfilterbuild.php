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
                        $qcte[$filter->questionid] .=  $filter->type == 'min' ? ">= " : "<= " . $range[0] . "\n";
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
            case 'year':
                if ($qfilter[$filter->questionid] != "") {
                    $range = explode(",", $qfilter[$filter->questionid]);
                    $months = array();
                    $years = array();
                    foreach ($range as $value) {
                        if (is_numeric($value))
                            $years[] = $value;
                        else
                            $months[] = $value;
                    }
                    $values = "(";
                    foreach ($months as $value) {
                        $values .= "value LIKE '$value %' OR ";
                    }
                    $values = mb_substr($values, 0, -3) . ") AND (";
                    foreach ($years as $value) {
                        $values .= "value LIKE '% $value' OR ";
                    }
                    $values = mb_substr($values, 0, -3) . ")";
                    $qcte[$filter->questionid] = "SELECT participantid, count(*) AS answers\nFROM ParticipantSurveyAnswers S" .
                    $filter->questionid . "\nWHERE questionid = " . $filter->questionid . " AND questionid=" . $filter->questionid . " AND " . $values . "\nGROUP BY participantid";
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
        $join .= "JOIN S$qid ON (S$qid.participantid = P.badgeid)\n";
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