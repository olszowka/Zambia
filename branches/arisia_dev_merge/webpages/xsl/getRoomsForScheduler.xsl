<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/doc/query[@queryName='rooms']/row">
    <div id="roomidDIV_{@roomid}" class="activeArea checkboxContainer">
        <input type="checkbox" id="roomidCHK_{@roomid}" />
        <span id="roomnameSPN_{@roomid}"><xsl:value-of select="@roomname"/></span>
    </div>
</xsl:template>
</xsl:stylesheet>
