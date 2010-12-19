<?php
    $title="Update My Schedule Constraint Info";
    global $participant,$message_error,$messages,$congoinfo;
    global $partAvail,$availability;
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    get_participant_availability_from_post();
    $status=validate_participant_availability(); /* return true if OK.  Store error messages in
        global $messages */
            for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
                if ($partAvail["availstartday_$i"]==0) {
                   unset($partAvail["availstartday_$i"]);
                   }
                if ($partAvail["availstarttime_$i"]==0) {
                   unset($partAvail["availstarttime_$i"]);
                   }
                if ($partAvail["availendday_$i"]==0) {
                   unset($partAvail["availendday_$i"]);
                   }
                if ($partAvail["availendtime_$i"]==0) {
                   unset($partAvail["availendtime_$i"]);
                   }
                }
    if ($status==false) {
            $message_error="The data you entered was incorrect.  Database not updated.<BR>".$messages; // error message
            unset($messages);
            }
        else {  /* Update DB */
            $query = "REPLACE ParticipantAvailability set ";
            $query .="badgeid=\"".$badgeid."\", ";
            $query .="maxprog=".$partAvail["maxprog"].", ";
            $query .="preventconflict=\"".mysql_real_escape_string($partAvail["preventconflict"],$link)."\", ";
            $query .="otherconstraints=\"".mysql_real_escape_string($partAvail["otherconstraints"],$link)."\", ";
            $query .="numkidsfasttrack=".$partAvail["numkidsfasttrack"];
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            for ($i=1; $i<=AVAILABILITY_ROWS; $i++) {
                if ($partAvail["availstarttime_$i"]>0) {
                    if (CON_NUM_DAYS==1) {
                        // for 1 day con didn't collect or validate day info; just set day=1
                        $partAvail["availstartday_$i"]=1;
                        $partAvail["availendday_$i"]=1;
                        }
                    $starttime=(($partAvail["availstartday_$i"]-1)*24+$partAvail["availstarttime_$i"]-1).":00:00";
                    $endtime=(($partAvail["availendday_$i"]-1)*24+$partAvail["availendtime_$i"]-1).":00:00";
                    $query = "REPLACE ParticipantAvailabilityTimes set ";
                    $query .="badgeid=\"$badgeid\",availabilitynum=$i,starttime=\"$starttime\",endtime=\"$endtime\"";
                    if (!mysql_query($query,$link)) {
                        $message=$query."<BR>Error updating database.  Database not updated.";
                        RenderError($title,$message);
                        exit();
                        }
                    }
                }
            if (CON_NUM_DAYS>=1) {
                $query = "REPLACE ParticipantAvailabilityDays (badgeid,day,maxprog) values";
                for ($i=1; $i<=CON_NUM_DAYS; $i++) {
                    $x=$partAvail["maxprogday$i"];
                    $query.="(\"$badgeid\",$i,$x),";
                    }
                $query = substr($query,0,-1); // remove extra trailing comma
                if (!mysql_query($query,$link)) {
                    $message=$query."<BR>Error updating database.  Database not updated.";
                    RenderError($title,$message);
                    exit();
                    }
                }     
            $query = "DELETE FROM ParticipantAvailabilityTimes WHERE badgeid=\"$badgeid\" and ";
            $query .="availabilitynum in (";
            $deleteany=false;
            for ($i=1; $i<=AVAILABILITY_ROWS; $i++) {
                 if ($partAvail["availstarttime_$i"]==0) {
                     $query.=$i.", ";
                     $deleteany=true;
                     }
                 }
            if ($deleteany) {
                $query = substr($query,0,-2).")";
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
