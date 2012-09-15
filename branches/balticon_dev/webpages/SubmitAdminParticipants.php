<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php');

	global $message_error;
// gets data for a participant to be displayed.  Returns as XML
function fetch_participant() {
	if(!isset($_POST["badgeid"])) {
		RenderErrorAjax("no badge id passed.");
		exit;
		}
	//RenderErrorAjax("Reached fetch_participant."); exit();
	$badgeid = $_POST["badgeid"];
	if(!$badgeid) {
		RenderErrorAjax("bad badge id ". $_POST["badgeid"] . " ($badgeid)");
		exit();
		}
	$query["fetchParticipants"] = <<<EOD
SELECT
		P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.regtype, CD.regdepartment
    FROM
			 Participants P
		JOIN CongoDump CD ON P.badgeid = CD.badgeid
EOD;
	$query["fetchParticipants"] .= " WHERE P.badgeid=".$badgeid.";";
	$query["permissionRoles"] = "SELECT badgeid, permroleid FROM UserHasPermissionRole WHERE badgeid=".$badgeid.";";
/*
echo("<div>");
RenderErrorAjax($query["fetchParticipants"]);
RenderErrorAjax($query["permissionRoles"]);
echo("</div>");
exit();
*/
	$resultXML=mysql_query_XML($query);
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
        }
	header("Content-Type: text/xml"); 
	echo($resultXML->saveXML());
	exit();
}

function update_participant() {
    global $link,$message_error;
	echo(print_r($_POST));
    if(!isset($_POST["badgeid"])) {
		echo("<p class=\"errmsg\">no badge id passed</p>");
		exit;
		}
	$partid = $_POST["badgeid"];
		
	$query = "UPDATE Participants SET ";
	$updateClause ="";
	$query_end =" WHERE badgeid=\"".mysql_real_escape_string($partid)."\"";

	$query2 = "INSERT INTO UserHasPermissionRole (badgeid, permroleid) VALUES ";
	$updateClause2 = "";
	$query2_end = ";";		
	$query3 = "DELETE FROM UserHasPermissionRole WHERE badgeid=".mysql_real_escape_string($partid)." AND ";
	$updateClause3 = "";
	$query3_end = ";";
	$query4 = "UPDATE CongoDump SET ";
	$updateClause4 = "";
	$query4_end = " WHERE badgeid =".mysql_real_escape_string($partid);
	
 	if(isset($_POST["password"]) && $_POST["password"]){
		$password = $_POST["password"];
        $updateClause.="password=\"".md5($password)."\", ";
        }

	if(isset($_POST["bio"]) && $_POST["bio"]){
		if(!may_I('EditBio')) {
			echo "<p class=\"errmsg\">You may not edit bios at this time, publications have gone to print</p>";
			} 
		else {
		$bio = stripslashes($_POST["bio"]);
			$updateClause.="bio=\"".mysql_real_escape_string($bio)."\", ";
			}
        }

    if (isset($_POST["pname"]) && $_POST["pname"]) {
	if(!may_I('EditBio')) {
			echo "<p class=\"errmsg\">You may not edit names for publications at this time, publications have gone to print</p>";
			} 
		else {
			$pubsname = stripslashes($_POST["pname"]);
			$updateClause.="pubsname=\"".mysql_real_escape_string($pubsname)."\", ";
			}
        }	

	if(isset($_POST["staffnotes"]) && $_POST["staffnotes"]) {
		$staffnotes = stripslashes($_POST["staffnotes"]);
        $updateClause.="staff_notes=\"".mysql_real_escape_string($staffnotes)."\", ";
        }
		
    if (isset($_POST["interested"]) && $_POST["interested"]) {
		$interested = $_POST["interested"];
        $updateClause.="interested=".mysql_real_escape_string($interested).", ";
        } else { $interested = ""; }
	
	if(isset($_POST['adminStatus']) && may_I('Administrator')) {
		if($_POST['adminStatus']) {
			$updateClause2 .= "(".mysql_real_escape_string($partid).",1), ";
		} else {
			$updateClause3 .= "permroleid=1 OR ";
		}
	}
	if(isset($_POST['staffStatus']) && may_I('Administrator')) {
		if($_POST['staffStatus']=="true" || $_POST['staffStatus']=="TRUE") {
			$updateClause2 .= "(".mysql_real_escape_string($partid).",2), ";
		} else {
			$updateClause3 .= "permroleid=2 OR ";
		}
	}
	if(isset($_POST['partStatus'])) {
		if($_POST['partStatus']) {
			$updateClause2 .= "(".mysql_real_escape_string($partid).",3), ";
		} else {
			$updateClause3 .= "permroleid=3 OR ";
		}
	}
	if(isset($_POST["regtype"]) && $_POST["regtype"]){
		$regtype = stripslashes($_POST["regtype"]);
		$updateClause4 .= "regtype=\"".mysql_real_escape_string($regtype)."\", ";
		}

	if(isset($_POST["regdepartment"]) && $_POST["regdepartment"]) {
		$regdepartment = stripslashes($_POST["regdepartment"]);
		$updateClause4 .= "regdepartment=\"".mysql_real_escape_string($regdepartment)."\", ";
		}

		if(!$updateClause && !$updateClause2 && !$updateClause3 && !$updateClause4) {
		echo "<p class=\"errmsg\">No Changes Found.  Database Not Updated</p>";
        return;
        }
    if ($updateClause && !mysql_query_with_error_handling($query.mb_substr($updateClause,0,-2).$query_end)) {
        echo "<p class=\"errmsg\">".$message_error."</p>";
        return;
        }
	if ($updateClause2 && !mysql_query_with_error_handling($query2.mb_substr($updateClause2,0,-2).$query2_end)){
		echo "<p class=\"errmsg\">".$message_error."</p>";
        return;
		}

	if ($updateClause3 && !mysql_query_with_error_handling($query3.mb_substr($updateClause3,0,-4).$query3_end)) {
		echo "<p class=\"errmsg\">".$message_error."</p>";
        return;
		}
	if ($updateClause4 && !mysql_query_with_error_handling($query4.mb_substr($updateClause4,0,-2).$query4_end)){
		echo "<p class=\"errmsg\">".$message_error."</p>";
        return;
		}
		$message="<p class=\"regmsg\">Database updated successfully.</p>";
    if ($interested==2) {
        $query="DELETE FROM ParticipantOnSession where badgeid = \"$partid\"";
	    if (!mysql_query_with_error_handling($query)) {
	        echo "<p class=\"errmsg\">".$message_error."</p>";
	        return;
	        }
        $message.="<p class=\"regmsg\">Participant removed from ".mysql_affected_rows($link)." session(s).</p>";
        }
    echo $message;
    }

function perform_search() {
	$searchString = mysql_real_escape_string(stripslashes($_POST["searchString"]));
	if ($searchString=="")
		exit();
	//echo($searchString);
	if (is_numeric($searchString)) {
		//echo(" is Numeric");
		$query["searchParticipants"] = <<<EOD
			SELECT
					P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.regtype, CD.regdepartment
				FROM
					Participants P
				JOIN CongoDump CD ON P.badgeid = CD.badgeid
				WHERE
			        P.badgeid = $searchString
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
	} else {
		//echo(" is NOT numeric");
		$searchString='%'.$searchString.'%';
		$query["searchParticipants"] = <<<EOD
			SELECT
			    	P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename, CD.regtype, CD.regdepartment
				FROM
					Participants P
				JOIN CongoDump CD ON P.badgeid = CD.badgeid
				WHERE
			           P.pubsname LIKE "$searchString"
					OR CD.lastname LIKE "$searchString"
					OR CD.firstname LIKE "$searchString"
					OR CD.badgename LIKE "$searchString"
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
	}
	//echo("\n<br/>".$query["searchParticipants"]);
	$xml=mysql_query_XML($query);
    if (!$xml) {
        echo $message_error;
        exit();
        }
	$xsl = new DomDocument;
	$xsl->load('xsl/AdminParticipants.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	if ($html = $xslt->transformToXML($xml)) {
			header("Content-Type: text/html"); 
		    echo $html;
			}
		else {
		    trigger_error('XSL transformation failed.', E_USER_ERROR);
			}
	exit();
}
// Start here.  Should be AJAX requests only
if (!$ajax_request_action=$_POST["ajax_request_action"])
 	if (!$ajax_request_action=$_GET["ajax_request_action"]) {
		RenderErrorAjax("no ajax_request_action");
		exit();
		}
//RenderErrorAjax("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action"); exit();
switch ($ajax_request_action) {
	case "fetch_participant":
		//RenderErrorAjax("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action"); exit();
		fetch_participant();
		break;
	case "perform_search":
		perform_search();
		break;
	case "update_participant":
		update_participant();
		break;
	default:
		RenderErrorAjax("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action"); exit();
		exit();
	}

?>
