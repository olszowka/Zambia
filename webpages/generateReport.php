<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
$title = "Run Report";
require_once('StaffCommonCode.php');
$reportName = getString("reportName");
if ($reportName == '') {
    $message_error = "Required parameter reportName misssing or invalid.";
    RenderError($message_error);
    exit();
}
$_SESSION['return_to_page'] = "generateReport.php?reportName=$reportName";
require("reports/$reportName.php");
foreach ($report['queries'] as $queryName => $query) {
    $report['queries'][$queryName] = str_replace('$ConStartDatim$',CON_START_DATIM, $query);
}
if (($resultXML = mysql_query_XML($report['queries'])) === false) {
    RenderError($message_error);
    exit();
}
staff_header($report['name']);
echo "<div class=\"alert alert-info\">" . htmlspecialchars($report['description'], ENT_NOQUOTES) . "</div>\n";
echo "<p class=\"text-success center\"> Generated: " . date("D M j G:i:s T Y") . "</p>\n";
echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
$xsl = new DomDocument;
$xsl->loadXML($report['xsl']);
$xslt = new XsltProcessor();
$xslt->importStylesheet($xsl);
$html = $xslt->transformToXML($resultXML);
// some browsers do not support empty div, iframe, script and textarea tags
echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
staff_footer();
?>
