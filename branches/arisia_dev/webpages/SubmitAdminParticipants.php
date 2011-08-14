<?php
require_once('db_functions.php');
require_once('StaffCommonCode.php');
function update_participant() {
    global $link,$message_error;
    $partid = $_GET["badgeid"];
    $password = $_GET["password"];
    $bio = stripslashes($_GET["bio"]);
    $pubsname = stripslashes($_GET["pname"]);
    $staffnotes = stripslashes($_GET["staffnotes"]);
    $interested = $_GET["interested"];
    $query = "UPDATE Participants SET ";
    if ($password) {
        $query.="password=\"".md5($password)."\", ";
        }
    if ($bio) {
        $query.="bio=\"".mysql_real_escape_string($bio)."\", ";
        }
    if ($pubsname) {
        $query.="pubsname=\"".mysql_real_escape_string($pubsname)."\", ";
        }
    if ($staffnotes) {
        $query.="staff_notes=\"".mysql_real_escape_string($staffnotes)."\", ";
        }
    if ($interested) {
        $query.="interested=".mysql_real_escape_string($interested).", ";
        }
	$query = substr($query,0,-2); //drop two characters at end: ", "
    $query.=" WHERE badgeid=\"".mysql_real_escape_string($partid)."\"";
    if (!mysql_query_with_error_handling($query)) {
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
	$searchString = mysql_real_escape_string(stripslashes($_GET["searchString"]));
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
if (!$ajax_request_action=$_GET["ajax_request_action"])
	exit();
switch ($ajax_request_action) {
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
