<?php
    global $participant,$message_error,$message2,$congoinfo;
    global $partAvail;
    $title="My Availability";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (!may_I('my_availability')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    $x = retrieve_participantAvailability_from_db($badgeid);
    if (($x!=0)&&($x!=-1)) {
        RenderError($title,$message_error);
        exit();
        }
    $i=1;
    while (isset($partAvail["starttimestamp_$i"])) {
        //error_log("zambia-my_sched got here.i $i");
        //availstartday, availendday: day 1 is 1st day of con, not day 0 so the day needs to be incremented
        //availstarttime, availendtime: -1 is unset
        //  HH:MM:SS is the format so 00:00:00 is midnight beginning of day 13:30:00 is 1:30pm
    	$x=parse_mysql_time($partAvail["starttimestamp_$i"]);
    	$partAvail["availstartday_$i"]=$x["day"]+1;
    	$partAvail["availstarttime_$i"]=$x["hour"].":".$x["minute"].":00";
    	$x=parse_mysql_time($partAvail["endtimestamp_$i"]);
    	$partAvail["availendday_$i"]=$x["day"]+1;
    	$partAvail["availendtime_$i"]=$x["hour"].":".$x["minute"].":00";
        $i++;
    	}
    require ('renderMySchedConstr.php');
    exit();
?>
