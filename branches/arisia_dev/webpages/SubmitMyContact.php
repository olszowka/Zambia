<?php
    $title="My Profile";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
	global $message_error;
	$password = false;
	$pubsname = false;
	//$foo = print_r($_GET,true);
	//echo(preg_replace("/\n/","<BR>",$foo));
	//exit;
	if (($x=$_GET['ajax_request_action']) != "update_participant") {
		$message_error="Invalid ajax_request_action: $x.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();		
		}
	$may_edit_bio = may_I('EditBio');
	$query = "UPDATE Participants SET ";
	$updateClause = "";
	$query_end = " WHERE badgeid = $badgeid";
	if (isset($_GET['interested'])) {
		$x=$_GET['interested'];
		if ($x==1 || $x==2)
				$updateClause.="interested=$x, ";
			else
				$updateClause.="interested=0, ";
		}
	if (isset($_GET['share_email'])) {
		$x=$_GET['share_email'];
		if ($x==0 || $x==1)
				$updateClause.="share_email=$x, ";
			else
				$updateClause.="share_email=null, ";
		}			
	if (isset($_GET['bestway'])) {
		$x=$_GET['bestway'];
		if ($x=="Email" || $x=="Postal mail" || $x=="Phone")
				$updateClause.="bestway=\"$x\", ";
			else {
				$message_error="Invalid value for bestway: $bestway.  Database not updated.";
				RenderErrorAjax($message_error);
				exit();
				}
		}
	if (isset($_GET['password'])) {
		$password = md5(stripslashes($_GET['password']));
		$updateClause.="password=\"$password\", ";
		}
	if (isset($_GET['pubsname']))
		if ($may_edit_bio) {
				$pubsname = stripslashes($_GET['pubsname']);
				$updateClause.="pubsname=\"".mysql_real_escape_string($pubsname)."\", ";
				}
			else {
				$message_error="You may not update your name for publications at this time.  Database not updated.";
				RenderErrorAjax($message_error);
				exit();
				}
	if (isset($_GET['bioText']))
		if ($may_edit_bio)
				$updateClause.="bio=\"".mysql_real_escape_string(stripslashes($_GET['bioText']))."\", ";
			else {
				$message_error="You may not update your biography at this time.  Database not updated.";
				RenderErrorAjax($message_error);
				exit();
				}
	$query2 = "REPLACE ParticipantHasCredential (badgeid, credentialid) VALUES ";
	$valuesClause2 = "";
	$query3 = "DELETE FROM ParticipantHasCredential WHERE badgeid = $badgeid AND credentialid in (";
	$credentialClause3 = "";
	foreach ($_GET as $name => $value) {
		if (substr($name,0,13)!="credentialCHK")
			continue;
		$ccid = substr($name,13);
		switch($value) {
			case "true":
				$valuesClause2 .= ($valuesClause2 ? ", " : "")."($badgeid, $ccid)";
				break;
			case "false":
				$credentialClause3 .= ($credentialClause3 ? ", " : "").$ccid;
				break;
			default:
				$message_error="Invalid value for $name: $value.  Database not updated.";
				RenderErrorAjax($message_error);
				exit();
				break;
			}
		}
	if (!$updateClause && !$valuesClause2 && !$credentialClause3) {
		$message_error="No data found to change.  Database not updated.";
		RenderErrorAjax($message_error);
		exit();		
		}
	if ($updateClause) {
		if (!mysql_query_with_error_handling($query.substr($updateClause,0,-2).$query_end)) {
			RenderErrorAjax($message_error);
			exit();
			}
		}
	if ($valuesClause2) {
		if (!mysql_query_with_error_handling($query2.$valuesClause2)) {
			RenderErrorAjax($message_error);
			exit();
			}
		}
	if ($credentialClause3) {
		if (!mysql_query_with_error_handling($query3.$credentialClause3.")")) {
			RenderErrorAjax($message_error);
			exit();
			}
		}
	echo ("<span class=\"regmsg\">");
	if ($password) {
		echo "Password updated. ";
		$_SESSION['password']=$password;
		}
	echo ("Database updated successfully. </span>\n");
	if ($pubsname)
		$_SESSION['badgename']=$pubsname;
    exit();
?>
