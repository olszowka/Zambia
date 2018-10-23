<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $session_interests, $session_interest_index, $title;
$title = "Select Sessions";
require('PartCommonCode.php'); //define database functions
require('PartPanelInterests_FNC.php');
require('PartPanelInterests_Render.php');
$delcount = 0;
$dellist = "";
if (!empty($_POST)) {
    foreach ($_POST as $postName => $postValue) {
        if (substr($postName, 0, 5) != "dirty")
            continue;
        $id = substr($postName, 5);
        if (isset($_POST["int" . $id]))
            $insarray[] = $id;
        else {
            $dellist .= $id . ", ";
            $delcount++;
        }
    }
}
if ($delcount > 0) {
    $dellist = substr($dellist, 0, -2); //remove trailing ", "
    $query = "DELETE FROM ParticipantSessionInterest WHERE badgeid=\"$badgeid\" AND sessionid in ($dellist)";
    mysqli_query_exit_on_error($query);
}
$inscount = count($insarray);
if ($inscount > 0) {
    foreach ($insarray as $i => $id) {
        $query = "INSERT INTO ParticipantSessionInterest SET badgeid=\"$badgeid\", sessionid = $id";
        mysqli_query_exit_on_error($query);
    }
}
$message = "";
$error = false;
if (($delcount == 0) && ($inscount == 0)) {
    $message = "No changes to database requested.";
}
if ($delcount > 0) {
    $message = $delcount . " session(s) removed from interest list.";
}
if ($inscount > 0) {
    $message .= $inscount . " session(s) added to interest list.";
}
$messageSave = $message;
$message = "";
// Get the participant's interest data -- use global $session_interests
$session_interest_count = get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
// Get title, etc. of such data -- use global $session_interests
get_si_session_info_from_db($session_interest_count); // Will render its own errors
$message = $messageSave . $message;
render_session_interests($session_interest_count, $message, "", false, false); // includes footer
?>        
