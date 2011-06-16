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
        //availstartday, availendday: day1 is 1st day of con
        //availstarttime, availendtime: measured in whole 1-24 hours only, 0 is unset; 1 is midnight beginning of day
    	$x=parse_mysql_time($partAvail["starttimestamp_$i"]);
    	$partAvail["availstartday_$i"]=$x["day"]+1;
    	$partAvail["availstarttime_$i"]=$x["hour"]+1;
    	$x=parse_mysql_time($partAvail["endtimestamp_$i"]);
    	$partAvail["availendday_$i"]=$x["day"]+1;
    	$partAvail["availendtime_$i"]=$x["hour"]+1;
        $i++;
    	}
    require ('renderMySchedConstr.php');
    exit();
?>
