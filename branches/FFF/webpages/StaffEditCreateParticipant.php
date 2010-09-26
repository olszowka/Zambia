<?php
    require_once ('StaffCommonCode.php');
    require ('StaffEditCreateParticipant_FNC.php');
    if (isset($_GET['action'])) {
        $action=$_GET['action'];
        }
      elseif (isset($_POST['action'])) {
        $action=$_POST['action'];
        }
      else {
        $title="Edit or Add Participant";
        $message_error="Required parameter 'action' not found.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if (!($action=="edit"||$action=="create")) {
        $title="Edit or Add Participant";
        $message_error="Parameter 'action' contains invalid value.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if ($action=="create") { //initialize participant array
            $title="Add Participant";
            staff_header($title);
            $participant_arr['password']="changeme";
	    $participant_arr['badgeid']="auto-assigned";
            $participant_arr['bestway']=""; //null means hasn't logged in yet.
            $participant_arr['interested']=""; //null means hasn't logged in yet.
            $participant_arr['permroleid']=""; //null means hasn't logged in yet.
            $participant_arr['bio']="";
            $participant_arr['altcontact']="";
            $participant_arr['prognotes']="";
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
            staff_header($title);
            if (isset($_POST["badgeid"])) {
                    $selbadgeid=$_POST["badgeid"];
                    }
                elseif (isset($_GET["badgeid"])) {
                    $selbadgeid=$_GET["badgeid"];
                    }
                else {
                    $selbadgeid=0;
                    }
            $query="SELECT P.badgeid, CD.lastname, CD.firstname, CD.badgename, P.pubsname FROM Participants P, CongoDump CD ";
            $query.="where P.badgeid = CD.badgeid ORDER BY CD.lastname";
            if (!$Sresult=mysql_query($query,$link)) {
                $message=$query."<BR>Error querying database. Unable to continue.<BR>";
                echo "<P class\"errmsg\">".$message."\n";
                staff_footer();
                exit();
                }
            echo "<FORM name=\"selpartform\" method=POST action=\"StaffEditCreateParticipant.php\">\n";
	    echo "<INPUT type=\"hidden\" name=\"action\" value=\"edit\">\n";
            echo "<DIV><LABEL for=\"badgeid\">Select Participant</LABEL>\n";
            echo "<SELECT name=\"badgeid\">\n";
            echo "     <OPTION value=0 ".(($selbadgeid==0)?"selected":"").">Select Participant</OPTION>\n";
            while (list($badgeid,$lastname,$firstname,$badgename,$pubsname)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
                echo "     <OPTION value=\"".$badgeid."\" ".(($selbadgeid==$badgeid)?"selected":"");
                echo ">".htmlspecialchars($lastname).", ".htmlspecialchars($firstname);
                echo " (".htmlspecialchars($badgename)."/".htmlspecialchars($pubsname).") - ".$badgeid."</OPTION>\n";
                }
            echo "</SELECT></DIV>\n";
            echo "<P>&nbsp;\n";
            echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
            echo "</FORM>\n";
            if ($selbadgeid==0) {
                staff_footer();
                exit();
                }
	    $participant_arr['badgeid']=$selbadgeid;
            $badgeid=mysql_real_escape_string($selbadgeid,$link);
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
            $query="SELECT P.bestway, P.interested, U.permroleid, P.bio, P.pubsname, P.altcontact, P.prognotes ";
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
	    $participant_arr['altcontact']=$result_array['altcontact'];
	    $participant_arr['prognotes']=$result_array['prognotes'];
            }
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    exit();
?>
