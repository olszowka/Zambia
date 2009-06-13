<?php
    require_once ('StaffCommonCode.php');
    require ('StaffEditCreateParticipant_FNC.php');
    global $badgeid, $partAvail,$partAvailRows,$availability,$messages;
    if (!isset($_POST['action'])) {
        $title="Edit or Add Participant";
        $message_error="Required parameter 'action' not found.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    $action=$_POST['action']; // "create" or "edit"
    if (!($action=="edit"||$action=="create")) {
        $title="Edit or Add Participant";
        $message_error="Parameter 'action' contains invalid value.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if ($action=="create") {
            $title="Add Participant";
            if (!may_I('create_participant')) {
                $message_error="You do not have permission to access this page.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            }
        else {
            $title="Edit Participant";
            if (!may_I('edit_participant')) {
                $message_error="You do not have permission to access this page.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            }
    get_participant_availability_from_post();
    $status=validate_participant_availability(); /* return true if OK.  Store error messages in
        global $messages */
    //error_log("Zambia: SECP: gpafp:".(($status)?"true":"false"."\n"),3,"error.log");
    //error_log("Zambia: SECP: gpafp messages: $messages\n",3,"error.log");
    //echo "Zambia: SECP: gpafp:".(($status)?"true":"false");
    for ($i = 1; $i <= $partAvailRows; $i++) {
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
    $message_error="";
    $message_fatal="";
    $participant_arr['badgeid']=stripslashes($_POST['badgeid']);
    $participant_arr['firstname']=stripslashes($_POST['firstname']);
    $participant_arr['lastname']=stripslashes($_POST['lastname']);
    $participant_arr['badgename']=stripslashes($_POST['badgename']);
    $participant_arr['phone']=stripslashes($_POST['phone']);
    $participant_arr['email']=stripslashes($_POST['email']);
    $participant_arr['postaddress']=stripslashes($_POST['postaddress']);
    $participant_arr['regtype']=stripslashes($_POST['regtype']);
    $participant_arr['bestway']=stripslashes($_POST['bestway']);
    $participant_arr['interested']=stripslashes($_POST['interested']);
    $participant_arr['bio']=stripslashes($_POST['bio']);
    $participant_arr['pubsname']=stripslashes($_POST['pubsname']);
    $participant_arr['password']=md5('changeme');
    $participant_arr['willmoderate']=stripslashes($_POST['willmoderate']);
    $participant_arr['willparteng']=stripslashes($_POST['willparteng']);
    $participant_arr['willpartengtrans']=stripslashes($_POST['willpartengtrans']);
    $participant_arr['willpartfre']=stripslashes($_POST['willpartfre']);
    $participant_arr['willpartfretrans']=stripslashes($_POST['willpartfretrans']);
    $participant_arr['speaksenglish']=stripslashes($_POST['speaksenglish']);
    $participant_arr['speaksfrench']=stripslashes($_POST['speaksfrench']);
    $participant_arr['speaksother']=stripslashes($_POST['speaksother']);
    $participant_arr['datacleanupid']=stripslashes($_POST['datacleanpuid']);
    $participant_arr['otherlangs']=stripslashes($_POST['otherlangs']);
    $error_status=$status;
    $message_error=$messages;
    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < 5) OR
        (strlen($participant_arr['badgename']) < 5) OR
        (strlen($participant_arr['pubsname']) < 5)) {
             $message_error="All name fields are required and minimum length is 5 characters.  <BR>\n";
             $error_status=true;
             }
    if (!is_email($participant_arr['email'])) {
        $message_error.="Email address is not valid.  <BR>\n";
        $error_status=true;
        }
    if ($error_status) {
        $message_error.="Database not updated.  <BR>\n";
        RenderEditCreateParticipant($action,$participant_arr,$message_error,$message_fatal);
        exit();
        }
    prepare_db();
    if ($action=="create") {
            for ($i=1; $i<2; $i++) { // loop to make attempt twice incase of concurrency issue
                $query = "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='2' AND badgeid NOT IN ('53159','6499')";
                $result=mysql_query($query,$link);
                if (!$result) {
                    $message_error="Unrecoverable error updating database.  Database not updated.<BR>\n";
                    $message_error.=$query;
                    RenderError($title,$message_error);
                    exit();
                    }
                if (mysql_num_rows($result)!=1) {
                    $message_error="Database query returned unexpected number of rows(1 expected).  Database not updated.<BR>\n";
                    $message_error.=$query;
                    RenderError($title,$message_error);
                    exit();
                    }
                $maxbadgeid=mysql_result($result,0);
                //error_log("Zambia: SubmitEditCreateParticipant.php: maxbadgeid: $maxbadgeid");
                sscanf($maxbadgeid,"%d",$x);
                $participant_arr['badgeid']=sprintf("%d",$x+1); // convert to num; add 1; convert back to string
                $j=MakeQueryEditCreateParticipant('create',$query_arr,$participant_arr);
                //echo "<P>Zambia: SubmitEditCreateParticipant.php: query: {$query_arr[1]}</P>\n";
                //echo "<P>Zambia: SubmitEditCreateParticipant.php: query: {$query_arr[2]}</P>\n";
                //exit();
                $result=mysql_query("START TRANSACTION;",$link);
                if (mysql_errno($link)!=0) {
                    $message_error="Database error: failed to start transaction.  Database not updated.<BR>\n";
                    RenderError($title,$message_error);
                    exit();
                    }
                $result=mysql_query($query_arr[1],$link);
                $errno=mysql_errno($link);
                //error_log("Zambia: SubmitEditCreateParticipant.php: query errno: $errno");
                if ($errno==1062) { // primary key violation; loop again and hope it was just a concurrency problem
                    $result=mysql_query("ROLLBACK;",$link);
                    if ($errno!=0) {
                        $message_error="Database error: failed to rollback transaction.  Database not updated.<BR>\n";
                        RenderError($title,$message_error);
                        exit();
                        }
                    continue;
                    }
                if ($errno!=0) {
                    $message_error="Database error: unknown.  Database not updated.<BR>\n";
                    $message_error.=$query_arr[1];
                    RenderError($title,$message_error);
                    exit();
                    }
                $result=mysql_query($query_arr[2],$link);
                $errno=mysql_errno($link);
                //error_log("Zambia: SubmitEditCreateParticipant.php: query2 errno: $errno");
                if ($errno==1062) { // primary key violation; loop again and hope it was just a concurrency problem
                    $result=mysql_query("ROLLBACK;",$link);
                    if (mysql_errno($link)!=0) {
                        $message_error="Database error: failed to rollback transaction.  Database not updated.<BR>\n";
                        RenderError($title,$message_error);
                        exit();
                        }
                    continue;
                    }
                if ($errno!=0) {
                    $message_error="Database error: unknown.  Database not updated.<BR>\n";
                    $message_error.=$query_arr[2];
                    RenderError($title,$message_error);
                    exit();
                    }
                $query="REPLACE ParticipantAvailability set badgeid='{$participant_arr['badgeid']}'";
                $result=mysql_query($query,$link);
                updateParticipantAvailabilityTimes($participant_arr['badgeid']);
                $result=mysql_query("COMMIT;",$link);
                $errno=mysql_errno($link);
                if ($errno!=0) {
                    $message_error="Database error: failed to commit transaction.  Database not updated.<BR>\n";
                    RenderError($title,$message_error);
                    exit();
                    }
                break;
                }
            if ($errno!=0) { // ran through loop but never succeeded
                $message_error="Database error: Tried several times, but had repeated key problems.  Database not updated.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            // insert was successful.  Prepare to do another one.
            $title="Add Participant";
            $participant_arr['badgeid']='';
            $participant_arr['password']="changeme";
            $participant_arr['bestway']=""; //null means hasn't logged in yet.
            $participant_arr['interested']=""; //null means hasn't logged in yet.
            $participant_arr['bio']="";
            $participant_arr['bioeditstatusid']=1; //not edited -- whatever is first step
            $participant_arr['pubsname']="";
            $participant_arr['firstname']="";
            $participant_arr['lastname']="";
            $participant_arr['badgename']="";
            $participant_arr['phone']="";
            $participant_arr['email']="";
            $participant_arr['postaddress']="";
            $participant_arr['masque']=0; // not participating the the masquerade
            $participant_arr['willmoderate']=0; //
            $participant_arr['willparteng']=0; //
            $participant_arr['willpartengtrans']=0; //
            $participant_arr['willpartfre']=0; //
            $participant_arr['willpartfretrans']=0; //
            $participant_arr['speaksfrench']=0; //
            $participant_arr['speaksenglish']=0; //
            $participant_arr['speaksother']=0; //
            $participant_arr['otherlangs']=""; //
            $participant_arr['datacleanupid']=1; // data not cleaned up
            for ($i = 1; $i <= $partAvailRows; $i++) {
                if ($i <= AVAILABILITY_ROWS) {
                        $partAvail["availstartday_$i"]=0;
                        $partAvail["availstarttime_$i"]=0;
                        $partAvail["availendday_$i"]=0;
                        $partAvail["availendtime_$i"]=0;
                        }
                    else {
                        unset ($partAvail["availstartday_$i"]);
                        unset ($partAvail["availstarttime_$i"]);
                        unset ($partAvail["availendday_$i"]);
                        unset ($partAvail["availendtime_$i"]);
                        }
                }
            $participant_arr['num_availability_slots']=AVAILABILITY_ROWS;
            $message_error="Participant added successfully.";
            $message_fatal="";
            RenderEditCreateParticipant($action,$participant_arr,$message_error,$message_fatal);
            exit();
            }
        else { // insert
            $queries=MakeQueryEditCreateParticipant('edit',$query_arr,$participant_arr);
            //echo "<P>Zambia: SubmitEditCreateParticipant.php: query: {$query_arr[1]}</P>\n";
            //echo "<P>Zambia: SubmitEditCreateParticipant.php: query: {$query_arr[2]}</P>\n";
            //exit();
            $result=mysql_query("START TRANSACTION;",$link);
            if (!$result) {
                $message_error="Database error: failed to start transaction.  Database not updated.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            for ($j=1; $j<=$queries; $j++) {
                $result=mysql_query($query_arr[$j],$link);
                if (!$result) {
                    $result=mysql_query("ROLLBACK;",$link);
                    $message_error="Database error.  Database not updated.<BR>\n";
                    $message_error.=$query_arr[$j];
                    RenderError($title,$message_error);
                    exit();
                    }
                }
            updateParticipantAvailabilityTimes($participant_arr['badgeid']);
            $result=mysql_query("COMMIT;",$link);
            if (!$result) {
                $message_error="Database error: failed to commit transaction.  Database not updated.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            $message_error="Database updated successfully.<BR>\n";
            RenderError($title,$message_error);
            exit();
            }
?>

