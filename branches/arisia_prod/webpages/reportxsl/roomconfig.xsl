<?xml version="1.0" encoding="UTF-8" ?>
<!--
	allprivsreport
	Created by Peter Olszowka on 2011-07-24.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <table class="report">
        <tr>
            <th rowspan = "2" class="report" style="">Room Id</th>
            <th class="report" style="">Room Name</th>
            <th class="report" style="">Height</th>
            <th class="report" style="">Dimensions</th>
            <th class="report" style="">Area</th>
            <th class="report" style="">Function</th>
            <th class="report" style="">Floor</th>
            <th class="report" style="">Notes</th>
            <th class="report" style="" title="rooms.is_scheduled">To be scheduled*</th>
            <th class="report" style="">Has been scheduled</th>
        </tr>
        <tr>
            <th class="report" colspan = "4" style="">Times</th>
            <th class="report" colspan = "5" style="">Room Sets</th>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='rooms']/row" />
    </table>
</xsl:template>
<xsl:template match="/doc/query[@queryName='rooms']/row">
    <xsl:variable name="roomid" select="@roomid" />
    <tr>
        <td rowspan="2" class="report"><xsl:value-of select="@roomid"/></td>
        <td class="report"><xsl:value-of select="@roomname"/></td>
        <td class="report"><xsl:value-of select="@height"/></td>
        <td class="report"><xsl:value-of select="@dimensions"/></td>
        <td class="report"><xsl:value-of select="@area"/></td>
        <td class="report"><xsl:value-of select="@function"/></td>
        <td class="report"><xsl:value-of select="@floor"/></td>
        <td class="report"><xsl:value-of select="@notes"/></td>
        <td class="report"><xsl:value-of select="@is_scheduled"/></td>
        <td class="report"><xsl:value-of select="@scheduled"/></td>
    </tr>
    <tr>
        <td colspan="4" class="report">
            <xsl:choose>
                <xsl:when test="@opentime1 or @closetime1">
                    <xsl:value-of select="@opentime1"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime1"/><br />
                </xsl:when>
                <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
            </xsl:choose>
            <xsl:choose>
                <xsl:when test="@opentime2 or @closetime2">
                    <xsl:value-of select="@opentime2"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime2"/><br />
                </xsl:when>
                <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
            </xsl:choose>
            <xsl:choose>
                <xsl:when test="@opentime3 or @closetime3">
                    <xsl:value-of select="@opentime3"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime3"/><br />
                </xsl:when>
                <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
            </xsl:choose>
        </td>
        <td colspan="5" class="report">
            <xsl:apply-templates select="/doc/query[@queryName='roomsets']/row[@roomid=$roomid]" /><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
        </td>
    </tr>
</xsl:template>
<xsl:template match="/doc/query[@queryName='roomsets']/row">
    <xsl:value-of select="@roomsetname"/><xsl:text> : </xsl:text><xsl:value-of select="@capacity"/><br />
</xsl:template>
</xsl:stylesheet>
