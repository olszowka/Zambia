<?php
    global $participant, $message_error, $message2, $congoinfo, $title;
    $title = "Table Tents";
    require_once('StaffCommonCode.php');
    require_once('schedule_functions.php');

$xml = get_scheduled_events_with_participants_as_xml();
$paramArray = array();
if (defined('CON_THEME') && CON_THEME !== "") {
    $paramArray['additionalCss'] = CON_THEME;
}
if (array_key_exists("paper", $_REQUEST)) {
    $paper = $_REQUEST["paper"];
    $paramArray['paper'] = mb_strtolower($paper, "utf-8");
}

RenderXSLT('TableTents.xsl', $paramArray, $xml);
?>
