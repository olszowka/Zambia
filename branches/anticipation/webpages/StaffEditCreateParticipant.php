<?php
    require_once ('StaffCommonCode.php');
    require ('StaffEditCreateParticipant_FNC.php');
    global $partAvail;
    if (!isset($_GET['action'])) {
        $title="Edit or Add Participant";
        $message_error="Required parameter 'action' not found.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    $action=$_GET['action'];
    if (!($action=="edit"||$action=="create")) {
        $title="Edit or Add Participant";
        $message_error="Parameter 'action' contains invalid value.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if ($action=="create") { //initialize participant array
            $title="Add Participant";
            if (!may_I('create_participant')) {
                $message_error="You do not have permission to access this page.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            $participant_arr['password']="changeme";
            $participant_arr['bestway']=""; //null means hasn't logged in yet.
            $participant_arr['interested']="1"; //1 means interested and attending
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
            }
        else { // get participant array from database
            $title="Edit Participant";
            if (!may_I('edit_participant')) {
                $message_error="You do not have permission to access this page.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            if (!(isset($_GET['badgeid']))) {
                $message_error="Required parameter 'badgeid' not found.  Can't continue.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            $badgeid=mysql_real_escape_string($_GET['badgeid'],$link);
            $query="SELECT firstname, lastname, badgename, phone, email, postaddress, regtype ";
            $query.=" FROM CongoDump where badgeid='$badgeid'";
            if (($result=mysql_query($query,$link))===false) {
                $message_error="Error retrieving data from database<BR>\n";
                $message_error.=$query;
                RenderError($title,$message_error);
                exit();
                }
            if (mysql_num_rows($result)!=1) {
                $message_error="Database query did not return expected number of rows (1).<BR>\n";
                $message_error.=$query;
                RenderError($title,$message_error);
                exit();
                }
            $result_array=mysql_fetch_array($result,MYSQL_ASSOC);
            $participant_arr['firstname']=$result_array['firstname'];
            $participant_arr['lastname']=$result_array['lastname'];
            $participant_arr['badgename']=$result_array['badgename'];
            $participant_arr['phone']=$result_array['phone'];
            $participant_arr['email']=$result_array['email'];
            $participant_arr['postaddress']=$result_array['postaddress'];
            $participant_arr['regtype']=$result_array['regtype'];
            $query=<<<EOD
SELECT bestway, interested, bio, pubsname, masque, willmoderate, willparteng,
        willpartengtrans, willpartfre, willpartfretrans, speaksFrench,
        speaksEnglish, speaksOther, otherLangs, datacleanupid
    FROM Participants where badgeid='$badgeid'
EOD;
            if (($result=mysql_query($query,$link))===false) {
                $message_error="Error retrieving data from database<BR>\n";
                $message_error.=$query;
                RenderError($title,$message_error);
                exit();
                }
            if (mysql_num_rows($result)!=1) {
                $message_error="Database query did not return expected number of rows (1).<BR>\n";
                $message_error.=$query;
                RenderError($title,$message_error);
                exit();
                }
            $result_array=mysql_fetch_array($result,MYSQL_ASSOC);
            $participant_arr['badgeid']=$badgeid;
            $participant_arr['bestway']=$result_array['bestway'];
            $participant_arr['interested']=$result_array['interested'];
            $participant_arr['bio']=$result_array['bio'];
            $participant_arr['pubsname']=$result_array['pubsname'];
            $participant_arr['masque']=$result_array['masque'];
            $participant_arr['willmoderate']=$result_array['willmoderate'];
            $participant_arr['willparteng']=$result_array['willparteng'];
            $participant_arr['willpartengtrans']=$result_array['willpartengtrans'];
            $participant_arr['willpartfre']=$result_array['willpartfre'];
            $participant_arr['willpartfretrans']=$result_array['willpartfretrans'];
            $participant_arr['speaksfrench']=$result_array['speaksFrench'];
            $participant_arr['speaksenglish']=$result_array['speaksEnglish'];
            $participant_arr['speaksother']=$result_array['speaksOther'];
            $participant_arr['otherlangs']=$result_array['otherLangs'];
            $participant_arr['datacleanupid']=$result_array['datacleanupid'];
            //$partAvail=array('red','blue');
            $x = retrieve_participantAvailability_from_db($badgeid);
            if ($x!=0) {
                $message_error="Problem retrieving ParticipantAvailability... from DB. error code=$x.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            $i=1;
            //error_log("SECP: before populate \$partAvail: \$i: $i \n",3,"error.log");
            //$x=print_r($partAvail,true);
            //$x="Hi\n".$x;
            //error_log($x,3,"error.log");
            while (isset($partAvail["starttimestamp_$i"])) {
                //error_log("SECP: populate \$partAvail: \$i: $i \n",3,"error.log");
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
            $participant_arr['num_availability_slots']=$i-1;
            }
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    exit();
?>
