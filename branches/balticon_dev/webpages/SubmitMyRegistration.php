<?php
    $title="My Profile";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
	global $message_error;
	$badgename = false;
	//$foo = print_r($_POST,true);
	//echo(preg_replace("/\n/","<BR>",$foo));
	//exit;
	if (($x=$_POST['ajax_request_action']) != "update_registration") {
		$message_error="Invalid ajax_request_action: $x.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();		
		}
	$may_edit_reg = may_I('EditReg');
	if(!$may_edit_reg) {
		$message_error="You may not update your registration information at this time.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();
		}
	$query = "UPDATE CongoDump SET ";
	$updateClause = "";
	$query_end = " WHERE badgeid = $badgeid";
	if (isset($_POST['firstname'])) {
		$x = $_POST['firstname'];
		$updateClause.="firstname=\"$x\", ";
		}
		if (isset($_POST['lastname'])) {
		$x = $_POST['lastname'];
		$updateClause.="lastname=\"$x\", ";
		}
		if (isset($_POST['middleInit'])) {
		$x = $_POST['middleInit'];
		$updateClause.="middleInit=\"$x\", ";
		}
		if (isset($_POST['regtype'])) {
		$x = $_POST['regtype'];
		//echo("\"" . $x . "\"");
		if(($x == "None") or ($x == "BSFS") or ($x == "Paid") or ($x == "Dealer") or ($x == "Volunteer") or ($x == "Participant")) {
			$updateClause.="regtype=\"$x\", ";
			}
		else if(may_I('Staff') and (($x == "Comp") or($x == "ConfirmedParticipant") or($x == "Guest of Honor") or($x == "Staff"))) {
			$updateClause.="regtype=\"$x\", ";
			}
		}
		if (isset($_POST['badgename'])) {
		$x = $_POST['badgename'];
		$updateClause.="badgename=\"$x\", ";
		$badgename = true;
		}
		if (isset($_POST['phone'])) {
		$x = $_POST['phone'];
		$updateClause.="phone=\"$x\", ";
		}
		if (isset($_POST['email'])) {
		$x = $_POST['email'];
		$updateClause.="email=\"$x\", ";
		}
		if (isset($_POST['postaddress1'])) {
		$x = $_POST['postaddress1'];
		$updateClause.="postaddress1=\"$x\", ";
		}
		if (isset($_POST['postaddress2'])) {
		$x = $_POST['postaddress2'];
		$updateClause.="postaddress2=\"$x\", ";
		}
		if (isset($_POST['postcity'])) {
		$x = $_POST['postcity'];
		$updateClause.="postcity=\"$x\", ";
		}
		if (isset($_POST['poststate'])) {
		$x = $_POST['poststate'];
		$updateClause.="poststate=\"$x\", ";
		}
		if (isset($_POST['postcode'])) {
		$x = $_POST['postcode'];
		$updateClause.="postcode=\"$x\", ";
		}
		if (isset($_POST['postcountry'])) {
		$x = $_POST['postcountry'];
		$updateClause.="postcountry=\"$x\", ";
		}
	if (!$updateClause) {
		$message_error="No data found to change.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();		
		}
	if ($updateClause) {
		if (!mysql_query_with_error_handling($query.mb_substr($updateClause,0,-2).$query_end)) {
			RenderErrorAjax($message_error);
			exit();
			}
		}
	echo ("<span class=\"regmsg\">");
	echo ("Database updated successfully.\n");
	if ($badgename)
		echo ("Badgename updates will be reflected next time you log in");
	echo ("</span>\n");
    exit();
?>
