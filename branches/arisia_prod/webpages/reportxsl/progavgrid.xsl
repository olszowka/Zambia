<?xml version="1.0" encoding="UTF-8" ?>
<!--
	progavgridreport
	Created by Peter Olszowka on 2011-12-20.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <table class="report">
        <tr>
            <th class="report" style="">Time</th>
            <xsl:call-template name="rooms" />
        </tr>
        <xsl:for-each select="/doc/query[@queryName='times']/row">
            <xsl:variable name="starttime" select="@starttime" />
            <tr>
                <td class="report"><xsl:value-of select="@starttimeFMT" /></td>
                <xsl:for-each select="/doc/query[@queryName='rooms']/row">
                    <xsl:variable name="roomid" select="@roomid" />
                    <xsl:variable name="sessionid" select="/doc/query[@queryName='sessions']/row[@roomid=$roomid and @starttime=$starttime]/@sessionid" />
                    <td class="report">
                        <xsl:choose>
                            <xsl:when test="$sessionid">
                                <a href="EditSession.php?id={$sessionid}" title="{/doc/query[@queryName='sessions']/row[@sessionid=$sessionid]/@title}"><xsl:value-of select="$sessionid" /></a>
                                <xsl:text> </xsl:text><span style="color:green"><xsl:value-of select="/doc/query[@queryName='sessions']/row[@sessionid=$sessionid]/@duration" /></span><br />
                                <xsl:call-template name="services">
                                    <xsl:with-param name="session" select="$sessionid" />
                                </xsl:call-template>                          
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                    </td>
                </xsl:for-each>
            </tr>
         </xsl:for-each>   
    </table>
</xsl:template>

<xsl:template name="rooms">
    <xsl:for-each select="/doc/query[@queryName='rooms']/row">
        <th class="report">
            <a href="MaintainRoomSched.php?selroom={@roomid}"><xsl:value-of select="@roomname" /></a>
        </th>
    </xsl:for-each>
</xsl:template>

<xsl:template name="services">
    <xsl:param name="session" />
    <xsl:for-each select="/doc/query[@queryName='services']/row[@sessionid=$session]">
        <xsl:value-of select="@servicename" /><br />
    </xsl:for-each>
</xsl:template>

</xsl:stylesheet>
