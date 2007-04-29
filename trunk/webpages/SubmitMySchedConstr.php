<?php
    $title="Update My Schedule Constraint Info";
    global $participant,$message_error,$message2,$congoinfo;
    global $partAvail,$availability;
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    get_participant_availability_from_post();
    $status=validate_participant_availability(); /* return true if OK.  Store error messages in
        global $messages */
    if ($status==false) {
            $message_error="The data you entered was incorrect.  Database not updated.<BR>".$messages; // error message
            unset($message);
            for ($i = 1; $i <= 6; $i++) {
                if ($partAvail["availstartday_".$i]==0) {
                        unset($availability[$i-1]["startday"]);
                        }
                    else {
                        $availability[$i-1]["startday"]=$partAvail["availstartday_".$i]-1;
                        }
                if ($partAvail["availstarttime_".$i]==0) {
                        unset($availability[$i-1]["starttime"]);
                        }
                    else {
                        $availability[$i-1]["starttime"]=$partAvail["availstarttime_".$i]-1;
                        }
                if ($partAvail["availendday_".$i]==0) {
                        unset($availability[$i-1]["endday"]);
                        }
                    else {
                        $availability[$i-1]["endday"]=$partAvail["availendday_".$i]-1;
                        }
                if ($partAvail["availendtime_".$i]==0) {
                        unset($availability[$i-1]["endtime"]);
                        }
                    else {
                        $availability[$i-1]["endtime"]=$partAvail["availendtime_".$i]-1;
                        }
                }
            }
        else {  /* Update DB */
            $query = "DELETE FROM ParticipantAvailability WHERE badgeid =\"".$badgeid."\"";
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            $query = "INSERT INTO ParticipantAvailability set ";
            $query .="badgeid=\"".$badgeid."\", fridaymaxprog=";
            $query .=$partAvail["fridaymaxprog"].", saturdaymaxprog=";
            $query .=$partAvail["saturdaymaxprog"].", sundaymaxprog=";
            $query .=$partAvail["sundaymaxprog"].", maxprog=";
            $query .=$partAvail["maxprog"].", preventconflict=";
            $query .="\"".mysql_real_escape_string($partAvail["preventconflict"],$link)."\", otherconstraints=";
            $query .="\"".mysql_real_escape_string($partAvail["otherconstraints"],$link)."\", numkidsfasttrack=";
            $query .=$partAvail["numkidsfasttrack"];
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            $query = "DELETE FROM ParticipantAvailabilityTimes WHERE badgeid=";
            $query .="\"".$badgeid."\"";
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            if ($partAvail["availstartday_1"]>0) {
                $starttime=(($partAvail["availstartday_1"]-1)*24+$partAvail["availstarttime_1"]-1).":00:00";
                $endtime=(($partAvail["availendday_1"]-1)*24+$partAvail["availendtime_1"]-1).":00:00";
                $query = "INSERT INTO ParticipantAvailabilityTimes set ";
                $query .="badgeid=\"".$badgeid."\",availabilitynum=1,starttime=\"".$starttime."\",endtime=\"".$endtime."\"";
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
                }
            if ($partAvail["availstartday_2"]>0) {
                $starttime=(($partAvail["availstartday_2"]-1)*24+$partAvail["availstarttime_2"]-1).":00:00";
                $endtime=(($partAvail["availendday_2"]-1)*24+$partAvail["availendtime_2"]-1).":00:00";
                $query = "INSERT INTO ParticipantAvailabilityTimes set ";
                $query .="badgeid=\"".$badgeid."\",availabilitynum=2,starttime=\"".$starttime."\",endtime=\"".$endtime."\"";
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
                }
            if ($partAvail["availstartday_3"]>0) {
                $starttime=(($partAvail["availstartday_3"]-1)*24+$partAvail["availstarttime_3"]-1).":00:00";
                $endtime=(($partAvail["availendday_3"]-1)*24+$partAvail["availendtime_3"]-1).":00:00";
                $query = "INSERT INTO ParticipantAvailabilityTimes set ";
                $query .="badgeid=\"".$badgeid."\",availabilitynum=3,starttime=\"".$starttime."\",endtime=\"".$endtime."\"";
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
                }
            if ($partAvail["availstartday_4"]>0) {
                $starttime=(($partAvail["availstartday_4"]-1)*24+$partAvail["availstarttime_4"]-1).":00:00";
                $endtime=(($partAvail["availendday_4"]-1)*24+$partAvail["availendtime_4"]-1).":00:00";
                $query = "INSERT INTO ParticipantAvailabilityTimes set ";
                $query .="badgeid=\"".$badgeid."\",availabilitynum=4,starttime=\"".$starttime."\",endtime=\"".$endtime."\"";
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
                }
            if ($partAvail["availstartday_5"]>0) {
                $starttime=(($partAvail["availstartday_5"]-1)*24+$partAvail["availstarttime_5"]-1).":00:00";
                $endtime=(($partAvail["availendday_5"]-1)*24+$partAvail["availendtime_5"]-1).":00:00";
                $query = "INSERT INTO ParticipantAvailabilityTimes set ";
                $query .="badgeid=\"".$badgeid."\",availabilitynum=5,starttime=\"".$starttime."\",endtime=\"".$endtime."\"";
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
                }
            if ($partAvail["availstartday_6"]>0) {
                $starttime=(($partAvail["availstartday_6"]-1)*24+$partAvail["availstarttime_6"]-1).":00:00";
                $endtime=(($partAvail["availendday_6"]-1)*24+$partAvail["availendtime_6"]-1).":00:00";
                $query = "INSERT INTO ParticipantAvailabilityTimes set ";
                $query .="badgeid=\"".$badgeid."\",availabilitynum=6,starttime=\"".$starttime."\",endtime=\"".$endtime."\"";
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
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
            $message="Database updated successfully.";
            unset($message_error);
            }
    require ('renderMySchedConstr.php');
    exit();
?>
