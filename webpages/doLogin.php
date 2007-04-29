	<?php
    require ('db_functions.php');
    require ('data_functions.php');
    require ('ParticipantHeader.php');
    $title="Submit Password";
    // echo "Trying to connect to database.\n";
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($title,$message_error);
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
    set_permission_set($badgeid);
    //error_log("Zambia: Completed set_permission_set.\n");
    $message2="";
    if (retrieve_participant_from_db($badgeid)==0) {
        if(may_I('Staff')) {
            require ('StaffPage.php');
            }
        elseif (may_I('Participant')) {
            require ('renderWelcome.php');
            }
        elseif (may_I('Brainstorm')) {
            require ('renderBrainstormWelcome.php');
            }
        else {
            $message_error="There is a problem with your userid's permission configuration:  It doesn't have ";
            $message_error.="permission to access any welcome page.  Please contact Zambia staff.";
            RenderError($title,$message_error);
            }
        exit();
        }
    $message_error=$message2."<BR>Error retrieving data from DB.  No further execution possible.";
    RenderError($title,$message_error);
    exit();
?>
