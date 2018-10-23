<?php
//	Copyright (c) 2011-2017 Peter Olszowka. All rights reserved. See copyright document for more details.

function StaffRenderErrorPage($title, $message) {
    global $debug;
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);
    if (isset($debug)) {
        echo $debug . "<br>\n";
    }
    echo "<p class=\"alert alert-error\">" . $message . "</p>\n";
    staff_footer();
}

function PartRenderErrorPage($title, $message) {
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    participant_header($title);
    echo "<p class=\"alert alert-error\">" . $message . "</p>\n";
    participant_footer();
}

function BrainstormRenderErrorPage($title, $message) {
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    brainstorm_header($title);
    echo "<p class=\"alert alert-error\">" . $message . "</p>\n";
    brainstorm_footer();
}

function RenderError($message_error, $ajax = false) {
    global $header_used, $title;
    if ($ajax) {
        RenderErrorAjax($message_error);
        exit(0);
    }
    if (!empty($header_used)) {
        echo "<p class=\"alert alert-error\">$message_error</p>\n";
        switch ($header_used) {
            case HEADER_BRAINSTORM:
                brainstorm_footer();
                break;
            case HEADER_PARTICIPANT:
                participant_footer();
                break;
            case HEADER_STAFF:
                staff_footer();
                break;
        }
        exit(0);
    }
    if (empty($title)) {
        $title = "";
    }
    if (isset($_SESSION['role']) && $_SESSION['role'] === "Brainstorm") {
        BrainstormRenderErrorPage($title, $message_error);
        exit(0);
    }
    if (isset($_SESSION['role']) && $_SESSION['role'] === "Participant") {
        PartRenderErrorPage($title, $message_error);
        exit(0);
    }
    if (isset($_SESSION['role']) && $_SESSION['role'] === "Staff") {
        StaffRenderErrorPage($title, $message_error);
        exit(0);
    }
    // else
    // do something generic here (though this might be way too generic)
    // better to output some error message reliably than none at all
    echo "<html>\n";
    echo "<head>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n";
    echo "<title>Zambia - $title</title>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<h1>Zambia&ndash;The" . CON_NAME . "Scheduling Tool</h1>\n";
    echo "<hr>\n";
    echo "<p> An error occurred: </p>\n";
    echo "<p>$message_error</p>\n";
    echo "</body>\n";
    echo "</html>\n";
}

function RenderErrorAjax($message_error) {
    echo "<span class=\"alert\">$message_error</span>\n";
}

?>
