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
            <th class="report">Badgeid</th>
            <th class="report">Name</th>
            <th class="report">Permission roles</th>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='users']/row"/>
    </table>
</xsl:template>
<xsl:template match="/doc/query[@queryName='users']/row">
    <xsl:variable name="badgeid" select="@badgeid"/>
    <tr>
        <td class="report"><xsl:value-of select="@badgeid"/></td>
        <td class="report"><xsl:value-of select="@name"/></td>
        <td class="report">
            <xsl:for-each select="/doc/query[@queryName = 'user_roles']/row[@badgeid = $badgeid]">
                <div><xsl:value-of select="@permrolename"/></div>
            </xsl:for-each>
        </td>
    </tr>
</xsl:template>
</xsl:stylesheet>
