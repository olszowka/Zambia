<?php
    //This file should be requested from post on "session interests(ranks)" form
    require ('PartCommonCode.php'); // initialize db; check login; set $badgeid
    require ('PartPanelInterests_FNC.php');
    require ('PartPanelInterests_Render.php');
    global $session_interests,$session_interest_index, $title, $message;
    $title="Panel Interests";
    $error=false;
    if (!may_I('my_panel_interests')) {
        $message="You do not currently have permission to view this page.<BR>\n";
        RenderError("Permission Error",$message);
        exit();
        }
    if (!isset($_POST["submitranks"])) { //That should be "save" button on "session interests" form.
        $message="This page was reached from an unexpected place.<BR>\n";
        RenderError("Page Flow Error",$message);
        exit();
        }
    $session_interest_count=get_session_interests_from_post();
    if (validate_session_interests($session_interest_count)===false) {
            $error=true;
            $message_error=$message;
            $message="";
            }
        else {
			update_session_interests_in_db($badgeid,$session_interest_count);
            $message_error="";
			//$message="Database updated.<BR>\n";
    		$session_interest_count=get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
            }
    // Get title, etc. of such data -- use global $session_interests
    //echo "<P>Got here.</P>";
	//print_r($session_interest_index);
    get_si_session_info_from_db($session_interest_count); // Will render its own errors 
    //print_r($session_interests);
    render_session_interests($badgid,$session_interest_count,$message,$message_error); // includes footer
?>
