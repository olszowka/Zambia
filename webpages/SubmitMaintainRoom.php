<?php
//	Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function check_room_sched_conflicts($deleteScheduleIds, $addToScheduleArray) {
    //
    // $addToScheduleArray is an array of $sessionid => $startmin
    //     sessions to add to schedule with starttime measured in minutes from start of con
    // $deleteScheduleIds is an array of $scheduleid => 1
    // Perform following checks on the participants in the new schedule entries (taking into account deleted entries):
    //    1. Are any participants double booked?
    //    2. Are any participants scheduled outside available times?
    //    3. Are any participants not currently set as attending?
    //    4. Are any participants scheduled for more sessions than limits (daily and total)? (not implemented yet!!!)
    //
    // Process
    //
    // Get the data of entries added -- Get the list of participants affected
    //      If there are no additions, then there are only deletions and these can't cause conflicts.
    // Get their existing schedule
    // Remove the deleted items from the existing schedule data to be compare
    // Check 1
    // Retrieve availability info
    // Check 2
    // Retrieve session limit info
    // Check 3
    //
    // $addToScheduleArray2 [sessionid]
    //[startmin]
    //[durationmin]
    //[participants] array of []=>badgeid
    global $linki, $message, $title;
    if (count($addToScheduleArray) < 1)
        return (true); // If there are no additions, then
    // there are only deletions and these can't cause conflicts.
    $sessionidlist = implode(",", array_keys($addToScheduleArray));
    $addToScheduleParticipants = array();
    $addToScheduleParticipantsAttending = array();
    $query = <<<EOD
SELECT
		S.sessionid, hour(S.duration)*60+minute(S.duration) as durationmin, S.title
	FROM
		Sessions S
	WHERE
		S.sessionid in ($sessionidlist)
EOD;
    if (!$result = mysqli_query_with_error_handling($query, true, true)) {
        exit(); // should have exited already
    }
    while (list($sessionid, $durationmin, $title) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $addToScheduleArray2[$sessionid]['startmin'] = $addToScheduleArray[$sessionid];
        $addToScheduleArray2[$sessionid]['endmin'] = $addToScheduleArray[$sessionid] + $durationmin;
        $addToScheduleArray2[$sessionid]['title'] = $title;
    }
    mysqli_free_result($result);
    $query = <<<EOD
SELECT
		S.sessionid, POS.badgeid
	FROM
			 Sessions S
		JOIN ParticipantOnSession POS USING (sessionid)
	WHERE
		S.sessionid in ($sessionidlist)
EOD;
    if (!$result = mysqli_query_with_error_handling($query, true, true)) {
        exit(); // should have exited already
    }
    while (list($sessionid, $badgeid) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $addToScheduleArray2[$sessionid]['participants'][] = $badgeid;
        $addToScheduleParticipants[$badgeid] = 1;
    }
    if (count($addToScheduleParticipants) < 1) {
        return (true); // if none of the sessions added to the schedule
        // had any participants, then there can be no participant conflicts.
    }
    $addToScheduleParticipantsForQuery = array_map(function($str){global $linki;return "'" . mysqli_real_escape_string($linki, $str) . "'";}, array_keys($addToScheduleParticipants));
    $badgeidlist = implode(",", $addToScheduleParticipantsForQuery);
    $query = <<<EOD
SELECT
		badgeid, pubsname, if (interested = 1, 1, 0) as attending
	FROM
		Participants
	WHERE
		badgeid in ($badgeidlist)
EOD;
    if (!$result = mysqli_query_with_error_handling($query, true, true)) {
        exit(); // should have exited already
    }
    while ($x = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $addToScheduleParticipants[$x['badgeid']] = $x['pubsname'];
        $addToScheduleParticipantsAttending[$x['badgeid']] = $x['attending'];
    }
    mysqli_free_result($result);
    // starttime and duration in minutes from start of con -- simpler time comparison
    // Get participant availabilities
    $query = <<<EOD
SELECT
		badgeid,
		HOUR(starttime) * 60 + MINUTE(starttime) AS startmin,
		HOUR(endtime) * 60 + MINUTE(endtime) AS endmin
	FROM
		ParticipantAvailabilityTimes
	WHERE
		badgeid IN ($badgeidlist)
	ORDER BY
		badgeid, startmin;
EOD;
    if (!$result = mysqli_query_with_error_handling($query, true, true)) {
        exit(); // should have exited already
    }
    $oldbadgeid = "";
    $participantAvailabilityTimes = array();
    while (list($badgeid, $startmin, $endmin) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        if ($oldbadgeid != $badgeid) {
            $oldbadgeid = $badgeid;
            $i = 1;
            $participantAvailabilityTimes[$badgeid][$i]['startmin'] = $startmin;
            $participantAvailabilityTimes[$badgeid][$i]['endmin'] = $endmin;
        } else {
            if ($startmin < $participantAvailabilityTimes[$badgeid][$i]['endmin']) {
                $participantAvailabilityTimes[$badgeid][$i]['endmin'] = max($endmin, $participantAvailabilityTimes[$badgeid][$i]['endmin']);
            } else {
                $i++;
                $participantAvailabilityTimes[$badgeid][$i]['startmin'] = $startmin;
                $participantAvailabilityTimes[$badgeid][$i]['endmin'] = $endmin;
            }
        }
    }
    mysqli_free_result($result);
    $query = <<<EOD
SELECT
		SCH.scheduleid,
		SCH.sessionid,
		HOUR(SCH.starttime) * 60 + MINUTE(SCH.starttime) AS startmin,
		HOUR(SCH.starttime) * 60 + MINUTE(SCH.starttime) + HOUR(S.duration) * 60 + MINUTE(S.duration) as endmin,
		POS.badgeid,
		S.title,
		R.roomname
	FROM
			 Schedule SCH
		JOIN Sessions S USING (sessionid)
		JOIN Rooms R USING (roomid)
		JOIN ParticipantOnSession POS USING (sessionid)
	WHERE
		POS.badgeid in ($badgeidlist)
EOD;
    if (!$result = mysqli_query_with_error_handling($query, true, true)) {
        exit(); // should have exited already
    }
    $refScheduleArray = array();
    while ($x = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        if (array_search($x['scheduleid'], $deleteScheduleIds) !== false) {
            continue; //skip the scheduleids that will be deleted anyway
        }
        $refScheduleArray[] = $x;
    }
    mysqli_free_result($result);
    if (count($refScheduleArray) < 1) {
        return (true); //If net of deletes there are no sessions for relevant participants then
        // there can be no conflicts
    }
    $message = "";
    foreach ($addToScheduleArray2 as $sessionid => $addSession) {
        $conflictThisAddition = false;
        // check #1 two place at once conflict
        foreach ($refScheduleArray as $refSession) {
            if ($addSession['startmin'] >= $refSession['endmin'] or
                $refSession['startmin'] >= $addSession['endmin'])
                continue;
            $participants = $addSession['participants'];
            if ($participants) {
                foreach ($participants as $badgeid) {
                    if ($badgeid != $refSession['badgeid'])
                        continue;
                    if (!$conflictThisAddition) { // Need header for this session
                        $message .= "<div class=\"alert alert-info\">Session $sessionid: {$addSession['title']}\n";
                        $message .= "<ul>";
                    }
                    $conflictThisAddition = true;
                    $message .= "<li>" . htmlspecialchars($addToScheduleParticipants[$badgeid], ENT_NOQUOTES) . " ($badgeid) ";
                    $message .= "has conflict with " . htmlspecialchars($refSession['title'], ENT_NOQUOTES) . " ({$refSession['sessionid']}) in ";
                    $message .= "{$refSession['roomname']}.</li>\n";
                    // conflict!
                }
            }
        }
        // check #2 not available conflict
        // Don't report conflict if there are no availabilities at all for the participant
        if ($addSession['participants']) {
            $addParts = $addSession['participants'];
            foreach ($addParts as $addBadgeid) {
                $availability_match = false;
                if (array_key_exists($addBadgeid, $participantAvailabilityTimes) !== false) {
                    $partAvailTimeSet = $participantAvailabilityTimes[$addBadgeid];
                    foreach ($partAvailTimeSet as $partAvailTime) {
                        if ($partAvailTime['startmin'] > $addSession['startmin'])
                            continue;
                        if ($partAvailTime['endmin'] < $addSession['endmin'])
                            continue;
                        $availability_match = true;
                        break;
                    }
                } else {
                    $availability_match = true;
                    // Don't report conflict if there are no availabilities at all for the participant
                }
                if (!$availability_match) {
                    if (!$conflictThisAddition) {
                        // Need header for this session
                        $message .= "<div class=\"alert alert-info\">Session $sessionid: {$addSession['title']}\n";
                        $message .= "<ul>";
                    }
                    $conflictThisAddition = true;
                    $message .= "<li>" . htmlspecialchars($addToScheduleParticipants[$addBadgeid], ENT_NOQUOTES) . " ($addBadgeid) ";
                    $message .= "is not available.</li>\n";
                }
            }
        }
        // check #3 not attending conflict
        if ($addSession['participants']) {
            $addParts = $addSession['participants'];
            foreach ($addParts as $addBadgeid) {
                if ($addToScheduleParticipantsAttending[$addBadgeid] != '1') {
                    if (!$conflictThisAddition) {
                        // Need header for this session
                        $message .= "<div class=\"alert alert-info\">Session $sessionid: {$addSession['title']}\n";
                        $message .= "<ul>";
                    }
                    $conflictThisAddition = true;
                    $message .= "<li>" . htmlspecialchars($addToScheduleParticipants[$addBadgeid], ENT_NOQUOTES) . " ($addBadgeid) ";
                    $message .= "is not attending.</li>\n";
                }
            }
        }

        if ($conflictThisAddition) {
            $message .= "</ul></div>";
        }
    }
    return (empty($message) ? true : false); // empty message == no conflicts.
}

function SubmitMaintainRoom($ignore_conflicts)
{
//
//  This is hardcoded to follow the workflow of editme -> vetted -> scheduled -> assigned
//  We need to find a way to make it more configurable and flexible
//
    global $linki, $message;
    $numrows = $_POST["numrows"];
    $selroomid = getInt("selroom");
    get_name_and_email($name, $email); // populates them from session data or db as necessary
    $name = mysqli_real_escape_string($linki, $name);
    $email = mysqli_real_escape_string($linki, $email);
    $badgeid = mysqli_real_escape_string($linki, $_SESSION['badgeid']);
    $deleteScheduleIds = array();
    $deleteSessionIds = array();
    for ($i = 1; $i <= $numrows; $i++) { //***** need to update render as well to start at 1********
        if (!isset($_POST["del$i"]) || $_POST["del$i"] != "1")
            continue;
        $deleteSessionIds[] = getInt("rowsession$i");
        $deleteScheduleIds[] = getInt("row$i");
    }
    $incompleteRows = 0;
    $completeRows = 0;
    $addToScheduleArray = array();
    for ($i = 1; $i <= newroomslots; $i++) {
        if ($_POST["sess$i"] == "unset")
            continue;
        if (CON_NUM_DAYS == 1) {
            $day = 1;
        } else {
            $day = $_POST["day$i"];
        }
        if ($day == 0 or $_POST["hour$i"] == -1 or $_POST["min$i"] == -1) {
            $incompleteRows++;
            continue;
        }
        // starttimes in minutes from start of con
        $addToScheduleArray[$_POST["sess$i"]] = ($day - 1) * 1440 + $_POST["ampm$i"] * 720 + $_POST["hour$i"] * 60 + $_POST["min$i"];
        $completeRows++;
    }
    if (!$ignore_conflicts) {
        if (!check_room_sched_conflicts($deleteScheduleIds, $addToScheduleArray)) {
            echo "<p class=\"alert alert-warning\">Database not updated.  There were conflicts.</p>\n";
            echo $message;
            return false;
        }
    }
    if (count($deleteScheduleIds) > 0) {
        $delSchedIdList = implode(",", $deleteScheduleIds);
//  Set status of deleted entries back to vetted.
        $vs = get_idlist_from_db('SessionStatuses', 'statusid', 'statusname', "'vetted'");
        $query = "UPDATE Sessions AS S, Schedule as SC SET S.statusid=$vs WHERE S.sessionid=SC.sessionid AND ";
        $query .= "SC.scheduleid IN ($delSchedIdList)";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.<br />";
            echo "<p class=\"alert alert-error\">" . $message . "\n</p>";
            staff_footer();
            exit();
        }
        $query = "DELETE FROM Schedule WHERE scheduleid in ($delSchedIdList)";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.<br />";
            echo "<p class=\"alert alert-error\">" . $message . "\n";
            staff_footer();
            exit();
        }
        $rows = mysqli_affected_rows($linki);
        echo "<p class=\"alert\">$rows session" . ($rows > 1 ? "s" : "") . " removed from schedule.\n</p>";
        $query = <<<EOD
INSERT INTO SessionEditHistory
        (sessionid, badgeid, name, email_address, sessioneditcode, statusid, editdescription)
        VALUES
EOD;
        foreach ($deleteSessionIds as $delsessionid) {
            $query .= "($delsessionid,\"$badgeid\",\"$name\",\"$email\",5,$vs,null),";
        }
        $query = substr($query, 0, -1); // remove trailing comma
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.<br />";
            echo "<p class=\"alert alert-error\">" . $message . "\n</p>";
            staff_footer();
            exit();
        }
    }
    if (count($addToScheduleArray) < 1)
        return (true); // nothing to add
    foreach ($addToScheduleArray as $sessionid => $startmin) {
        $hour = floor($startmin / 60); // convert to hours since start of con
        $min = $startmin % 60;
        $time = sprintf("%03d:%02d:00", $hour, $min);
        $query = "INSERT INTO Schedule SET sessionid=$sessionid, roomid=$selroomid, starttime=\"$time\"";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.<br />";
            echo "<p class=\"alert alert-error\">" . $message . "\n";
            staff_footer();
            exit();
        }
// Set status of scheduled entries to Scheduled.
        $vs = get_idlist_from_db('SessionStatuses', 'statusid', 'statusname', "'scheduled'");
        $query = "UPDATE Sessions SET statusid=$vs WHERE sessionid=$sessionid";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.<br />";
            echo "<p class=\"alert alert-error\">" . $message . "\n";
            staff_footer();
            exit();
        }
// Record history of new entries to schedule 
        $query = <<<EOD
INSERT INTO SessionEditHistory
        (sessionid, badgeid, name, email_address, sessioneditcode, statusid, editdescription)
        VALUES
EOD;
        $query .= "($sessionid,\"$badgeid\",\"$name\",\"$email\",4,$vs,\"" . time_description($time) . " in $selroomid\")";
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.<br />";
            echo "<p class=\"alert alert-error\">" . $message . "\n";
            staff_footer();
            exit();
        }
    }
    if ($completeRows) {
        echo "<p class=\"alert alert-success\">$completeRows new schedule entr" . ($completeRows > 1 ? "ies" : "y") . " written to database.\n";
    }
    if ($incompleteRows) {
        echo "<p class=\"alert alert-error\">$incompleteRows row" . ($incompleteRows > 1 ? "s" : "") . " not entered due to incomplete data.\n";
    }
    return (true);
}

?>
