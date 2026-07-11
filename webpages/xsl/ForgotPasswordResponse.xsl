<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-19;
     Copyright (c) 2020-2024 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="subject_line" select="''" />
    <xsl:param name="from_address" select="''" />
    <xsl:template match="/">
        <div class="container-xxxl">
            <div class="row">
                <div class="col-36 mt-4">
                    <h3 class="mx-auto" style="width:23.5rem">Reset Password Confirmation</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-36 col-md-18 mt-4">
                    <div class="alert alert-success">Email sent</div>
                </div>
            </div>
            <div class="row">
                <div class="col-36 col-md-18 mt-4">
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
