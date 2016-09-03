<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php');

// gets data for a participant to be displayed.  Returns as XML
function fetch_participant() {
	//error_log("Reached fetch_participant.");
	$fbadgeid = getInt("badgeid");
	if (!$fbadgeid)
		exit();
	$query["fetchParticipants"] = <<<EOD
SELECT
        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
    FROM
			 Participants P
		JOIN CongoDump CD ON P.badgeid = CD.badgeid
    WHERE
        P.badgeid = "$fbadgeid"
    ORDER BY
        CD.lastname, CD.firstname
EOD;
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
    $partid = mysql_real_escape_string($_POST["badgeid"], $link);
    $password = $_POST["password"];
	$biodirty = isset($_POST["bio"]);
    $bio = stripslashes($_POST["bio"]);
	$pubsnamedirty = isset($_POST["pname"]);
    $pubsname = stripslashes($_POST["pname"]);
	$staffnotesdirty = isset($_POST["staffnotes"]);
    $staffnotes = stripslashes($_POST["staffnotes"]);
    $interested = $_POST["interested"];
    $query = "UPDATE Participants SET ";
    if ($password) {
        $query.="password=\"".md5($password)."\", ";
        }
    if ($biodirty) {
        $query.="bio=\"".mysql_real_escape_string($bio, $link)."\", ";
        }
    if ($pubsnamedirty) {
        $query.="pubsname=\"".mysql_real_escape_string($pubsname, $link)."\", ";
        }
    if ($staffnotesdirty) {
        $query.="staff_notes=\"".mysql_real_escape_string($staffnotes, $link)."\", ";
        }
    if ($interested) {
        $query.="interested=".mysql_real_escape_string($interested, $link).", ";
        }
	$query = mb_substr($query,0,-2); //drop two characters at end: ", "
    $query.=" WHERE badgeid=\"$partid\"";
    if (!mysql_query_with_error_handling($query)) {
        echo "<p class=\"alert alert-error\">".$message_error."</p>";
        return;
        }
    $message="<p class=\"alert alert-success\">Database updated successfully.</p>";
    if ($interested==2) {
		$query=<<<EOD
UPDATE ParticipantOnSessionHistory
    SET inactivatedts = NOW(), inactivatedbybadgeid = "{$_SESSION['badgeid']}"
	WHERE
	        badgeid = "$partid"
		AND inactivatedts IS NULL;
EOD;
	    if (!mysql_query_with_error_handling($query)) {
	        echo "<p class=\"alert alert-error\">".$message_error."</p>";
	        return;
	        }
        $message.="<p class=\"alert alert-info\">Participant removed from ".mysql_affected_rows($link)." session(s).</p>";
        }
    echo $message;
    }

function perform_search() {
	global $link;
	$searchString = mysql_real_escape_string(stripslashes($_POST["searchString"]), $link);
	if ($searchString=="")
		exit();
	if (is_numeric($searchString)) {
			$query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
			    FROM
						 Participants P
					JOIN CongoDump CD ON P.badgeid = CD.badgeid
			    WHERE
			        P.badgeid = "$searchString"
			    ORDER BY
			        CD.lastname, CD.firstname
EOD;
			}
		else {
			$searchString='%'.$searchString.'%';
			$query["searchParticipants"] = <<<EOD
			SELECT
			        P.badgeid, P.pubsname, P.interested, P.bio, P.staff_notes, CD.firstname, CD.lastname, CD.badgename
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
 	if (!$ajax_request_action=$_GET["ajax_request_action"])
		exit();
//error_log("Reached SubmitAdminParticpants. ajax_request_action: $ajax_request_action");
switch ($ajax_request_action) {
	case "fetch_participant":
		fetch_participant();
		break;
	case "perform_search":
		perform_search();
		break;
	case "update_participant":
		update_participant();
		break;
	default:
		exit();
	}

?>
