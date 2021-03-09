<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2021-01-04;
	Copyright (c) 2021 Peter Olszowka. All rights reserved.
	See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/doc/query[@queryName='permroles']/row">
        <div>
            <xsl:choose>
                <xsl:when test="@mayedit != '1'">
                    <xsl:attribute name="class">tag-chk-label-wrapper disabled</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="class">tag-chk-label-wrapper</xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <label class="tag-chk-label">
                <input type="checkbox" name="permroles[]" id="role_{@permroleid}" class="tag-chk mycontrol" value="{@permroleid}" >
                    <xsl:if test="@badgeid">
                        <xsl:attribute name="checked" >checked</xsl:attribute>
                    </xsl:if>
                    <xsl:if test="@mayedit != '1'">
                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                    </xsl:if>
                </input>
                <xsl:value-of select="@permrolename" />
            </label>
        </div>
    </xsl:template>
</xsl:stylesheet>
