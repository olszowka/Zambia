<?php
  $title="Admin - Manage Convention";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');

//  staff_header($title);
  
if(!may_I('Administrator')) {
	RenderError($title, $message_error);
	exit();
	}
//handle query stuff
if(isset($_POST) && isset($_POST["action"])) {
	$resultMsg = "<p class=\"regmsg\">called with action:<br/>".print_r($_POST,true)."</p>";
	
	switch($_POST["action"]) {
	case "update_phase":
		if(!isset($_POST["currentPhase"])) {
			$resultMsg ="<p class=\"errmsg\">Error missing current phase.<br/>Database Not Updated</p>";
			break;
			}
		if(!isset($_POST["newPhase"])) {
			$resultMsg ="<p class=\"errmsg\">Error missing new phase.<br/>Database Not Updated</p>";
			break;
			}
		if($_POST["newPhase"] == $_POST["currentPhase"]) {
			$resultMsg ="<p class=\"errmsg\">Error: New Phase is the same as Old Phase.<br/>Database Not Updated</p>";
			break;
			}
			
		$sqlOldPhase = "UPDATE phases SET current=0 WHERE phaseid=".$_POST["currentPhase"].";";
		$sqlNewPhase = "UPDATE phases SET current=1 WHERE phaseid=".$_POST["newPhase"].";";
		
		if(!mysql_query_with_error_handling($sqlOldPhase) || !mysql_query_with_error_handling($sqlNewPhase)) {
			$resultMsg="<p class=\"errmsg\">".$message_error."</p>";
			exit();
			}
		$resultMsg="<p class=\"regmsg\">Phase Updated Successfully</p>";
		break;
		
	case "modify_permissions":
		if(!isset($_POST["submitPerms"])) {
			$resultMsg ="<p class=\"errmsg\">Error: Need to know what change to make.</p>";
			break;
			}
		switch($_POST["submitPerms"]) {
		case "Add Permissions":
			if(!isset($_POST["newPermRole"]) || !isset($_POST["newPhase"]) || !isset($_POST["newPermAtom"])) {
				$resultMsg="<p class=\"errmsg\">Error: Missing new permission values.</p>";
				break;
				}
			$phase = substr($_POST["newPhase"],6);
			$atom = substr($_POST["newPermAtom"],5);
			$role = substr($_POST["newPermRole"],5);
			$sqlAddPerm = "INSERT INTO permissions VALUES (NULL,".$atom.",".$phase.",".$role.",NULL);";
			if(!mysql_query_with_error_handling($sqlAddPerm)) {
				$resultMsg="<p class=\"errmsg\">".$message_error."</p>";
				exit();
				}
			$resultMsg="<p class=\"regmsg\">New Permission Added</p>";
			break;
		case "Delete Permissions":
			$sqlDelPerm="DELETE FROM permissions WHERE ";
			$updateClause="";
			$sqlDelPerm_end=";";
			foreach($_POST as $key => $value) {
				if(substr($key,0,4)=="rem_") {
					$updateClause .= "permissionid=".substr($key,4).", ";
					}
				}
			if(!$updateClause) {
				$resultMsg="<p class=\"errmsg\">Error: Nothing to delete.</p>";
				break;
				}
			if(!mysql_query_with_error_handling($sqlDelPerm.substr($updateClause,0,-2).$sqlDelPerm_end)) {
				$resultMsg="<p class=\"errmsg\">".$message_error."</p>";
				break;
				}
			$resultMsg="<p class=\"regmsg\">Permission Removed</p>";
			break;
		default:
			$resultMsg ="<p class=\"errmsg\">Error: Unknown Permission Change.</p>";
		}
		break;

	default:
		$resultMsg ="<p class=\"errmsg\">Called with unknown action: ".$_POST["action"]."</p>";
	}
	
	} else {
	$resultMsg = "<span class=\"beforeResult\" id=\"resultBoxSPAN\">Result messages will appear here.</span>";
	}
	
//done with changes stuff	
$query["rooms"] = <<<EOD
SELECT roomid, roomname, function, floor, notes
FROM rooms 
WHERE 1
EOD;

$query["phases"] = <<<EOD
SELECT phaseid, phasename, current, notes
FROM phases
WHERE 1
EOD;

$query["permissionroles"] = <<<EOD
SELECT permroleid, permrolename, notes
FROM permissionroles
WHERE 1
EOD;

$query["permissionatoms"] = <<<EOD
SELECT permatomid, permatomtag, notes
FROM permissionatoms
WHERE 1
EOD;

$query["permissions"] = <<<EOD
SELECT P.permissionid, P.phaseid, P.permroleid, P.permatomid, P.badgeid, PR.permrolename, PA.permatomtag, PH.phasename
FROM permissions P
JOIN permissionatoms PA ON P.permatomid = PA.permatomid
JOIN permissionroles PR ON P.permroleid = PR.permroleid
JOIN phases PH ON P.phaseid = PH.phaseid
EOD;

$query["currentPhase"] = "SELECT phaseid FROM phases WHERE current=1";

$resultXML=mysql_query_XML($query);
if (!$resultXML) {
    RenderError($title, $message_error);
    exit();
    }
if(!$result = mysql_query_with_error_handling("SELECT phaseid FROM phases WHERE current=1")) {
    RenderError($title, $message_error);
    exit();
	}
$currentPhaseRow = mysql_fetch_object($result);
$currentPhase = $currentPhaseRow->phaseid;
	
  staff_header($title);
//  echo($resultXML->saveXML()); // for debuging only
  
	$optionsNode = $resultXML->createElement("options");
	$docNode = $resultXML->getElementsByTagName("doc")->item(0);
	$optionsNode = $docNode->appendChild($optionsNode);
	
	$optionsNode->setAttribute("enableAdministration",may_I('Admin'));
	$optionsNode->setAttribute("conName", CON_NAME);
	$optionsNode->setAttribute("currentPhase", $currentPhase);
	$optionsNode->setAttribute("resultMsg", $resultMsg);

	$xsl = new DomDocument;
	$xsl->load('xsl/AdminConvention.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));

?>
	

<?php staff_footer(); ?>