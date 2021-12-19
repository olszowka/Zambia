<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="additionalCss" select="''" />
    <xsl:param name="paper" select="'letter'" />
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:template match="/">
        <html>
            <head>
                <link rel="stylesheet" href="css/zambia_print.css" type="text/css" />
                <xsl:if test="not($additionalCss = '')">
                    <link rel="stylesheet" type="text/css">
                        <xsl:attribute name="href"> 
                            <xsl:value-of select="$additionalCss" />
                        </xsl:attribute>
                    </link>
                </xsl:if>
                <title>Table Tents</title>
            </head>
            <body class="table-tent-body">
            <xsl:apply-templates select="doc/session"/>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="doc/session">
        <section>
            <xsl:attribute name="class">
                <xsl:value-of select="'table-tent paper-'" /> 
                <xsl:value-of select="$paper" />
            </xsl:attribute>
            <div class="page">
                <div class="front"><h1 class="title"><xsl:value-of select="@title" disable-output-escaping="yes"/></h1></div>
                <div class="back"><h1 class="title"><xsl:value-of select="@title" disable-output-escaping="yes"/></h1></div>
            </div>
            <xsl:for-each select="participant">
                <div class="page">
                    <div class="front">
                        <div>
                            <h1 class="panelist"><xsl:value-of select="@pubsname" /></h1>
                        </div>
                    </div>
                    <div class="back">
                        <div>
                            <p class="panelname">This is Session: &quot;<span><xsl:value-of select="../@title" disable-output-escaping="yes"/></span>&quot;</p>
                            <p><xsl:value-of select="../@roomname" /> &#8226; <xsl:value-of select="../@starttime" /> &#8226; <xsl:value-of select="../@trackname" /></p>
                            <p class="panelists">
                                <xsl:for-each select="../participant">
                                    <xsl:if test="@moderator = '1'"><b>M: </b></xsl:if>
                                    <xsl:value-of select="@pubsname" />
                                    <xsl:if test="not(position() = last())">, </xsl:if>
                                </xsl:for-each>
                            </p>
                            <p class="paneldescribe"><xsl:value-of select="../@progguiddesc" disable-output-escaping="yes"/></p>
                            <xsl:choose>
                            <xsl:when test="./nextSession[@title != '']">
                                <p class="lastnote">Your next session is &quot;<xsl:value-of select="./nextSession/@title"  disable-output-escaping="yes"/>&quot; on <xsl:value-of select="./nextSession/@starttime" /></p>
                            </xsl:when>
                            <xsl:otherwise>
                                <p class="lastnote">This is your last scheduled session.</p>
                            </xsl:otherwise>
                            </xsl:choose>
                        </div>
                    </div>
                </div>
            </xsl:for-each>
        </section>
    </xsl:template>
</xsl:stylesheet>