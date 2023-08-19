<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2023-07-01;
	Copyright (c) 2023 Peter Olszowka. All rights reserved.
	See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="edit_participant_tags" select="false" />
    <xsl:output encoding="UTF-8" indent="yes" method="html"/>
    <xsl:template match="/doc/query[@queryName='participant_tags']/row">
        <div>
            <xsl:choose>
                <xsl:when test="not($edit_participant_tags)">
                    <xsl:attribute name="class">checkbox-list-label-wrapper disabled</xsl:attribute>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="class">checkbox-list-label-wrapper</xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
            <label class="checkbox-list-label">
                <input type="checkbox" name="participant_has_tag[]" id="tag_{@participanttagid}" class="checkbox-list-check mycontrol tag-check" value="{@participanttagid}" >
                    <xsl:if test="@badgeid">
                        <xsl:attribute name="checked" >checked</xsl:attribute>
                    </xsl:if>
                    <xsl:if test="not($edit_participant_tags)">
                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                    </xsl:if>
                </input>
                <xsl:value-of select="@participanttagname" />
            </label>
        </div>
    </xsl:template>
</xsl:stylesheet>
