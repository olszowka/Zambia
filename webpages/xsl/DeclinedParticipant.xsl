<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2026-06-06;
    Copyright (c) 2026 Peter Olszowka. All rights reserved.
    See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="conName" select="''"/>
    <xsl:param name="programEmail" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <xsl:variable name="declined_particpant" select="/doc/customText/@declined_particpant" />
        <div class="container mt-2">
            <xsl:choose>
                <xsl:when test="string-length($declined_particpant) > 0">
                    <xsl:value-of select="$declined_particpant" disable-output-escaping="yes"/>
                </xsl:when>
                <xsl:otherwise>
                    <h3 class="mb-2">
                        <xsl:text>Thank you so much for contacting </xsl:text>
                        <xsl:value-of select="$conName"/>
                        <xsl:text>.</xsl:text>
                    </h3>
                    <p>If you are receiving this message, your record in the Zambia system has been closed. A closed record indicates one or more of three things:</p>
                    <ol>
                        <li>
                            <xsl:text>You contacted </xsl:text>
                            <xsl:value-of select="$conName"/>
                            <xsl:text> to let us know that you are unable to participate in the program this year.</xsl:text>
                        </li>
                        <li>You did not meet a deadline to contact us or provide required information.</li>
                        <li>
                            <xsl:text>You were not selected to be on the </xsl:text>
                            <xsl:value-of select="$conName"/>
                            <xsl:text> program. We received far more requests to be on program from qualified and amazing people than it is possible to accommodate.</xsl:text>
                        </li>
                    </ol>
                    <p>If you have any questions or if you believe that an error has been made, please contact us at <a href="mailto:$programEmail"><xsl:value-of select="$programEmail"/></a></p>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </xsl:template>
</xsl:stylesheet>
