<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="not(/doc/query[@queryName='searchParticipants']/row)">
                <div class="emptyResult">No matching results found.</div>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="/doc/query[@queryName='searchParticipants']/row">
                    <div class="action" id="actionDIV_{@badgeid}" onmouseover="highlight(true,{@badgeid});" onmouseout="highlight(false,{@badgeid});"
                        onclick="chooseParticipant({@badgeid});">
                        <span class="action" id="lnameSPAN_{@badgeid}"><xsl:value-of select="@lastname"/>, <xsl:value-of select="@firstname"/></span>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <span class="actionB" id="pnameSPAN_{@badgeid}"><xsl:value-of select="@pubsname"/></span>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>                
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        "<span class="action" id="bnameSPAN_{@badgeid}"><xsl:value-of select="@badgename"/></span>"
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        (<span class="action" id="bidSPAN_{@badgeid}"><xsl:value-of select="@badgeid"/></span>)
                        <input type="hidden" id="interestedHID_{@badgeid}">
                            <xsl:attribute name="value"><xsl:value-of select="@interested"/></xsl:attribute>
                        </input>
                        <input type="hidden" id="bioHID_{@badgeid}">
                            <xsl:attribute name="value"><xsl:value-of select="@bio"/></xsl:attribute>
                        </input>
                        <input type="hidden" id="staffnotesHID_{@badgeid}">
                            <xsl:attribute name="value"><xsl:value-of select="@staff_notes"/></xsl:attribute>
                        </input>
                    </div>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>