<?xml version="1.0" encoding="UTF-8" ?>
<!--
	profqualsreport
	Created by Peter Olszowka on 2011-10-12.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <table class="report">
        <tr>
            <th class="report">Last name, first name</th>
            <th class="report">Name for Publications</th>
            <th class="report">Email address</th>
            <th class="report">Badge Id</th>
            <th class="report">Professional Qualifications</th>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='participants']/row"/>
    </table>
</xsl:template>
<xsl:template match="/doc/query[@queryName='participants']/row">
    <xsl:variable name="badgeid" select="@badgeid"/>
    <tr>
        <td class="report"><xsl:value-of select="@name"/></td>
        <td class="report"><xsl:value-of select="@pubsname"/></td>
        <td class="report"><xsl:value-of select="@email"/></td>
        <td class="report"><xsl:value-of select="@badgeid"/></td>
        <td class="report">
            <xsl:for-each select="/doc/query[@queryName = 'credentials']/row[@badgeid = $badgeid]">
                <div><xsl:value-of select="@credentialname"/></div>
            </xsl:for-each>
        </td>
    </tr>
</xsl:template>
</xsl:stylesheet>
