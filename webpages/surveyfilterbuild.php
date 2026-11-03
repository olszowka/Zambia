<?php
//	Copyright (c) 2021-2026 Peter Olszowka. All rights reserved. See copyright document for more details.

// Returns a "FROM" source, aliased S<questionid>(badgeid, value), extracting one question's answer
// value out of ParticipantSurveyResponses.answers by shortname. Used only by the from/to-monthyear date
// range filter below: that logic is intricate enough (STR_TO_DATE/DATE_FORMAT edge cases across 1-4 part
// month/year ranges) that it stays as SQL rather than being ported to PHP; only the source of the
// per-question value changes. The other filter types are matched in PHP -- see
// survey_filter_match_participants() below.
function survey_filter_json_source($questionid) {
    return "(SELECT PSR.badgeid, JSON_UNQUOTE(JSON_EXTRACT(PSR.answers, CONCAT('\$.', JSON_QUOTE(SQC.shortname), '.value'))) AS value" .
        " FROM ParticipantSurveyResponses PSR JOIN SurveyQuestionConfig SQC ON SQC.questionid = $questionid) S$questionid";
}

// Fetches every participant's decoded answer for $questionid from ParticipantSurveyResponses and applies
// $matchFn(value) to each in PHP, rather than embedding the filter's match logic (and user-supplied filter
// text) directly into SQL. Returns a CTE body in the same S<questionid>(badgeid, answers) shape the
// SQL-built filters below produce, so survey_filter_build_cte()/_join()/_join_subquery()/_where() -- the
// shared AND/OR combining logic -- don't need to know or care whether a given filter matched in SQL or PHP.
function survey_filter_match_participants($questionid, $matchFn) {
    global $mysqli;
    $result = mysqli_query_with_prepare_and_exit_on_error(
        "SELECT PSR.badgeid, JSON_UNQUOTE(JSON_EXTRACT(PSR.answers, CONCAT('\$.', JSON_QUOTE(SQC.shortname), '.value'))) AS value" .
        " FROM ParticipantSurveyResponses PSR JOIN SurveyQuestionConfig SQC ON SQC.questionid = ?;",
        'i', array($questionid)
    );
    $matchedIds = array();
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['value'] !== null && $matchFn($row['value'])) {
            $matchedIds[] = $row['badgeid'];
        }
    }
    if (count($matchedIds) === 0) {
        return "SELECT badgeid, 1 AS answers FROM Participants WHERE 1 = 0";
    }
    $quoted = array_map(fn($id) => "'" . $mysqli->real_escape_string($id) . "'", $matchedIds);
    return "SELECT badgeid, 1 AS answers FROM Participants WHERE badgeid IN (" . implode(',', $quoted) . ")";
}

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
                $needle = $filter->value;
                $qcte[$filter->questionid] = survey_filter_match_participants($filter->questionid, function($value) use ($needle) {
                    return mb_stripos($value, $needle) !== false;
                });
                break;
            case 'min':
            case 'max':
                if ($qfilter[$filter->questionid] != "") {
                    $range = explode(",", $qfilter[$filter->questionid]);
                    if (count($range) == 2) {
                        $min = (int)($range[0] > $range[1] ? $range[1] : $range[0]);
                        $max = (int)($range[0] < $range[1] ? $range[1] : $range[0]);
                        $qcte[$filter->questionid] = survey_filter_match_participants($filter->questionid, function($value) use ($min, $max) {
                            $n = (int)$value;
                            return $n >= $min && $n <= $max;
                        });
                    } else {
                        $bound = (int)$range[0];
                        $isMin = $filter->type == 'min';
                        $qcte[$filter->questionid] = survey_filter_match_participants($filter->questionid, function($value) use ($bound, $isMin) {
                            $n = (int)$value;
                            return $isMin ? $n >= $bound : $n <= $bound;
                        });
                    }
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'check':
                if ($qfilter[$filter->questionid] != "") {
                    $range = explode(",", $qfilter[$filter->questionid]);
                    $qcte[$filter->questionid] = survey_filter_match_participants($filter->questionid, function($value) use ($range) {
                        $selected = explode(',', $value);
                        return count(array_intersect($range, $selected)) > 0;
                    });
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'month':
                if ($qfilter[$filter->questionid] != "") {
                    $range = explode(",", $qfilter[$filter->questionid]);
                    $qcte[$filter->questionid] = survey_filter_match_participants($filter->questionid, function($value) use ($range) {
                        return in_array($value, $range, true);
                    });
                    $qfilter[$filter->questionid] = "";
                }
                break;
            case 'from-monthyear':
            case 'to-monthyear':
                if ($qfilter[$filter->questionid] != "") {
                    $qcte[$filter->questionid] = "SELECT badgeid, count(*) AS answers\nFROM " . survey_filter_json_source($filter->questionid) .
                    "\nWHERE 1 = 1";

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
                    // Pre-existing bug fix: this branch never appended a GROUP BY, unlike every other
                    // filter type here, so it errored under ONLY_FULL_GROUP_BY (MySQL's default) whenever
                    // a from/to-monthyear filter was actually used.
                    $qcte[$filter->questionid] .= "\nGROUP BY badgeid";
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
           $query .= "S$qid(badgeid, answers) AS (\n$cte\n),\n";
        }
        $query = mb_substr($query, 0, -2) . "\n";
        return $query;
    }
    return "";
}

// $andor selects the join type: in "match all" (AND) mode, a participant must appear in every S<qid> CTE
// to survive at all, so a plain (inner) JOIN is correct and sufficient. In "match any" (OR) mode, a plain
// JOIN would incorrectly require matching *every* filtered question just to appear in the result at all --
// it must be a LEFT OUTER JOIN instead, so a participant who only matches some (but not all) of the
// filtered questions still survives the join, letting survey_filter_build_where()'s "S1.answers = 1 OR
// S2.answers = 1 OR ..." actually mean anything.
function survey_filter_build_join($qcte, $andor) {
    $joinType = ($andor === ' OR ') ? 'LEFT OUTER JOIN' : 'JOIN';
    $join = "";
    foreach ($qcte as $qid => $cte) {
        $join .= "$joinType S$qid ON (S$qid.badgeid = P.badgeid)\n";

    }
    return $join;
}

function survey_filter_build_join_subquery($qcte, $andor) {
    $joinType = ($andor === ' OR ') ? 'LEFT OUTER JOIN' : 'JOIN';
    $join = "";
    foreach ($qcte as $qid => $cte) {
        $join .= "$joinType (\n$cte\n) S$qid ON (S$qid.badgeid = P.badgeid)\n";
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
