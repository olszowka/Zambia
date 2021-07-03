<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:template match="/">
        <html>
            <head>
                <style type='text/css'>
                .panelname { font-weight: bold; font-size: 18pt } 
                .paneldescribe { font-size: 14pt; margin-top: 10pt; } 
                .panelists { font-size: 12pt; font-style: italic } 
                .panelists b { font-size: 12pt; font-style: normal } 
                .lastnote { font-size: 10pt } 
                .hashfront { height:2.5in; x-width:8in; display:block; line-height:72pt;
                    text-align:center; margin-bottom:1.5in; font-size: 52pt; font-weight: bold;
                    -webkit-transform: rotate(-180deg);
                    -moz-transform: rotate(-180deg); }
                    .hashback { height:2.5in; x-width:8in; font-size: 52pt; font-weight: bold; display:block; line-height:72pt;
                    text-align:center; margin-bottom:1.5in; }
                .backside { height:2.5in; x-width:8in; }
                p { margin-top: 2px; margin-bottom: 2px; }
                body { width: 9.5in; margin-left:auto; font-family:"Futura Condensed",sans-serif;
                    margin-right:auto; }
                h1 { display:block; line-height:72pt; font-size: 80pt;
                    text-align:center; height:2.25in; margin-bottom:2in;
                    -webkit-transform: rotate(-180deg); 
                    -moz-transform: rotate(-180deg); 
                }
                .page {
                    page-break-after: always; 
                }
                </style> 
            </head>
            <body>
            <xsl:apply-templates select="doc/session"/>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="doc/session">
        <section>
            <div class="page">
                <div class="hashfront"><xsl:value-of select="@title" disable-output-escaping="yes"/></div>
                <div class="hashback"><xsl:value-of select="@title" disable-output-escaping="yes"/></div>
            </div>
            <xsl:for-each select="participant">
                <div class="page">
                    <h1 class="panelist"><xsl:value-of select="@pubsname" /></h1>
                    <div class="backside">
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
            </xsl:for-each>
        </section>
    </xsl:template>
</xsl:stylesheet>