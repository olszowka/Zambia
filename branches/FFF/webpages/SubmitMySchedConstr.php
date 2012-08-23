<?php
    $title="Update My Schedule Constraint Info";
    global $participant,$message_error,$messages,$congoinfo;
    global $partAvail,$availability;
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    get_participant_availability_from_post();
    $status=validate_participant_availability(); /* return true if OK.  Store error messages in
        global $messages */
    // Day null is '0', time null is '';
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
      if ($partAvail["availstartday_$i"]==0) {
	unset($partAvail["availstartday_$i"]);
      }
      if ($partAvail["availstarttime_$i"]=='') {
	unset($partAvail["availstarttime_$i"]);
      }
      if ($partAvail["availendday_$i"]==0) {
	unset($partAvail["availendday_$i"]);
      }
      if ($partAvail["availendtime_$i"]=='') {
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
	      // if savailstarttime_$i exists, as opposed to being not set ('')
	      if ($partAvail["availstarttime_$i"]!='') {
		// for 1 day con didn't collect or validate day info; just set day=1
		// no change needed to the formats for the start and end times
		if (CON_NUM_DAYS==1) {
		  $starttime=$partAvail["availstarttime_$i"];
		  $endtime=$partAvail["availendtime_$i"];
		} else {
		  /* Variables:
		     $dayoffset is the ordinal day number translated to cardnal (decrimented by one)
		     then converted to a days worth of hours (eg Day 2 is translated to 24).
		     $hours is the first two digits of the time, added to the $dayoffset
		     with the possibly missing leading 0 fixed
		     $minsec is the rest of the time string
		     $starttime or $endtime is the new time string */
		  $dayoffset=($partAvail["availstartday_$i"]-1)*24;
		  $hours=0+substr($partAvail["availstarttime_$i"],0,2)+$dayoffset;
		  if ($hours < 10) {$hours="0".$hours;}
		  $minsec=substr($partAvail["availstarttime_$i"],2,6);
		  $starttime=$hours.$minsec;
		  $dayoffset=($partAvail["availendday_$i"]-1)*24;
		  $hours=0+substr($partAvail["availendtime_$i"],0,2)+$dayoffset;
		  if ($hours < 10) {$hours="0".$hours;}
		  $minsec=substr($partAvail["availendtime_$i"],2,6);
		  $endtime=$hours.$minsec;
		}

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
	         // if is set to the null (or erase) field
                 if ($partAvail["availstarttime_$i"]=='') {
                     $query.=$i.", ";
                     $deleteany=true;
                     }
                 }
	    // remove the trailing comma, close the list of availabiltynum, and run the query
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
	    $i=1;
	    while (isset($partAvail["starttimestamp_$i"])) {
	      //error_log("zambia-my_sched got here.i $i");
	      //availstartday, availendday: day 1 is 1st day of con, need to offset because of it.
	      //availstarttime, availendtime: -1 is unset
	      //format is HH:MM:SS so 00:00:00 is midnight beginning of day and 13:30:00 is 1:30pm
	      $x=parse_mysql_time($partAvail["starttimestamp_$i"]);
	      $partAvail["availstartday_$i"]=$x["day"]+1;
	      $partAvail["availstarttime_$i"]=$x["hour"].":".$x["minute"].":00";
	      $x=parse_mysql_time($partAvail["endtimestamp_$i"]);
	      $partAvail["availendday_$i"]=$x["day"]+1;
	      $partAvail["availendtime_$i"]=$x["hour"].":".$x["minute"].":00";
	      $i++;
	    }
            $message="Database updated successfully.";
            unset($message_error);
            }
    require ('renderMySchedConstr.php');
    exit();
?>
