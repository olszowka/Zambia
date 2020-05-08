<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-19;
     Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="subject_line" select="''" />
    <xsl:param name="from_address" select="''" />
    <xsl:template match="/">
        <p class="alert alert-success vert-sep-above">Email sent</p>
        <p class="vert-sep-above">
            <xsl:text>Look for an email with subject "</xsl:text>
            <xsl:value-of select="$subject_line" />
            <xsl:text>" sent from </xsl:text>
            <xsl:value-of select="$from_address" />
            <xsl:text> for a link to reset your password. If you don't see it in a few minutes, remember to check your junk or spam folder.</xsl:text>
        </p>
    </xsl:template>
</xsl:stylesheet>