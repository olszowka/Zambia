<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/doc/query[@queryName='sessions']/row">
    <div id="sessionBlockDIV_{@sessionid}" class="sessionBlock" sessionid="{@sessionid}" durationUnits="{@durationUnits}" duration="{@duration}">
        <div class="sessionBlockTitleRow">
            <i class="icon-info-sign getSessionInfoP"></i>	
            <!--<div class="ui-icon ui-icon-info getSessionInfoP"></div>-->
            <div class="sessionBlockTitle"><xsl:value-of select="@title" /></div>
        </div>
        <div>
            <span class="sessionBlockId"><xsl:value-of select="@sessionid" /></span>
            <span class="sessionBlockDivis"><xsl:value-of select="@divisionname" /></span>
        </div>
        <div>
            <span class="sessionBlockType"><xsl:value-of select="@typename" /></span>
            <span class="sessionBlockTrack"><xsl:value-of select="@trackname" /></span>
        </div>
    </div>
</xsl:template>
</xsl:stylesheet>
