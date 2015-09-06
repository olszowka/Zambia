<?php
	global $participant,$message_error,$message2,$congoinfo;
	$title="Run Report";
	require_once('db_functions.php');
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	require_once('StaffCommonCode.php');
	$reporttypeid = getInt("reporttypeid");
	if ($reporttypeid===false) {
		$message_error = "Required parameter reporttypeid misssing or invalid.";
		RenderError($title,$message_error);
        exit();
		}
	$_SESSION['return_to_page']="runreport.php?reporttypeid=$reporttypeid";
	$query = "SELECT title, description, oldmechanism, xsl from ReportTypes where reporttypeid = $reporttypeid;";
	$result = mysql_query_with_error_handling($query);
	if ($result===false || mysql_num_rows($result) != 1) {
		$message = "Report type $reporttypeid not found in db. ".$message_error;
		RenderError($title,$message);
        exit();
		}
	list($title, $description, $oldmechanism, $xmlstr) = mysql_fetch_array($result, MYSQL_NUM);
	if ($oldmechanism == '1') {
		$message = "Problem with report configuration for $reporttypeid.";
		RenderError($title,$message);
        exit();
		}
	$query = "SELECT queryname, query from ReportQueries where reporttypeid = $reporttypeid;";
	$result = mysql_query_with_error_handling($query);
	if ($result===false || mysql_num_rows($result) == 0) {
		$message = "Problem retrieving queries for report. ".$message_error;
		RenderError($title,$message);
        exit();
		}
	while($row = mysql_fetch_assoc($result)) {
		$queryArray[$row["queryname"]] = str_replace('$ConStartDatim$',CON_START_DATIM, $row["query"]);
		}
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	echo "<div class=\"alert alert-info\">".htmlspecialchars($description,ENT_NOQUOTES)."</div>\n";
	date_default_timezone_set('US/Eastern');
    echo "<p class=\"text-success center\"> Generated: ".date("D M j G:i:s T Y")."</p>\n";
	//echo($resultXML->saveXML()); //for debugging only
	$xsl = new DomDocument;
	$xsl->loadXML($xmlstr);
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	// some browsers do not support empty div, iframe, script and textarea tags
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	staff_footer();
?>
