<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('error_functions.php');
require_once('Constants.php');
require_once('data_functions.php');
require_once('db_functions.php');
require_once('render_functions.php');
require_once('validation_functions.php');
require_once('HtmlHeader.php');
require_once('CommonHeader.php');
if (!isset($title)) {
    $title = "";
}
session_start();
// inclusion of configuration file db_name.php occurs here
if (!prepare_db_and_more()) {
    $message_error = "Unable to connect to database.<br>No further execution possible.";
    RenderError($message_error);
    exit();
};
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}

// function to generate a clickable tab.
// 'text' contains the text that should appear in the tab.
// 'usable' indicates whether the tab is usable.
//
// used by old (non-bootstrap menuing system)currently just brainstorm pages
function maketab($text, $usable, $url) {
    if ($usable) {
        echo '<span class="menutab enabled">';
        echo '<img class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
        echo "<a href='$url'>";// XXX link needs to be quoted
        echo $text;                     // XXX needs to be quoted
        echo '<img class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
        echo '</span>';
    } else {
        echo '<span class="menutab disabled">';
        echo '<img class="tabborder" src="images/leftCorner.gif" alt="&nbsp;">';
        echo $text;                     // XXX needs to be quoted
        echo '<img class="tabborder" src="images/rightCorner.gif" alt="&nbsp;">';
        echo '</span>';
    }
}

// used by new (bootstrap) menuing system
function makeMenuItem($text, $usable, $url, $sep = false) {
    //plain menu item looks like
    //<li><a href="StaffAssignParticipants.php">Assign to a Session</a></li>
    if ($usable) {

        if ($sep) {
            echo "<li class=\"divider-vertical\"></li>";
        }
        echo "<li><a href=\"$url\">$text</a></li>";
    }
}

?>
