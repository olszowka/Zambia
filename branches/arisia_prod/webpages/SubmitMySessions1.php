<?php
    global $participant,$message_error,$message2,$congoinfo,$session_interests,$session_interest_index, $title;
    $title="Select Sessions";
    require ('PartCommonCode.php'); //define database functions
    require ('PartPanelInterests_FNC.php');
    require ('PartPanelInterests_Render.php');
    $delcount = 0;
    $dellist = "";
	if (!empty($_POST)) {
		foreach ($_POST as $postName => $postValue) {
			if (substr($postName,0,5) != "dirty")
				continue;
			$id = substr($postName,5);
			if (isset($_POST["int".$id]))
					$insarray[]=$id;
				else {
					$dellist .= $id.", ";
					$delcount++;
					}
			}
		}
    if ($delcount > 0) {
		$dellist = substr($dellist, 0, -2); //remove trailing ", "
        $query = "DELETE FROM ParticipantSessionInterest WHERE badgeid=\"$badgeid\" AND sessionid in ($dellist)";
        if (!mysql_query_with_error_handling($query)) {
		    RenderError($title,$message_error);
            exit();
            }
        }
	$inscount = count($insarray);
	if ($inscount > 0) {
		foreach ($insarray as $i => $id) {
			$query="INSERT INTO ParticipantSessionInterest SET badgeid=\"$badgeid\", sessionid = $id";
            if (!mysql_query_with_error_handling($query)) {
			    RenderError($title,$message_error);
                exit();
				}
			}
		}
    $message=""; 
    $error=false;
    if (($delcount==0)&&($inscount==0)) {
        $message="No changes to database requested.";
        }
    if ($delcount>0) {
        $message=$delcount." session(s) removed from interest list.";
        }
    if ($inscount>0) {
        $message.=$inscount." session(s) added to interest list.";
        }
	$messageSave=$message;
	$message="";
// Get the participant's interest data -- use global $session_interests
    $session_interest_count=get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
// Get title, etc. of such data -- use global $session_interests
    get_si_session_info_from_db($session_interest_count); // Will render its own errors
    $message=$messageSave.$message;
    $message_error="";
    render_session_interests($badgid,$session_interest_count,$message,$message_error); // includes footer
?>        
