<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $session_interests, $session_interest_index, $title;
$title = "Select Sessions";
require('PartCommonCode.php'); //define database functions
require('PartPanelInterests_FNC.php');
require('PartPanelInterests_Render.php');
$addInterest = getArrayOfInts('addInterest');
if ($addInterest !== false) {
    $inscount = count($addInterest);
    foreach ($addInterest as $id) {
        $query = "INSERT INTO ParticipantSessionInterest SET badgeid=\"$badgeid\", sessionid = $id";
        mysqli_query_exit_on_error($query);
    }
} else
    $inscount = 0;

$deleteInterest = getArrayOfInts('deleteInterest');
if ($deleteInterest !== false) {
    $delcount = count($deleteInterest);
    $dellist = implode(',', $deleteInterest);
    $query = "DELETE FROM ParticipantSessionInterest WHERE badgeid=\"$badgeid\" AND sessionid in ($dellist)";
    mysqli_query_exit_on_error($query);
} else
    $delcount = 0;

$messageSave = "";
if (($delcount == 0) && ($inscount == 0)) {
    $messageSave = "No changes to database requested.";
}
if ($delcount > 0) {
    $messageSave = $delcount . " session(s) removed from interest list. ";
}
if ($inscount > 0) {
    $messageSave .= $inscount . " session(s) added to interest list.";
}
$message = "";
// Get the participant's interest data -- use global $session_interests
$session_interest_count = get_session_interests_from_db($badgeid); // Returns count; Will render its own errors
// Get title, etc. of such data -- use global $session_interests
get_si_session_info_from_db($session_interest_count); // Will render its own errors
$message = $messageSave . $message;
render_session_interests($session_interest_count, $message, "", false, false); // includes footer
?>        
