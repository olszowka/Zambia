<?php
    global $participant, $message_error, $message2, $congoinfo, $title;
    $title = "Table Tents";
    require_once('StaffCommonCode.php');
    require_once('schedule_functions.php');

$xml = get_scheduled_events_with_participants_as_xml();
RenderXSLT('TableTents.xsl', [], $xml);
?>
