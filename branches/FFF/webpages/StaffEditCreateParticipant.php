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
            $participant_arr['password']="changeme";
            $participant_arr['bestway']=""; //null means hasn't logged in yet.
            $participant_arr['interested']=""; //null means hasn't logged in yet.
            $participant_arr['permroleid']=""; //null means hasn't logged in yet.
            $participant_arr['bio']="";
            $participant_arr['bioeditstatusid']=1; //not edited -- whatever is first step
            $participant_arr['pubsname']="";
            $participant_arr['firstname']="";
            $participant_arr['lastname']="";
            $participant_arr['badgename']="";
            $participant_arr['phone']="";
            $participant_arr['email']="";
            $participant_arr['postaddress1']="";
	    $participant_arr['postaddress2']="";
	    $participant_arr['postcity']="";
	    $participant_arr['poststate']="";
	    $participant_arr['postzip']="";
            }
        else { // get participant array from database
            $title="Edit Participant";
            if (!(isset($_GET['badgeid']))) {
                $message_error="Required parameter 'badgeid' not found.  Can't continue.<BR>\n";
                RenderError($title,$message_error);
                exit();
                }
            $badgeid=mysql_real_escape_string($_GET['badgeid'],$link);
            $query="SELECT firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, regtype ";
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
            $participant_arr['postaddress1']=$result_array['postaddress1'];
            $participant_arr['postaddress2']=$result_array['postaddress2'];
            $participant_arr['postcity']=$result_array['postcity'];
            $participant_arr['poststate']=$result_array['poststate'];
            $participant_arr['postzip']=$result_array['postzip'];
            $participant_arr['regtype']=$result_array['regtype'];
            $query="SELECT P.bestway, P.interested, U.permorleid, P.bio, P.pubsname ";
            $query.=" FROM Participants P";
            $query.=" JOIN UserHasPermissionRole U USING (badgeid)";
            $query.=" where badgeid='$badgeid'";
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
            $participant_arr['permroleid']=$result_array['permroleid'];
            $participant_arr['bio']=$result_array['bio'];
            $participant_arr['pubsname']=$result_array['pubsname'];
            }
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    exit();
?>
