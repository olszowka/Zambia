<?php
	global $participant,$message_error,$message2,$congoinfo;
	$title="Reports in Category";
	require_once('db_functions.php');
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	require_once('StaffCommonCode.php');
	$reportcategoryid = getInt("reportcategoryid");
	if ($reportcategoryid===false) {
		$message_error = "Required parameter reportcategoryid misssing or invalid.";
		RenderError($title,$message_error);
        exit();
		}
	$query = "SELECT description from ReportCategories where reportcategoryid = $reportcategoryid;";
	$result = mysql_query_with_error_handling($query);
	if ($result===false || mysql_num_rows($result) != 1) {
		$message_error = "reportcategoryid $reportcategoryid not found in db.";
		RenderError($title,$message_error);
        exit();
		}
	$title = mysql_result($result,0);
	$queryArray["reportTypes"]=<<<EOD
SELECT
		RT.reporttypeid, RT.title, RT.description, RT.oldmechanism, RT.ondemand, RT.filename
	FROM
			 ReportTypes RT
		JOIN CategoryHasReport CHR USING (reporttypeid)
	WHERE
		CHR.reportcategoryid = $reportcategoryid
	ORDER BY
		RT.display_order;
EOD;
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
			<xsl:choose>
				<xsl:when test="doc/query[@queryName='reportTypes']/row">
					<xsl:apply-templates match="doc/query[@queryName='reportTypes']/row" />
				</xsl:when>
				<xsl:otherwise>
					<div>No reports found for that category.</div>
				</xsl:otherwise>
			</xsl:choose>
			<div style="margin-top:0.75em; font-style: italic">
				<span>Most reports are now generated when the link is clicked.  Those marked with</span>
				<div style="display: inline-block; border:none" class="ui-state-active">
				    <span class="ui-icon ui-icon-clock"></span>
				</div>
				<span>are generated periodically--about every 15 minutes.</span>
			</div>
		</xsl:template>
		<xsl:template match="/doc/query[@queryName='reportTypes']/row">
			<div>
				<xsl:choose>
					<xsl:when test="@oldmechanism='1'">
						<a href="{@filename}"><xsl:value-of select="@title" /></a>
						<xsl:if test="@ondemand = '0'">
							<div style="display: inline-block; border:none" class="ui-state-active">
							    <span class="ui-icon ui-icon-clock"></span>
							</div>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<a href="runreport.php?reporttypeid={@reporttypeid}"><xsl:value-of select="@title" /></a>
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<div style="margin-left:3em">
				<xsl:value-of select="@description" />
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
