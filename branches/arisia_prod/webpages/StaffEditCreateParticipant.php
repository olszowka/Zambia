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
            $query="SELECT bestway, interested, bio, pubsname ";
            $query.=" FROM Participants where badgeid='$badgeid'";
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
            $participant_arr['bestway']=$result_array['bestway'];
            $participant_arr['interested']=$result_array['interested'];
            $participant_arr['bio']=$result_array['bio'];
            $participant_arr['pubsname']=$result_array['pubsname'];
            }
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    exit();
?>
