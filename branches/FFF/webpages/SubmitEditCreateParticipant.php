<?php
    require_once ('StaffCommonCode.php');
    require ('StaffEditCreateParticipant_FNC.php');
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
        } elseif ($action=="edit") {
	$title="Edit Participant";
	}
    $message_error="";
    $message_warn="";
    $participant_arr['badgeid']=stripslashes($_POST['selbadgeid']);
    $participant_arr['firstname']=stripslashes($_POST['firstname']);
    $participant_arr['lastname']=stripslashes($_POST['lastname']);
    $participant_arr['badgename']=stripslashes($_POST['badgename']);
    $participant_arr['phone']=stripslashes($_POST['phone']);
    $participant_arr['email']=stripslashes($_POST['email']);
    $participant_arr['postaddress1']=stripslashes($_POST['postaddress1']);
    $participant_arr['postaddress2']=stripslashes($_POST['postaddress2']);
    $participant_arr['postcity']=stripslashes($_POST['postcity']);
    $participant_arr['poststate']=stripslashes($_POST['poststate']);
    $participant_arr['postzip']=stripslashes($_POST['postzip']);
    $participant_arr['regtype']=stripslashes($_POST['regtype']);
    $participant_arr['bestway']=stripslashes($_POST['bestway']);
    $participant_arr['interested']=stripslashes($_POST['interested']);
    $participant_arr['permroleid']=stripslashes($_POST['permroleid']);
    $participant_arr['bio']=stripslashes($_POST['bio']);
    $participant_arr['altcontact']=stripslashes($_POST['altcontact']);
    $participant_arr['prognotes']=stripslashes($_POST['prognotes']);
    $participant_arr['pubsname']=stripslashes($_POST['pubsname']);
    $participant_arr['password']=md5('changeme');
    $error_status=false;
// Commented out, becuase some people have short names
/*    if ((strlen($participant_arr['firstname'])+strlen($participant_arr['lastname']) < 5) OR
        (strlen($participant_arr['badgename']) < 5) OR
        (strlen($participant_arr['pubsname']) < 5)) {
             $message_error="All name fields are required and minimum length is 5 characters.  <BR>\n";
             $error_status=true;
             } */
    if (!is_email($participant_arr['email'])) {
        $message_error.="Email address is not valid.  <BR>\n";
        $error_status=true;
        }
    if ($error_status) {
        $message_error.="Database not updated.  <BR>\n";
        RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
        exit();
        }
    prepare_db();
    for ($i=1; $i<2; $i++) { // loop to make attempt twice incase of concurrency issue
        if ($action=="create") {
            $query = "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='1' AND badgeid<68";
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
            $newbadgeid=sprintf("%d",$x+1); // convert to num; add 1; convert back to string
            $query = "INSERT INTO Participants (badgeid, password, bestway, interested, bio, biolockedby, altcontact, prognotes, pubsname) VALUES (";
            $query.= "'".mysql_real_escape_string($newbadgeid)."',";
            $query.= "'".mysql_real_escape_string($participant_arr['password'])."',";
            $query.= "'".mysql_real_escape_string($participant_arr['bestway'])."',";
            $query.= (($participant_arr['interested']=='')?"NULL":$participant_arr['interested']).",";
            $query.= "'".mysql_real_escape_string($participant_arr['bio'])."',";
            $query.= "NULL,"; // biolockedby
            $query.= "'".mysql_real_escape_string($participant_arr['altcontact'])."',";
            $query.= "'".mysql_real_escape_string($participant_arr['prognotes'])."',";
            $query.= "'".mysql_real_escape_string($participant_arr['pubsname'])."');";
            $query2 = "INSERT INTO CongoDump (badgeid, firstname, lastname, badgename, phone, email, postaddress1, postaddress2, postcity, poststate, postzip, regtype) VALUES (";
            $query2.= "'".mysql_real_escape_string($newbadgeid)."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['firstname'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['lastname'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['badgename'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['phone'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['email'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['postaddress1'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['postaddress2'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['postcity'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['poststate'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['postzip'])."',";
            $query2.= "'".mysql_real_escape_string($participant_arr['regtype'])."');";
            $query3 = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES (";
            $query3.= "'".mysql_real_escape_string($newbadgeid)."',";
            $query3.= "'".mysql_real_escape_string($participant_arr['permroleid'])."');";
	    } elseif ($action=="edit") {
            $query = "update Participants set ";
            $query.= "bestway=\"".mysql_real_escape_string($participant_arr['bestway'])."\",";
            $query.= "interested=\"".(($participant_arr['interested']=='')?"NULL":$participant_arr['interested'])."\",";
            $query.= "bio=\"".mysql_real_escape_string($participant_arr['bio'])."\",";
            $query.= "altcontact=\"".mysql_real_escape_string($participant_arr['altcontact'])."\",";
            $query.= "prognotes=\"".mysql_real_escape_string($participant_arr['prognotes'])."\",";
            $query.= "pubsname=\"".mysql_real_escape_string($participant_arr['pubsname'])."\"";
            $query.= " WHERE badgeid=\"".$participant_arr['badgeid']."\";";
            $query2 = "update CongoDump set ";
            $query2.= "firstname=\"".mysql_real_escape_string($participant_arr['firstname'])."\",";
            $query2.= "lastname=\"".mysql_real_escape_string($participant_arr['lastname'])."\",";
            $query2.= "badgename=\"".mysql_real_escape_string($participant_arr['badgename'])."\",";
            $query2.= "phone=\"".mysql_real_escape_string($participant_arr['phone'])."\",";
            $query2.= "email=\"".mysql_real_escape_string($participant_arr['email'])."\",";
            $query2.= "postaddress1=\"".mysql_real_escape_string($participant_arr['postaddress1'])."\",";
            $query2.= "postaddress2=\"".mysql_real_escape_string($participant_arr['postaddress2'])."\",";
            $query2.= "postcity=\"".mysql_real_escape_string($participant_arr['postcity'])."\",";
            $query2.= "poststate=\"".mysql_real_escape_string($participant_arr['poststate'])."\",";
            $query2.= "postzip=\"".mysql_real_escape_string($participant_arr['postzip'])."\",";
            $query2.= "regtype=\"".mysql_real_escape_string($participant_arr['regtype'])."\"";
            $query2.= " WHERE badgeid=\"".$participant_arr['badgeid']."\";";
            $query3 = "SELECT badgeid from CongoDump where badgeid=\"".$participant_arr['badgeid']."\";";
	    }
        $result=mysql_query("START TRANSACTION;",$link);
        if (mysql_errno($link)!=0) {
            $message_error="Database error: failed to start transaction.  Database not updated.<BR>\n";
            RenderError($title,$message_error);
            exit();
	    }
        $result=mysql_query($query,$link);
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
            $message_error.=$query;
            RenderError($title,$message_error);
            exit();
            }
        $result=mysql_query($query2,$link);
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
            $message_error="Database errorx: unknown.  Database not updated.<BR>\n";
            $message_error.=$query2;
            RenderError($title,$message_error);
            exit();
            }
        $result=mysql_query($query3,$link);
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
            $message_error="Database errorx: unknown.  Database not updated.<BR>\n";
            $message_error.=$query3;
            RenderError($title,$message_error);
            exit();
            }
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
    $message_warn="Participant ".$action."ed successfully.";
    $message_error="";
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    exit();
?>

