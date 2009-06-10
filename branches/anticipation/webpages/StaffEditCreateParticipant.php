<?php
    require_once ('StaffCommonCode.php');
    require ('StaffEditCreateParticipant_FNC.php');
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
            }
        else { // get participant array from database
            $title="Edit Participant";
            if (!may_I('edit_participant')) {
                $message_error="You do not have permission to access this page.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            if (!(isset($_GET['badgeid']))) {((willparteng=='1')?'1':'0')."',";
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
            }
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    exit();
?>
