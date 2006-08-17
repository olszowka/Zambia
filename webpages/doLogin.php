<?php
    require ('db_functions.php');
    require ('ParticipantHeader.php');
    // echo "Trying to connect to database.\n";
    if (prepare_db()===false) {
        require ('RenderErrorPart.php');
        $title="Submit Password";
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        ErrorResponse($title,$message_error);
        exit();
        };
    // echo "Connected to database.\n";
    $badgeid = $_POST['badgeid'];
    $password = stripslashes($_POST['passwd']);
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
    $result=mysql_query("Select badgename from CongoDump where badgeid='".$badgeid."'",$link);
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
    if (retrieve_participant_from_db($badgeid)==0) {
        require ('renderWelcome.php');
        exit();
        }
    $message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
    RenderError($title,$message_error);
    exit();
?>
