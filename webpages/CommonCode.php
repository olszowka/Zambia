<?php
//	Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
require_once('error_functions.php');
require_once('Constants.php');
require_once('data_functions.php');
require_once('db_functions.php');
require_once('render_functions.php');
require_once('validation_functions.php');
// require_once('php_functions.php'); For setting session timeout which doesn't seem to work
//set_session_timeout();
if (!isset($title)) {
    $title = "";
}
session_start();
date_default_timezone_set(defined("PHP_DEFAULT_TIMEZONE") ? PHP_DEFAULT_TIMEZONE : "AMERICA/NEW_YORK");
if (prepare_db() === false) {
    $message_error = "Unable to connect to database.<br>No further execution possible.";
    RenderError($message_error);
    exit();
};
if (isLoggedIn() === false and !isset($logging_in)) {
    $message = "Session expired. Please log in again.";
    if (isset($_GET["ajax_request_action"]) || isset($_POST["ajax_request_action"])) {
        RenderErrorAjax("Session expired. Please <a href=\"index.php\">log in</a> again.");
        exit();
    }
    require('login.php');
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
// if the tab is usable, its background and foreground color will
// be determined by the 'usabletab' class.  when the mouse is over the tab
// the background and foreground colors of the tab will be determined
// by the 'mousedovertab' class.
//
// if the tab is not usable, the tab will use class 'unusabletab'

// used by old (non-bootstrap menuing system)currently just brainstorm pages
function maketab($text, $usable, $url) {
    if ($usable) {
        echo '<span class="usabletab" onmouseover="mouseovertab(this)" onmouseout="mouseouttab(this)">';
        echo '<img class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
        echo '<a href="$url">';// XXX link needs to be quoted
        echo $text;                     // XXX needs to be quoted
        echo '<img class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
        echo '</span>';
    } else {
        echo '<span class="unusabletab">';
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
