<?php
	global $participant,$message_error,$message2,$congoinfo;
	$title="Available Reports";
	require_once('db_functions.php');
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	require_once('StaffCommonCode.php');
	$queryArray["categories"] = "SELECT reportcategoryid, description FROM ReportCategories ORDER BY display_order;";
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($title,$message_error);
        exit();
        }
	staff_header($title);
	//echo($resultXML->saveXML()); //for debugging only
	$xmlstr = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
	<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
		<xsl:output omit-xml-declaration="yes" />
		<xsl:template match="/">
			<xsl:apply-templates match="doc/query[@queryName='categories']/row" />
			<div>
				<a href="staffReportsInCategory?reportcategoryid=0">All reports</a>
			</div>
		</xsl:template>
		<xsl:template match="/doc/query[@queryName='categories']/row">
			<div>
				<a href="staffReportsInCategory?reportcategoryid={@reportcategoryid}"><xsl:value-of select="@description" /></a>
			</div>
		</xsl:template>
	</xsl:stylesheet>
EOD;
	$xsl = new DomDocument;
	$xsl->loadXML($xmlstr);
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	// some browsers do not support empty div, iframe, script and textarea tags
	echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
	staff_footer();
?>
