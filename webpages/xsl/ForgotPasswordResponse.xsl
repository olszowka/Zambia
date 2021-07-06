<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-19;
     Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="subject_line" select="''" />
    <xsl:param name="from_address" select="''" />
    <xsl:template match="/">
        <div class="container">
            <p class="alert alert-success mt-2">Email sent</p>
            <div class="card mt-2">
                <div class="card-header">
                    <h2>Email Sent</h2>
                </div>
                <div class="card-body">
                    <xsl:text>Look for an email with subject "</xsl:text>
                    <xsl:value-of select="$subject_line" />
                    <xsl:text>" sent from </xsl:text>
                    <xsl:value-of select="$from_address" />
                    <xsl:text> for a link to reset your password. If you don't see it in a few minutes, remember to check your junk or spam folder.</xsl:text>
                </div>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>