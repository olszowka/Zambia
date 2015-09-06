<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <!--<xsl:include href="xsl/reportInclude.xsl" />-->
    <xsl:template match="/">
        <xsl:apply-templates select="/doc/query[@queryName='sessions']/row" />
    </xsl:template>
    <xsl:template match="query[@queryName='sessions']/row">
        <div class="infolabel">Title:</div>
        <div class="infofield"><xsl:value-of select="@title" /></div>
        <div class="infolabel">Description:</div>
        <div class="infofield"><xsl:value-of select="@progguiddesc" /></div>
        <div>
            <span class="infolabel">Sessionid:</span>
            <span class="infofield"><xsl:value-of select="@sessionid" /></span>
        </div>
        <div>
            <span class="infolabel">Track:</span>
            <span class="infofield"><xsl:value-of select="@trackname" /></span>
        </div>
        <div>
            <span class="infolabel">Type:</span>
            <span class="infofield"><xsl:value-of select="@typename" /></span>
        </div>
        <div>
            <span class="infolabel">Division:</span>
            <span class="infofield"><xsl:value-of select="@divisionname" /></span>
        </div>
        <div>
            <span class="infolabel">Duration:</span>
            <span class="infofield"><xsl:value-of select="@duration" /></span>
        </div>
        <xsl:choose>
            <xsl:when test="@notesforprog">
                <div class="infolabel">Notes for programming:</div>
                <div class="infofield"><xsl:value-of select="@notesforprog" /></div>
            </xsl:when>
            <xsl:otherwise>
                <div class="infolabel">No notes for programming</div>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="@starttime">
                <div class="infolabel">Scheduled:</div>
                <div class="inforow"><xsl:value-of select="@starttime" /> - <xsl:value-of select="@endtime" /></div>
                <div class="inforow">in <xsl:value-of select="@roomname" /></div>
            </xsl:when>
            <xsl:otherwise>
                <div class="infolabel">Not scheduled</div>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:choose>
            <xsl:when test="/doc/query[@queryName='participants']/row">
                <xsl:variable name="modrow" select="/doc/query[@queryName='participants']/row[@moderator='1']" />
                <xsl:choose>
                    <xsl:when test="$modrow">
                        <div class="infolabel">Moderator</div>
                        <div class="inforow"><xsl:value-of select="$modrow/@badgename" /> (<xsl:value-of select="$modrow/@badgeid" />) <xsl:value-of select="$modrow/@participantname" /></div>
                    </xsl:when>
                    <xsl:otherwise>
                        <div class="infolabel">No moderator assigned</div>
                    </xsl:otherwise>
                </xsl:choose>
                <div class="infolabel">Participants</div>
                <xsl:apply-templates select="/doc/query[@queryName='participants']/row[@moderator='0']" />
            </xsl:when>
            <xsl:otherwise>
                <div class="infolabel">No participants assigned</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="query[@queryName='participants']/row">
        <div class="inforow"><xsl:value-of select="@badgename" /> (<xsl:value-of select="@badgeid" />) <xsl:value-of select="@participantname" /></div>
    </xsl:template>
</xsl:stylesheet>
