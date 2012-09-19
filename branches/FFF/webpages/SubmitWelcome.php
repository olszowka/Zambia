<?php
    // The current version of the Welcome Page does not include a form for doing this, but this
    // code for SubmitWelcome supports having the user change his password directly on the
    // Welcome page.  A previous version prompted the user to change his password if it was
    // still the initial password.
    require ('PartCommonCode.php');
    global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

    $title="Welcome";
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

// If interested is changed.
if ($_POST['interested']!=$participant['interested']) {
  $query ="UPDATE $ReportDB.Interested SET ";
  $query.="interestedtypeid=".$_POST['interested']." ";
  $query.="WHERE badgeid=\"".$badgeid."\" AND conid=".$_SESSION['conid'];
  if (!mysql_query($query,$link)) {
    $message.=$query."<BR>Error updating Interested table.  Database not updated.";
    echo "<P class=\"errmsg\">".$message."</P>\n";
    return;
  }
  ereg("Rows matched: ([0-9]*)", mysql_info($link), $r_matched);
  if ($r_matched[1]==0) {
    $element_array=array('conid','badgeid','interestedtypeid');
    $value_array=array($_SESSION['conid'], $badgeid, mysql_real_escape_string(stripslashes($_POST['interested'])));
    $message.=submit_table_element($link,$title,"$ReportDB.Interested", $element_array, $value_array);
  } elseif ($r_matched[1]>1) {
    $message.="There might be something wrong with the table, there are multiple interested elements for this year.";
  }
  $participant['interested']=$_POST['interested'];
}

// If password is changed.
    if ($password=="" and $cpassword=="") {
            $update_password=false;
	    }
        elseif ($password==$cpassword) {
            $update_password=true;
            }
        else {
            $message_error="Passwords do not match each other.  Database not updated.";
            if (retrieve_participant_from_db($badgeid)==0) {
                    require ('renderWelcome.php');
                    exit();
                    }
                else {
                    $message=$message2."<BR>Failure to re-retrieve data for Participant.";
                    RenderError($title,$message);
                    exit();
                    }
            }
	$query = "UPDATE $ReportDB.Participants SET ";
	if ($update_password==true) {
		$query=$query."password=\"".md5($password)."\", ";
		}
	$query.=" WHERE badgeid=\"".$badgeid."\"";                               //"
    if (!mysql_query($query,$link)) {
		$message=$query."<BR>Error updating database.  Database not updated.";
		RenderError($title,$message);
		exit();
		}
    $message="Database updated successfully.";
    if ($update_password==true) {
	$_SESSION['password']=md5($password);
	}
    if (retrieve_participant_from_db($badgeid)==0) {
            require ('renderWelcome.php');
            exit();
            }
        else {
            $message=$message2."<BR>Failure to re-retrieve data for Participant.";
            RenderError($title,$message);
            exit();
            }
    $result=mysql_query("Select password from $ReportDB.Participants where badgeid='".$badgeid."'",$link);
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
/*
    $result=mysql_query("Select badgename from $ReportDB.Participants where badgeid='".$badgeid."'",$link);
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
*/
    require ('ParticipantHome.php');
    exit();
?>
