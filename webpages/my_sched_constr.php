<?php
    global $participant,$message_error,$message2,$congoinfo;
    global $partAvail,$availability;
    $title="My Availability";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (!may_I('my_availability')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if (retrieve_participantAvailability_from_db($badgeid)!=0) {
        RenderError($title,$message_error);
        exit();
        }
    $i=0;
    while ($partAvail["availtimes"][$i]) {
    	$x=parse_mysql_time($partAvail["availtimes"][$i][2]);
    	$availability[$i]["startday"]=$x["day"];
    	$availability[$i]["starttime"]=$x["hour"];
    	$x=parse_mysql_time($partAvail["availtimes"][$i][3]);
    	$availability[$i]["endday"]=$x["day"];
    	$availability[$i]["endtime"]=$x["hour"];
        $i++;
    	}
    require ('renderMySchedConstr.php');
    exit();
?>
