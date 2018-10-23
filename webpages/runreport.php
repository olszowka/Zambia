<?php
// Copyright (c) 2015-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Run Report";
require_once('StaffCommonCode.php');
$reporttypeid = getInt("reporttypeid");
if ($reporttypeid === false) {
    $message_error = "Required parameter reporttypeid misssing or invalid.";
    RenderError($message_error);
    exit();
}
$_SESSION['return_to_page'] = "runreport.php?reporttypeid=$reporttypeid";
$query = "SELECT title, description, oldmechanism, xsl from ReportTypes where reporttypeid = $reporttypeid;";
$result = mysqli_query_exit_on_error($query);
if (mysqli_num_rows($result) != 1) {
    $message = "Report type $reporttypeid not found in db. ";
    RenderError($message);
    exit();
}
list($title, $description, $oldmechanism, $xmlstr) = mysqli_fetch_array($result, MYSQLI_NUM);
if ($oldmechanism == '1') {
    $message = "Problem with report configuration for $reporttypeid.";
    RenderError($message);
    exit();
}
mysqli_free_result($result);
$query = "SELECT queryname, query from ReportQueries where reporttypeid = $reporttypeid;";
$result = mysqli_query_exit_on_error($query);
if (mysqli_num_rows($result) == 0) {
    $message = "Problem retrieving queries for report. ";
    RenderError($message);
    exit();
}
while ($row = mysqli_fetch_assoc($result)) {
    $queryArray[$row["queryname"]] = str_replace('$ConStartDatim$', CON_START_DATIM, $row["query"]);
}
mysqli_free_result($result);
if (($resultXML = mysql_query_XML($queryArray)) === false) {
    RenderError($message_error);
    exit();
}
staff_header($title, true);
echo "<div class=\"alert alert-info\">" . htmlspecialchars($description, ENT_NOQUOTES) . "</div>\n";
date_default_timezone_set('US/Eastern');
echo "<p class=\"text-success center\"> Generated: " . date("D M j G:i:s T Y") . "</p>\n";
//echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
$xsl = new DomDocument;
$xsl->loadXML($xmlstr);
$xslt = new XsltProcessor();
$xslt->importStylesheet($xsl);
$html = $xslt->transformToXML($resultXML);
// some browsers do not support empty div, iframe, script and textarea tags
echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
echo "<div class=\"clearfix\"></div>\n";
staff_footer();
?>
