<?php
	$title="Update My Contact Info";
    require ('db_functions.php');
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $interested = $_POST[interested];
    $bestway = $_POST[bestway];
    $password = $_POST[password];
    $cpassword = $_POST[cpassword];
    $bio = stripslashes($_POST["bio"]);
    if (strlen($bio)>500) {
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
	$query = "UPDATE Participants SET ";
	if ($update_password==true) {
		$query=$query."password=\"".md5($password)."\", ";
		}
	$query.="bestway=\"".$bestway."\", ";
	$query.="interested=".$interested.", ";
        $query.="bio=\"".mysql_real_escape_string($bio,$link);
	$query.="\" WHERE badgeid=\"".$badgeid."\"";                               //"
    if (!mysql_query($query,$link)) {
		$message=$query."<BR>Error updating database.  Database not updated.";
		RenderError($title,$message);
		exit();
		}
    $message="Database updated successfully.";
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
    session_start();
    if ($result) {
    		$dbobject=mysql_fetch_object($result);
    		$badgename=$dbobject->badgename;
    		$_SESSION['badgename']=$badgename;
    		}
    	else {
    		$_SESSION['badgename']="";
		}
    $_SESSION['badgeid']=$badgeid;
    $_SESSION['password']=$dbpassword;
    require ('ParticipantHome.php');
    exit();
?>
