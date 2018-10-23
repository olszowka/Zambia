<?php
// Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $message_error, $messages, $title;
$title = "My Availability";
require('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
require('my_sched_constr_func.php');
$partAvail = get_participant_availability_from_post();
$timesXML = retrieve_timesXML();
$status = validate_participant_availability(); /* return true if OK.  Store error messages in
        global $messages */
for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
    if ($partAvail["availstartday_$i"] == 0) {
        unset($partAvail["availstartday_$i"]);
    }
    if ($partAvail["availstarttime_$i"] == 0) {
        unset($partAvail["availstarttime_$i"]);
    }
    if ($partAvail["availendday_$i"] == 0) {
        unset($partAvail["availendday_$i"]);
    }
    if ($partAvail["availendtime_$i"] == 0) {
        unset($partAvail["availendtime_$i"]);
    }
}
if ($status == false) {
    $message_error = "The data you entered was incorrect.  Database not updated.<br />" . $messages; // error message
    unset($messages);
} else {  /* Update DB */
    $query = "REPLACE ParticipantAvailability SET ";
    $query .= "badgeid='$badgeid', ";
    $query .= "maxprog={$partAvail["maxprog"]}, ";
    $query .= "preventconflict=\"" . mysqli_real_escape_string($linki, $partAvail["preventconflict"]) . "\", ";
    $query .= "otherconstraints=\"" . mysqli_real_escape_string($linki, $partAvail["otherconstraints"]) . "\", ";
    $query .= "numkidsfasttrack={$partAvail["numkidsfasttrack"]};";
    if (!mysqli_query($linki, $query)) {
        $message = $query . "<br />Error updating database.  Database not updated.";
        RenderError($message);
        exit();
    }
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
        if (isset($partAvail["availstarttime_$i"])) {
            if (CON_NUM_DAYS == 1) {
                // for 1 day con didn't collect or validate day info; just set day=1
                $partAvail["availstartday_$i"] = 1;
                $partAvail["availendday_$i"] = 1;
            }
            $time = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availstarttime_$i"] . "']/@timevalue)");
            $nextday = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availstarttime_$i"] . "']/@next_day)");
            $findit = strpos($time, ':');
            $hour = substr($time, 0, $findit);
            $restOfTime = substr($time, $findit);
            $starttime = (($partAvail["availstartday_$i"] - 1 + $nextday) * 24 + $hour) . $restOfTime;

            $time = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availendtime_$i"] . "']/@timevalue)");
            $nextday = $timesXML["XPath"]->evaluate("string(query/row[@timeid='" . $partAvail["availendtime_$i"] . "']/@next_day)");
            $findit = strpos($time, ':');
            $hour = substr($time, 0, $findit);
            $restOfTime = substr($time, $findit);
            $endtime = (($partAvail["availendday_$i"] - 1 + $nextday) * 24 + $hour) . $restOfTime;

            $query = "REPLACE ParticipantAvailabilityTimes SET ";
            $query .= "badgeid=\"$badgeid\",availabilitynum=$i,starttime=\"$starttime\",endtime=\"$endtime\"";
            if (!mysqli_query($linki, $query)) {
                $message = $query . "<br />Error updating database.  Database not updated.";
                RenderError($message);
                exit();
            }
        }
    }
    if (CON_NUM_DAYS >= 1) {
        $query = "REPLACE ParticipantAvailabilityDays (badgeid,day,maxprog) values";
        for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
            $x = $partAvail["maxprogday$i"];
            $query .= "(\"$badgeid\",$i,$x),";
        }
        $query = substr($query, 0, -1); // remove extra trailing comma
        if (!mysqli_query($linki, $query)) {
            $message = $query . "<br />Error updating database.  Database not updated.";
            RenderError($message);
            exit();
        }
    }
    $query = "DELETE FROM ParticipantAvailabilityTimes WHERE badgeid=\"$badgeid\" and ";
    $query .= "availabilitynum in (";
    $deleteany = false;
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
        if (empty($partAvail["availstarttime_$i"])) {
            $query .= $i . ", ";
            $deleteany = true;
        }
    }
    if ($deleteany) {
        $query = substr($query, 0, -2) . ");\n";
        // error_log($query); for debugging only
        if (!mysqli_query_with_error_handling($query, true)) {
            exit();
        }
    }
    if (!$partAvail = retrieve_participantAvailability_from_db($badgeid, true)) {
        exit();
    }
    $i = 1;
    while (isset($partAvail["starttimestamp_$i"])) {
        //error_log("zambia-my_sched got here.i $i");
        //availstartday, availendday: day1 is 1st day of con
        //availstarttime, availendtime: 0 is unset; other is index into Times table
        $x = convert_timestamp_to_timeindex($timesXML["XPath"], $partAvail["starttimestamp_$i"], true);
        $partAvail["availstartday_$i"] = $x["day"];
        $partAvail["availstarttime_$i"] = $x["hour"];
        $x = convert_timestamp_to_timeindex($timesXML["XPath"], $partAvail["endtimestamp_$i"], false);
        $partAvail["availendday_$i"] = $x["day"];
        $partAvail["availendtime_$i"] = $x["hour"];
        $i++;
    }
    $message = "Database updated successfully.";
    unset($message_error);
}
require('renderMySchedConstr.php');
exit();
?>
