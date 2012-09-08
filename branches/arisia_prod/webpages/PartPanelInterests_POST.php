<?php
    //This file should be requested from post on "add" form
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
    if (!isset($_POST["add"])) { //That should be "submit" button on "add" form.
        $message="This page was reached from an unexpected place.<BR>\n";
        RenderError("Page Flow Error",$message);
        exit();
        }
    if (!isset($_POST["addsessionid"])) { //That should be "sessionid" box on "add" form.
        $message="Sessionid value not found.<BR>\n";
        RenderError($title,$message);
        exit();
        }
    $addsessionid=$_POST["addsessionid"];
    if (!validate_add_session_interest($addsessionid,$badgeid,ParticipantAddSession)) {
            $error=true;
            $message_error=$message;
            $message="";
            }
        else {
            $query="INSERT INTO ParticipantSessionInterest set badgeid=\"$badgeid\", sessionid=$addsessionid";
            if (!$result=mysql_query($query,$link)) {
                $message=$query."<BR>Error inserting into database.<BR>";
                RenderError($title,$message);
                exit();
                }
            $message="Database updated successfully.";
            $message_error="";
            }
    // $add
    // Get the participant's interest data -- use global $session_interests
    $session_interest_count=get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
    // Get title, etc. of such data -- use global $session_interests
    get_si_session_info_from_db($session_interest_count); // Will render its own errors 
    render_session_interests($badgid,$session_interest_count,$message,$message_error); // includes footer
?>
