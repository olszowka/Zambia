<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2011-01-02;
	Copyright (c) 2011-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <select name="selroom" id="selroom">
            <option value="0" selected="selected">Select Room</option>
            <xsl:apply-templates select="/doc/query[@queryName='rooms']/row" />
        </select>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='rooms']/row">
        <option value="{@roomid}" data-is-scheduled="{@is_scheduled}">
            <xsl:value-of select="@roomname" />
            <xsl:if test="@function">
                <xsl:text> (</xsl:text><xsl:value-of select="@function" /><xsl:text>)</xsl:text>
            </xsl:if>
        </option>
    </xsl:template>
</xsl:stylesheet>
