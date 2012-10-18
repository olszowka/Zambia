<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <xsl:variable name="index" select="/doc/variables/@index" />
        <xsl:variable name="option" select="/doc/variables/@option" /><!-- 'start' or 'end' -->
        <option value="0">
            <xsl:if test="not($index) or $index=0">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
        </option>
        <xsl:choose>
            <xsl:when test="$option='start'">
                <xsl:for-each select="/doc/query[@queryName='times']/row[@avail_start='1']">
                    <xsl:sort select="@timeid" data-type="number" />
                    <xsl:call-template name="optionRow">
                        <xsl:with-param name="timeid" select="@timeid" />
                        <xsl:with-param name="timedisplay" select="@timedisplay" />
                        <xsl:with-param name="index" select="$index" />
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:when>                    
            <xsl:otherwise>
                <xsl:for-each select="/doc/query[@queryName='times']/row[@avail_end='1']">
                    <xsl:sort select="@timeid" data-type="number" />
                    <xsl:call-template name="optionRow">
                        <xsl:with-param name="timeid" select="@timeid" />
                        <xsl:with-param name="timedisplay" select="@timedisplay" />
                        <xsl:with-param name="index" select="$index" />
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="optionRow">
        <xsl:param name="timeid" />
        <xsl:param name="timedisplay" />
        <xsl:param name="index" />
        <option value="{$timeid}">
            <xsl:if test="$timeid = $index">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="$timedisplay" />
        </option>
    </xsl:template>
</xsl:stylesheet>