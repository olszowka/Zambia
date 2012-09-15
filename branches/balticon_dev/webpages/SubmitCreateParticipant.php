<?php
    $title="My Profile";
    require_once('error_functions.php');
    require_once('CommonCode.php');
	$_SESSION['role']="Staff";

	global $message_error;
	
function fetch_participant($badgeid) {
//RenderErrorAjax("Reached fetch_participant. $badgeid");
//exit;
	//error_log("Reached fetch_participant.");
	$query["fetchParticipants"] = <<<EOD
SELECT
		P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.regtype, CD.regdepartment
    FROM
			 Participants P
		JOIN CongoDump CD ON P.badgeid = CD.badgeid
EOD;
	$query["fetchParticipants"] .= " WHERE P.badgeid=".$badgeid.";";
	$query["permissionRoles"] = "SELECT badgeid, permroleid FROM UserHasPermissionRole WHERE badgeid=".$badgeid.";";
//$message_error = "barf";
	$resultXML=mysql_query_XML($query);
//RenderErrorAjax("QueryDone." . $query["fetchParticipants"]);
//exit; 
	if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
        }
//RenderErrorAjax("Query Passed.");
//exit; 
	header("Content-Type: text/xml"); 
	echo($resultXML->saveXML());
	exit();
}

	//RenderErrorAjax(print_r($_POST,true));
	//echo(preg_replace("/\n/","<BR>",$foo));
	//exit;
	if (($x=$_POST['ajax_request_action']) != "create_participant") {
		$message_error="Invalid ajax_request_action: $x.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();		
		}
	if (!isset($_POST['email'])) {
		$message_error="Did not receive email address. Database not updated.";
		RenderErrorAjax($message_error);
		exit();
		}
	if (!isset($_POST['pname'])) {
		$message_error="Did not receive participant name. Database not updated.";
		RenderErrorAjax($message_error);
		exit();
		}
		
	$may_edit_reg = may_I('create_participant');
	if(!$may_edit_reg) {
		$message_error="You may not create a participant at this time.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();
		}
		
	$query = "INSERT INTO CongoDump (badgename,email,regtype,regdepartment) VALUES ('";
	$updateClause = mysql_real_escape_string(stripslashes($_POST['pname'])) ."','". 
					mysql_real_escape_string(stripslashes($_POST['email'])) ."','";
	$query_end = ");";
	if(!isset($_POST['regtype'])) { $updateClause .= "None','"; }
	else { $updateClause .= mysql_real_escape_string(stripslashes($_POST['regtype'])) ."','"; }
	
	if(!isset($_POST['regdepartment'])) { $updateClause .= "'"; }
	else { $updateClause .= mysql_real_escape_string(stripslashes($_POST['regdepartment'])) ."'"; }
	
	$insert_result = mysql_query_with_error_handling($query.$updateClause.$query_end);
		$new_id = mysql_insert_id();
	if(!$insert_result || $new_id == 0) {
		RenderErrorAjax($message_error);
		exit();
		}
	//RenderErrorAjax("first query done");
	//exit;
	
	$query2 = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES ";
	$updateClause2 = "";
	$query2_end = ";";
	if(isset($_POST['adminStatus']) && may_I('Admin')) {
		if($_POST['adminStatus']=="true" || $_POST['adminStatus']=="TRUE") {
			$updateClause2 .= "(".$new_id.",1), ";
		} 
	}
	if(isset($_POST['staffStatus']) && may_I('Admin')) {
		if($_POST['staffStatus']=="true" || $_POST['staffStatus']=="TRUE") {
			$updateClause2 .= "(".$new_id.",2), ";
		}
	}
	if(isset($_POST['partStatus'])) {
		if($_POST['partStatus']=="true" || $_POST['partStatus']=="TRUE") {
			$updateClause2 .= "(".$new_id.",3), ";
		}
	}
	
	//RenderErrorAjax("pre final query");
	$sql_partInsert = "INSERT INTO participants VALUES (". $new_id . ",'4cb9c8a8048fd02294477fcb1a41191a', NULL, NULL, NULL,'".
						$_POST['pname']."',NULL, NULL);";
	$sql_roleInsert = "INSERT INTO `ParticipantHasRole` VALUES (". $new_id . ",1);";
	if(!mysql_query_with_error_handling($sql_partInsert) || 
		!mysql_query_with_error_handling($query2.mb_substr($updateClause2,0,-2).$query2_end) || 
				!mysql_query_with_error_handling($sql_roleInsert)){
			RenderErrorAjax($message_error);
			exit();
			}
	//RenderErrorAjax("leaving add stuff");
	//exit;
	fetch_participant($new_id);
    exit();
?>
