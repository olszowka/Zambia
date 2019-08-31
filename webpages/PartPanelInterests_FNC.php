<?php
//	Copyright (c) 2009-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
// Function get_session_interests_from_db($badgeid)
// Returns count; Will render its own errors
// Populates global $session_interest with
// ['sessionid'] ['rank'] ['willmoderate'] ['comments']
// and populates $session_interest_index
//
function get_session_interests_from_db($badgeid) {
    global $session_interests, $session_interest_index;
    $query = <<<EOD
SELECT sessionid, rank, willmoderate, comments FROM ParticipantSessionInterest
    WHERE badgeid='$badgeid' ORDER BY IFNULL(rank,9999), sessionid
EOD;
    if (!$result = mysqli_query_exit_on_error($query)) {
        exit(); // Should have exited already
    }
    $session_interest_count = mysqli_num_rows($result);
    for ($i = 1; $i <= $session_interest_count; $i++) {
        $session_interests[$i] = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $session_interest_index[$session_interests[$i]['sessionid']] = $i;
    }
    mysqli_free_result($result);
    return ($session_interest_count);
}

// Function get_si_session_info_from_db($session_interest_count)
// Will render its own errors
// Reads global $session_interest to get sessionid's to retrieve
// Reads global $session_interest_index
// Populates global $session_interest with
// ['trackname'] ['title'] ['duration'] ['progguiddesc'] ['persppartinfo']
//
function get_si_session_info_from_db($session_interest_count) {
    global $linnki, $message, $session_interests, $session_interest_index, $title;
    //print_r($session_interest_index);
    if ($session_interest_count == 0) {
        return false;
    }
    $sessionidlist = "";
    for ($i = 1; $i <= $session_interest_count; $i++) {
        $sessionidlist .= $session_interests[$i]['sessionid'] . ", ";
    }
    $sessionidlist = substr($sessionidlist, 0, -2); // drop extra trailing ", "
// If session for which participant is interested no longer has status valid for signup, then don't retrieve
    $query = <<<EOD
SELECT
        S.sessionid,
        T.trackname,
        S.title,
        CASE
            WHEN (minute(S.duration)=0) THEN date_format(S.duration,'%l hr')
            WHEN (hour(S.duration)=0) THEN date_format(S.duration, '%i min')
            ELSE date_format(S.duration,'%l hr, %i min')
            END
            as duration,
        S.progguiddesc,
        S.persppartinfo
    FROM
        Sessions S JOIN
        Tracks T using (trackid) JOIN
        SessionStatuses SS using (statusid)
    WHERE
        S.sessionid in ($sessionidlist) and
        SS.may_be_scheduled=1
EOD;
    if (!$result = mysqli_query_with_error_handling($query)) {
        $message .= $query . "<br>Error querying database.<br>";
        RenderError($message);
        exit();
    }
    $num_rows = mysqli_num_rows($result);
    for ($i = 1; $i <= $num_rows; $i++) {
        $this_row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $j = $session_interest_index[$this_row['sessionid']];
        $session_interests[$j]['trackname'] = $this_row['trackname'];
        $session_interests[$j]['title'] = $this_row['title'];
        $session_interests[$j]['duration'] = $this_row['duration'];
        $session_interests[$j]['progguiddesc'] = $this_row['progguiddesc'];
        $session_interests[$j]['persppartinfo'] = $this_row['persppartinfo'];
    }
    mysqli_free_result($result);
    return (true);
}

// Function get_session_interests_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.  Returns
// the maximum index value.
//
function get_session_interests_from_post() {
    global $session_interests, $session_interest_index;
    $i = 1;
    while (isset($_POST["sessionid$i"])) {
        $session_interests[$i]['sessionid'] = $_POST["sessionid$i"];
        $session_interest_index[$_POST["sessionid$i"]] = $i;
        $session_interests[$i]['rank'] = isset($_POST["rank$i"]) ? $_POST["rank$i"] : "";
        $session_interests[$i]['delete'] = (isset($_POST["delete$i"])) ? true : false;
        $session_interests[$i]['comments'] = isset($_POST["comments$i"]) ? stripslashes($_POST["comments$i"]) : "";
        $session_interests[$i]['willmoderate'] = (isset($_POST["mod$i"])) ? true : false;
        $i++;
    }
    $i--;
    //echo "<P>I: $i</P>";
    //print_r($session_interest_index);
    return ($i);
}

// Function update_session_interests_in_db($session_interest_count)
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.  Returns
// the maximum index value.
//
function update_session_interests_in_db($badgeid, $session_interest_count) {
    global $linki, $session_interests, $title, $message;
    //print_r($session_interests);
    $deleteSessionIds = "";
    $noDeleteCount = 0;
    for ($i = 1; $i <= $session_interest_count; $i++) {
        if ($session_interests[$i]['delete']) {
            $deleteSessionIds .= $session_interests[$i]['sessionid'] . ", ";
        } else {
            $noDeleteCount++;
        }
    }
    if ($deleteSessionIds) {
        $deleteSessionIds = substr($deleteSessionIds, 0, -2); //drop trailing ", "
        $query = "DELETE FROM ParticipantSessionInterest WHERE badgeid=\"$badgeid\" and sessionid in ($deleteSessionIds)";
        if (!mysqli_query_exit_on_error($query)) {
            exit(); // Should have exited already.
        }
        $deleteCount = mysqli_affected_rows($linki);
        $message = "$deleteCount record(s) deleted.<br />\n";
    }
    if ($noDeleteCount) {
        $noDeleteCount = 0;
        $query = "REPLACE INTO ParticipantSessionInterest (badgeid, sessionid, rank, willmoderate, comments) VALUES ";
        for ($i = 1; $i <= $session_interest_count; $i++) {
            if ($session_interests[$i]['delete'])
                continue;
            $noDeleteCount++;
            $query .= "(\"$badgeid\",{$session_interests[$i]['sessionid']},";
            $rank = $session_interests[$i]['rank'];
            $query .= ($rank == "" ? "null" : $rank) . ",";
            $query .= ($session_interests[$i]['willmoderate'] ? 1 : 0) . ",";
            $query .= "\"" . mysqli_real_escape_string($linki, $session_interests[$i]['comments']) . "\"),";
        }
        $query = substr($query, 0, -1); // drop trailing ","
        if (!mysqli_query_exit_on_error($query)) {
            exit(); // Should have exited already.
        }
        $message .= "$noDeleteCount sessions recorded.<br />\n";
    }
    return (true);
}

?>
