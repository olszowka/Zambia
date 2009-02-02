<?php
    $title="Update My Contact Info";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $interested = $_POST['interested'];
    $bestway = $_POST['bestway'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $pubsname = stripslashes($_POST['pubsname']);
    $pubsnameold = stripslashes($_POST['pubsnameold']);
    $bio = stripfancy(stripslashes($_POST['bio']));
    if (strlen($pubsname)<3) {
        $message_error="Name for publications is too short.  Please edit.  Database not updated.";
        if (getCongoData($badgeid)==0) {
                require ('renderMyContact.php');
                exit();
                }
            else {
                $message=$message."<BR>Failure to re-retrieve Congo data for Participant.";
                RenderError($title,$message);
                exit();
                }
        }
    if (strlen($bio)>MAX_BIO_LEN) {
        $message_error="Biography is too long: ".(strlen($bio))." characters.  Please edit.  Database not updated.";
        if (getCongoData($badgeid)==0) {
                require ('renderMyContact.php');
                exit();
                }
            else {
                $message=$message."<BR>Failure to re-retrieve Congo data for Participant.";
                RenderError($title,$message);
                exit();
                }
        }
    if ($password=="" and $cpassword=="") {
            $update_password=false;
	    }
        elseif ($password==$cpassword) {
            $update_password=true;
            }
        else {
            $message_error="Passwords do not match each other.  Database not updated.";
            if (getCongoData($badgeid)==0) {
                    require ('renderMyContact.php');
                    exit();
                    }
                else {
                    $message=$message."<BR>Failure to re-retrieve Congo data for Participant.";
                    RenderError($title,$message);
                    exit();
                    }
            }
    $update_pubsname=false;
    if ($pubsnameold!=$pubsname) {
        $update_pubsname=true;
        }
    if ($update_pubsname and !may_I('EditBio')) { //Don't have permission to change pubsname
        $message_error="You may not update your name for publication at this time.\n";
        if (getCongoData($badgeid)==0) {
                require ('renderMyContact.php');
                exit();
                }
            else {
                $message=$message."<BR>Failure to re-retrieve Congo data for Participant.";
                RenderError($title,$message);
                exit();
                }
        }
    $query = "UPDATE Participants SET ";
    if ($update_password==true) {
        $query=$query."password=\"".md5($password)."\", ";
        }
    if ($update_pubsname) {
        $query=$query."pubsname=\"".mysql_real_escape_string($pubsname,$link)."\", ";
        }
    $query.="bestway=\"".$bestway."\", ";
    $query.="interested=".$interested.", ";
    if (may_I('EditBio')) {
        $query.="bio=\"".mysql_real_escape_string($bio,$link);
        }
    $query.="\" WHERE badgeid=\"".$badgeid."\"";                               //"
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        RenderError($title,$message);
        exit();
        }
    $message="Database updated successfully.";
    $pubsnameold=$pubsname;
    $_SESSION['badgename']=$pubsname;
    if ($update_password==true) {
        $_SESSION['password']=md5($password);
        }
    if (getCongoData($badgeid)==0) {
            require ('renderMyContact.php');
            exit();
            }
        else {
            $message=$message."<BR>Failure to re-retrieve Congo data for Participant.";
            RenderError($title,$message);
            exit();
            }
    // It looks like code never gets past here.
    $result=mysql_query("Select password from Participants where badgeid='".$badgeid."'",$link);
    if (!$result) {
    	$message="Incorrect badgeid or password.";
        require ('login.php');
	exit();
	}
    $dbobject=mysql_fetch_object($result);
    $dbpassword=$dbobject->password;
    //echo $badgeid."<BR>".$dbpassword."<BR>".$password."<BR>".md5($password);
    //exit(0);
    if (md5($password)!=$dbpassword) {
    	$message="Incorrect badgeid or password.";
        require ('login.php');
	exit(0);
	}
    $result=mysql_query("Select badgename from Participants where badgeid='".$badgeid."'",$link);
    //session_start();
    if ($result) {
            $dbobject=mysql_fetch_object($result);
            $badgename=$dbobject->badgename;
            $_SESSION['badgename']=$badgename;
            }
        else {
            $_SESSION['badgename']="";
            }
    if (!($pubsname=="")) {
        $_SESSION['badgename']=$pubsname;
        }
    $_SESSION['badgeid']=$badgeid;
    $_SESSION['password']=$dbpassword;
    require ('ParticipantHome.php');
    exit();
?>
