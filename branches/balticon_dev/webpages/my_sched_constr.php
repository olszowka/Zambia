<?php
	global $participant,$message_error,$message2,$congoinfo;
    global $partAvail;
    $title="My Availability";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
	require ('my_sched_constr_func.php');
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
	retrieve_timesXML();
    $i=1;
    while (isset($partAvail["starttimestamp_$i"])) {
        //error_log("zambia-my_sched got here.i $i");
        //availstartday, availendday: day1 is 1st day of con
        //availstarttime, availendtime: 0 is unset; other is index into Times table
    	$x=convert_timestamp_to_timeindex($partAvail["starttimestamp_$i"],true);
    	$partAvail["availstartday_$i"]=$x["day"];
    	$partAvail["availstarttime_$i"]=$x["hour"];
    	$x=convert_timestamp_to_timeindex($partAvail["endtimestamp_$i"],false);
    	$partAvail["availendday_$i"]=$x["day"];
    	$partAvail["availendtime_$i"]=$x["hour"];
        $i++;
    	}
    require ('renderMySchedConstr.php');
    exit();
?>
